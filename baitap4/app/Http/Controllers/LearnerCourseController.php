<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class LearnerCourseController extends Controller
{
    /**
     * API: Lấy danh sách khóa học mà user (Learner) đã mua
     * Đáp ứng yêu cầu Đề 4: GET /api/my/courses
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Truy vấn qua bảng enrollments (đã cấp quyền) để lấy danh sách khóa học
        // Dùng Eager Loading (with) để tối ưu query, lấy luôn thông tin người dạy
        $enrollments = Enrollment::with(['course'])
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'completed']) // Chỉ lấy khóa đang học hoặc đã xong
            ->orderBy('enrolled_at', 'desc')
            ->get();

        // Chuẩn hóa lại cấu trúc dữ liệu trả ra cho Frontend dễ dùng
        $purchasedCourses = $enrollments->map(function ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'status' => $enrollment->status,
                'progress_percent' => (float) $enrollment->progress_percent,
                'enrolled_at' => $enrollment->enrolled_at,
                'last_accessed_at' => $enrollment->last_accessed_at,
                'course' => $enrollment->course
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khóa học của bạn thành công.',
            'data' => $purchasedCourses
        ]);
    }
}
