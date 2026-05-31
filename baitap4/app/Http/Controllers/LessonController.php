<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\CourseSection;
use App\Http\Requests\StoreLessonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LessonController extends Controller {
    public function store(StoreLessonRequest $request, CourseSection $section) {
        // Kiểm tra quyền của Instructor đối với khóa học chứa chương này
        $this->authorize('modify', $section->course);

        $data = $request->validated();
        $data['course_section_id'] = $section->id;
        $data['course_id'] = $section->course_id;

        $lesson = Lesson::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài học thành công',
            'data' => $lesson
        ], 201);
    }

    public function togglePreview(Request $request, Lesson $lesson) {
        $this->authorize('modify', $lesson->course);

        $lesson->update(['is_preview' => !$lesson->is_preview]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật trạng thái học thử.'
        ]);
    }
}
