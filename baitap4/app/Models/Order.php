<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $fillable = ['user_id', 'order_code', 'status', 'amount', 'price_snapshot', 'payment_status', 'payment_method', 'paid_at'];
    public function items() { return $this->hasMany(OrderItem::class); }
}
