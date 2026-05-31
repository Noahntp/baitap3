<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest {
    public function authorize() { return true; }
    public function rules() {
        return [
            'title' => 'required|string|max:255',
            'lesson_type' => 'required|in:video,text,quiz,assignment',
            'sort_order' => 'integer|min:0', // INS-09: sort_order không được âm.
            'video_url' => 'nullable|url',
            'video_duration_seconds' => 'required_if:lesson_type,video|integer|min:1' // INS-07: Lesson có video phải > 0s.
        ];
    }
}
