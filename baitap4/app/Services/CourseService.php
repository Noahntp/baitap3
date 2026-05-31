<?php
namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Exception;

class CourseService {
    protected $courseRepo;

    public function __construct(CourseRepository $courseRepo) {
        $this->courseRepo = $courseRepo;
    }

    public function createCourse($instructorId, array $data) {
        $data['instructor_id'] = $instructorId;
        $data['status'] = 'draft'; // INS-02: Khóa học mới tạo mặc định draft.
        return $this->courseRepo->create($data);
    }

    public function updateCourse(Course $course, array $data) {
        // INS-03: Chỉ khóa draft hoặc rejected mới được chỉnh sửa.
        if (!in_array($course->status, ['draft', 'rejected'])) {
            throw new Exception("Chỉ khóa nháp hoặc bị từ chối mới được chỉnh sửa.", 403);
        }
        return $this->courseRepo->update($course, $data);
    }

    public function submitForReview(Course $course) {
        if (!in_array($course->status, ['draft', 'rejected'])) {
            throw new Exception("Khóa học không ở trạng thái hợp lệ để gửi duyệt.", 400);
        }

        $course->load('sections.lessons');

        // INS-05: Không cho gửi duyệt nếu chưa có chương.
        if ($course->sections->isEmpty()) {
            throw new Exception("Khóa học chưa có chương nào.", 400);
        }

        $hasPreview = false;
        foreach ($course->sections as $section) {
            // INS-06: Không cho gửi duyệt nếu chương chưa có bài học.
            if ($section->lessons->isEmpty()) {
                throw new Exception("Chương '{$section->title}' chưa có bài học.", 400);
            }
            if ($section->lessons->where('is_preview', 1)->count() > 0) {
                $hasPreview = true;
            }
        }

        // INS-08: Nên có ít nhất 1 bài preview.
        if (!$hasPreview) {
            throw new Exception("Vui lòng set ít nhất 1 bài học làm Preview.", 400);
        }

        // INS-04: Đổi trạng thái từ draft -> pending.
        // INS-10: Instructor không được tự đổi sang published.
        $course->update(['status' => 'pending']);

        return $course;
    }
}
