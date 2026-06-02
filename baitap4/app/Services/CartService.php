<?php
namespace App\Services;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Enrollment;
use Exception;

class CartService {
    public function addItem($userId, $courseId) {
        $course = Course::findOrFail($courseId);

        // PAY-01: Chỉ mua được course published
        if ($course->status !== 'published') {
            throw new Exception("Khóa học chưa được xuất bản.", 422);
        }

        // PAY-02: Không cho thêm course đã enrolled vào giỏ
        $isEnrolled = Enrollment::where('user_id', $userId)->where('course_id', $courseId)->exists();
        if ($isEnrolled) {
            throw new Exception("Bạn đã sở hữu khóa học này rồi.", 422);
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        // PAY-03: Không cho thêm trùng course trong giỏ
        $existsInCart = CartItem::where('cart_id', $cart->id)->where('course_id', $courseId)->exists();
        if ($existsInCart) {
            throw new Exception("Khóa học đã có trong giỏ hàng.", 422);
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'course_id' => $courseId
        ]);
    }
}
