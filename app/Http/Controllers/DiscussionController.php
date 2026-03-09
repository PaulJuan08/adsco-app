<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DiscussionController extends Controller
{
    private function resolveCourse(string $encryptedId): Course
    {
        $id = Crypt::decrypt(urldecode($encryptedId));
        return Course::with(['teacher', 'teachers'])->findOrFail($id);
    }

    private function isAuthorized(Course $course): bool
    {
        $user = auth()->user();

        if ($user->role === 1) {
            return true;
        }

        if ($user->role === 3) {
            return $course->teacher_id == $user->id
                || $course->teachers()->where('users.id', $user->id)->exists();
        }

        if ($user->role === 4) {
            return Enrollment::where('student_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();
        }

        return false;
    }

    private function layoutFor(int $role): string
    {
        return match ($role) {
            1       => 'admin',
            3       => 'teacher',
            default => 'student',
        };
    }

    // ----------------------------------------------------------------
    // INDEX — list courses accessible to the current user
    // ----------------------------------------------------------------

    public function index()
    {
        $user   = auth()->user();
        $layout = $this->layoutFor($user->role);

        try {
            $query = Course::with(['teacher:id,f_name,l_name'])
                ->withCount([
                    'discussions as thread_count' => fn ($q) => $q->whereNull('parent_id'),
                    'discussions as reply_count'  => fn ($q) => $q->whereNotNull('parent_id'),
                    'discussions as total_count',
                ]);

            if ($user->role === 1) {
                // Admin sees all published courses
                $query->where('is_published', true);
            } elseif ($user->role === 3) {
                // Teacher sees courses they own or are co-assigned to
                $query->where(function ($q) use ($user) {
                    $q->where('teacher_id', $user->id)
                      ->orWhereHas('teachers', fn ($q2) => $q2->where('users.id', $user->id));
                });
            } else {
                // Student sees courses they are actively enrolled in
                $enrolledIds = Enrollment::where('student_id', $user->id)
                    ->where('status', 'active')
                    ->pluck('course_id');
                $query->whereIn('id', $enrolledIds)->where('is_published', true);
            }

            $courses = $query->get()->map(function ($course) {
                // Last activity: most recent message in this course's discussions
                $last = CourseDiscussion::where('course_id', $course->id)
                    ->latest()->first();
                $course->last_activity   = $last?->created_at;
                $course->last_author     = $last?->author;
                $course->encrypted_id    = Crypt::encrypt($course->id);
                return $course;
            })->sortByDesc('last_activity')->values();

            $totalThreads       = $courses->sum('thread_count');
            $totalReplies       = $courses->sum('reply_count');
            $coursesWithActivity = $courses->filter(fn ($c) => $c->total_count > 0)->count();

            return view("{$layout}.discussions.index", compact(
                'courses', 'layout', 'totalThreads', 'totalReplies', 'coursesWithActivity'
            ));

        } catch (\Exception $e) {
            Log::error('Discussion index error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to load discussions.');
        }
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------

    public function show(string $encryptedId)
    {
        try {
            $course = $this->resolveCourse($encryptedId);

            if (!$this->isAuthorized($course)) {
                return redirect()->back()->with('error', 'You are not authorized to view this discussion.');
            }

            $discussions = CourseDiscussion::where('course_id', $course->id)
                ->whereNull('parent_id')
                ->with([
                    'author:id,f_name,l_name,role,sex',
                    'replies.author:id,f_name,l_name,role,sex',
                ])
                ->latest()
                ->get();

            $layout = $this->layoutFor(auth()->user()->role);

            return view("{$layout}.courses.discussions", compact('course', 'encryptedId', 'discussions', 'layout'));

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Discussion show DB error', ['error' => $e->getMessage()]);
            if (str_contains($e->getMessage(), 'course_discussions')) {
                return redirect()->back()->with('error', 'Discussion table not found. Please run the SQL migration first.');
            }
            return redirect()->back()->with('error', 'A database error occurred.');
        } catch (\Exception $e) {
            Log::error('Discussion show error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Course not found or access denied.');
        }
    }

    // ----------------------------------------------------------------
    // STORE
    // ----------------------------------------------------------------

    public function store(Request $request, string $encryptedId)
    {
        $request->validate([
            'body'      => 'required|string|max:5000',
            'parent_id' => 'nullable|integer',
        ]);

        try {
            $course = $this->resolveCourse($encryptedId);

            if (!$this->isAuthorized($course)) {
                return redirect()->back()->with('error', 'Not authorized.');
            }

            if ($request->parent_id) {
                $parent = CourseDiscussion::where('id', $request->parent_id)
                    ->where('course_id', $course->id)
                    ->first();

                if (!$parent) {
                    return redirect()->back()->with('error', 'Invalid reply target.');
                }
            }

            CourseDiscussion::create([
                'course_id' => $course->id,
                'user_id'   => auth()->id(),
                'parent_id' => $request->parent_id ?: null,
                'body'      => $request->body,
            ]);

            return redirect()->back()->with('success', 'Message posted.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Discussion store DB error', ['error' => $e->getMessage()]);
            if (str_contains($e->getMessage(), 'course_discussions')) {
                return redirect()->back()->with('error', 'Discussion table not found. Please run the SQL migration first.');
            }
            return redirect()->back()->with('error', 'A database error occurred while posting.');
        } catch (\Exception $e) {
            Log::error('Discussion store error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to post message.');
        }
    }

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------

    public function destroy(string $encryptedId, int $discussionId)
    {
        try {
            $course     = $this->resolveCourse($encryptedId);
            $discussion = CourseDiscussion::where('course_id', $course->id)
                ->findOrFail($discussionId);

            $user = auth()->user();

            if ($user->role !== 1 && $discussion->user_id !== $user->id) {
                return redirect()->back()->with('error', 'You can only delete your own messages.');
            }

            $discussion->delete();

            return redirect()->back()->with('success', 'Message deleted.');

        } catch (\Exception $e) {
            Log::error('Discussion destroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to delete message.');
        }
    }
}
