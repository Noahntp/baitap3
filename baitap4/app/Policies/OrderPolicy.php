<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Order;

class OrderPolicy {
    // PAY-06: User chỉ xem/thanh toán order của chính mình
    public function viewOrPay(User $user, Order $order) {
        return $user->id === $order->user_id;
    }
}
