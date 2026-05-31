<?php
 use App\Http\Controllers\InstructorCourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\LessonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('instructor')->group(function () {
    // API Khóa học
    Route::get('/courses', [InstructorCourseController::class, 'index']);
    Route::post('/courses', [InstructorCourseController::class, 'store']);
    Route::put('/courses/{course}', [InstructorCourseController::class, 'update']);
    Route::delete('/courses/{course}', [InstructorCourseController::class, 'destroy']);
    Route::patch('/courses/{course}/submit-review', [InstructorCourseController::class, 'submitReview']);

    // API Chương (Sections/Chapters)
    Route::post('/courses/{course}/chapters', [CourseSectionController::class, 'store']);
    Route::put('/chapters/{section}', [CourseSectionController::class, 'update']);

    // API Bài học (Lessons)
    Route::post('/chapters/{section}/lessons', [LessonController::class, 'store']);
    Route::put('/lessons/{lesson}', [LessonController::class, 'update']);
    Route::patch('/lessons/{lesson}/preview', [LessonController::class, 'togglePreview']);
});
