<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService {

    // Tạo đơn hàng
    public function createOrder($userId, array $cartItemIds) {
        return DB::transaction(function () use ($userId, $cartItemIds) {
            $cartItems = CartItem::with('course')
                ->whereIn('id', $cartItemIds)
                ->whereHas('cart', fn($q) => $q->where('user_id', $userId))
                ->get();

            // PAY-05: Không tạo order rỗng
            if ($cartItems->isEmpty()) {
                throw new Exception("Giỏ hàng rỗng hoặc không hợp lệ.", 400);
            }

            $order = Order::create([
                'user_id' => $userId,
                'order_code' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'amount' => 0,
                'price_snapshot' => 0
            ]);

            $totalAmount = 0;

            foreach ($cartItems as $item) {
                if ($item->course->status !== 'published') {
                    throw new Exception("Khóa học {$item->course->title} không còn được xuất bản.", 422);
                }

                // PAY-04: Snapshot giá tại thời điểm tạo đơn
                $price = $item->course->sale_price ?? $item->course->price;
                $totalAmount += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course->id,
                    'price' => $price
                ]);
            }

            $order->update(['amount' => $totalAmount, 'price_snapshot' => $totalAmount]);

            // Dọn dẹp giỏ hàng
            CartItem::whereIn('id', $cartItemIds)->delete();

            return $order->load('items.course');
        });
    }

    // Thanh toán Demo (PAY-08: Phải dùng Transaction)
    public function processDemoPayment(Order $order, $method) {
        return DB::transaction(function () use ($order, $method) {

            // PAY-07: Chỉ order pending mới được thanh toán
            if ($order->status !== 'pending' || $order->payment_status === 'paid') {
                throw new Exception("Đơn hàng này không ở trạng thái chờ thanh toán.", 400);
            }

            // Update Order
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now()
            ]);

            // PAY-09: Paid thành công thì tạo enrollment
            $items = OrderItem::where('order_id', $order->id)->get();
            foreach ($items as $item) {
                Enrollment::updateOrCreate(
                    ['user_id' => $order->user_id, 'course_id' => $item->course_id],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'enrolled_at' => now()
                    ]
                );
            }

            // PAY-10: Nếu role là mặc định thì đổi thành student (learner)
            $user = User::find($order->user_id);
            if ($user && !in_array($user->role, ['admin', 'instructor'])) {
                $user->update(['role' => 'student']);
            }

            return $order;
        }); // PAY-11: Tự động rollback nếu có Exception
    }
}
