<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */


    public function authorize() { return true; }
    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:courses,slug',
            'price' => 'numeric|min:0',
            'level' => 'required|in:beginner,intermediate,advanced'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    
}
