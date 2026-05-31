<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model {
    protected $fillable = [
        'course_section_id', 'course_id', 'title', 'lesson_type',
        'video_url', 'video_duration_seconds', 'is_preview', 'sort_order'
    ]; // [cite: 13]

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
