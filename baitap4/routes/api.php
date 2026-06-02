<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import các Controller của Đề 3 (Instructor)
use App\Http\Controllers\InstructorCourseController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\LessonController;

// Import các Controller của Đề 4 (Member / Learner)
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LearnerCourseController;

/*
|--------------------------------------------------------------------------
| NHÓM 1: DÀNH CHO GIẢNG VIÊN (Có thêm chữ /instructor trên URL)
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| NHÓM 2: DÀNH CHO HỌC VIÊN / MEMBER (Không có prefix instructor)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {
    // API Giỏ hàng (Cart)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::delete('/cart/items/{cartItemId}', [CartController::class, 'destroy']);

    // API Đơn hàng & Thanh toán (Order & Payment)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/my/orders', [OrderController::class, 'index']);
    Route::get('/my/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/pay-demo', [OrderController::class, 'payDemo']);

    // API Xem khóa học đã mua (Learner)
    Route::get('/my/courses', [LearnerCourseController::class, 'index']);
});
