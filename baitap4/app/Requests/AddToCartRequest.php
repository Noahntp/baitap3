<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'course_id' => 'required|exists:courses,id'
        ];
    }
}
