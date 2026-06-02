<?php
namespace App\Http\Controllers;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseSectionController extends Controller
{
    // Tạo chương mới cho một khóa học
    public function store(Request $request, Course $course) {
        Gate::authorize('modify', $course);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0'
        ]);

        $data['course_id'] = $course->id;
        $data['status'] = 'active';

        $section = CourseSection::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Tạo chương học thành công',
            'data' => $section
        ], 201);
    }

    // Cập nhật tên/thứ tự của chương
    public function update(Request $request, CourseSection $section) {
        // Dò ngược từ Section -> Course để check quyền owner
        Gate::authorize('modify', $section->course);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer|min:0'
        ]);

        $section->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật chương học thành công',
            'data' => $section
        ]);
    }
}
