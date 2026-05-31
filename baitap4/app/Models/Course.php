<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model {
    protected $fillable = [
        'instructor_id', 'title', 'slug', 'short_description', 'description',
        'price', 'level', 'status'
    ]; // [cite: 9]

    public function sections() {
        return $this->hasMany(CourseSection::class, 'course_id');
    }
}
