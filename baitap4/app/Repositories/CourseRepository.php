<?php
namespace App\Repositories;
use App\Models\Course;

class CourseRepository {
    public function getCoursesByInstructor($instructorId) {
        return Course::where('instructor_id', $instructorId)->latest()->paginate(10);
    }

    public function create(array $data) {
        return Course::create($data);
    }

    public function update(Course $course, array $data) {
        $course->update($data);
        return $course;
    }
}
