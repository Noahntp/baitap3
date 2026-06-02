<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'cart_item_ids' => 'required|array|min:1',
            'cart_item_ids.*' => 'exists:cart_items,id'
        ];
    }
}
