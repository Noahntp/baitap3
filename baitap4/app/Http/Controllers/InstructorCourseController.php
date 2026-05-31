<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Services\CourseService;
use App\Repositories\CourseRepository;
use Illuminate\Http\Request;

class InstructorCourseController extends Controller {
    protected $courseService;
    protected $courseRepo;

    public function __construct(CourseService $courseService, CourseRepository $courseRepo) {
        $this->courseService = $courseService;
        $this->courseRepo = $courseRepo;
    }

    public function index(Request $request) {
        $courses = $this->courseRepo->getCoursesByInstructor($request->user()->id);
        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khóa học thành công',
            'data' => $courses
        ]); // [cite: 34, 35, 36, 37]
    }

    public function store(StoreCourseRequest $request) {
        $course = $this->courseService->createCourse($request->user()->id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Tạo khóa học thành công',
            'data' => $course
        ], 201);
    }

    public function submitReview(Request $request, Course $course) {
        $this->authorize('modify', $course); // Kích hoạt Policy INS-01

        try {
            $this->courseService->submitForReview($course);
            return response()->json([
                'success' => true,
                'message' => 'Đã gửi duyệt khóa học thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}
