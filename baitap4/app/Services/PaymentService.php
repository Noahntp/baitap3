<?php
namespace App\Services;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;
class PaymentService
{
    public function processDemoPayment($userId, $orderId, $method)
    {
        return DB::transaction(function () use ($userId, $orderId, $method) {
            $order = Order::with('orderItems')->where('id', $orderId)->where('user_id', $userId)->firstOrFail();

            // PAY-07: Chỉ order pending mới được thanh toán
            if ($order->status !== 'pending' || $order->payment_status === 'paid') {
                throw new Exception("Đơn hàng này không ở trạng thái chờ thanh toán.", 400);
            }

            // 1. Ghi nhận thanh toán
            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $method,
                'amount' => $order->amount,
                'status' => 'success',
                'paid_at' => now()
            ]);

            // 2. Cập nhật Order
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $method,
                'paid_at' => now()
            ]);

            // 3. PAY-09: Tạo Enrollment cho từng khóa học trong đơn
            foreach ($order->orderItems as $item) {
                Enrollment::updateOrCreate(
                    ['user_id' => $userId, 'course_id' => $item->course_id],
                    [
                        'order_id' => $order->id,
                        'status' => 'active',
                        'enrolled_at' => now()
                    ]
                );
            }

            // 4. PAY-10: Update User Role (Từ user thường lên học viên)
            $user = User::find($userId);
            if ($user->role !== 'admin' && $user->role !== 'instructor') {
                // Đảm bảo user có role để học
                $user->update(['role' => 'student']);
            }

            return $order;
        }); // PAY-11: Rollback toàn bộ nếu có lỗi
    }
}
