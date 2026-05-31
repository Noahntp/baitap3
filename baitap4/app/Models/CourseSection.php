<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model {
    protected $fillable = ['course_id', 'title', 'description', 'sort_order', 'status']; // [cite: 12]

    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function lessons() {
        return $this->hasMany(Lesson::class, 'course_section_id');
    }
}
