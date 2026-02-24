<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\TodoController as AdminTodoController;
use App\Http\Controllers\Registrar\UserController as RegistrarUserController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\TopicController as TeacherTopicController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Teacher\TodoController as TeacherTodoController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TopicController as StudentTopicController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\TodoController as StudentTodoController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Auth\VerificationController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Contact routes
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');

// ===== PUBLIC API ROUTES FOR REGISTRATION =====
Route::get('/api/registration/colleges', [App\Http\Controllers\Admin\CollegeController::class, 'getActiveColleges'])
    ->name('api.registration.colleges');
    
Route::get('/api/registration/colleges/{collegeId}/programs', [App\Http\Controllers\Admin\CollegeController::class, 'getPrograms'])
    ->name('api.registration.colleges.programs');
    
Route::get('/api/registration/colleges/{id}/years', [App\Http\Controllers\Admin\CollegeController::class, 'getYears'])
    ->name('api.registration.colleges.years');

// Guest routes
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ============ EMAIL VERIFICATION ROUTES ============
Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{encryptedId}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// Protected routes
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        
        // Cache Clearing Route
        Route::post('/dashboard/clear-cache', [App\Http\Controllers\Admin\DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

        // User Management
        Route::resource('users', AdminUserController::class)->parameters(['users' => 'encryptedId']);
        Route::post('/users/{encryptedId}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{encryptedId}/resend-verification', [AdminUserController::class, 'resendVerification'])->name('users.resend-verification');

        // Course Management
        Route::resource('courses', AdminCourseController::class)->parameters(['courses' => 'encryptedId']);
        Route::get('/courses/{encryptedId}/available-topics', [AdminCourseController::class, 'availableTopics'])->name('courses.availableTopics');
        Route::post('/courses/{encryptedId}/add-topic', [AdminCourseController::class, 'addTopic'])->name('courses.addTopic');
        Route::post('/courses/{encryptedId}/add-topics', [AdminCourseController::class, 'addTopics'])->name('courses.addTopics');
        Route::post('/courses/{encryptedId}/remove-topic', [AdminCourseController::class, 'removeTopic'])->name('courses.removeTopic');

        // Topic Management
        Route::resource('topics', AdminTopicController::class)->parameters(['topics' => 'encryptedId']);

        // Assignment Management
        Route::resource('assignments', AdminAssignmentController::class)->parameters(['assignments' => 'encryptedId']);
        Route::patch('/assignments/{encryptedId}/publish', [AdminAssignmentController::class, 'togglePublish'])->name('assignments.publish');

        // Quiz Management
        Route::post('quizzes/{encryptedId}/submit', [AdminQuizController::class, 'submit'])->name('quizzes.submit');
        Route::resource('quizzes', AdminQuizController::class)->parameters(['quizzes' => 'encryptedId'])->except(['submit']);

        // College Management - FIXED: Use explicit routes instead of resource
        Route::get('/colleges', [App\Http\Controllers\Admin\CollegeController::class, 'index'])->name('colleges.index');
        Route::get('/colleges/create', [App\Http\Controllers\Admin\CollegeController::class, 'create'])->name('colleges.create');
        Route::post('/colleges', [App\Http\Controllers\Admin\CollegeController::class, 'store'])->name('colleges.store');
        Route::get('/colleges/{encryptedId}', [App\Http\Controllers\Admin\CollegeController::class, 'show'])->name('colleges.show');
        Route::get('/colleges/{encryptedId}/edit', [App\Http\Controllers\Admin\CollegeController::class, 'edit'])->name('colleges.edit');
        Route::put('/colleges/{encryptedId}', [App\Http\Controllers\Admin\CollegeController::class, 'update'])->name('colleges.update');
        Route::delete('/colleges/{encryptedId}', [App\Http\Controllers\Admin\CollegeController::class, 'destroy'])->name('colleges.destroy');
        
        // Additional college routes
        Route::get('/colleges/{encryptedId}/students', [App\Http\Controllers\Admin\CollegeController::class, 'students'])->name('colleges.students');
        Route::get('/colleges/{encryptedId}/available-programs', [App\Http\Controllers\Admin\CollegeController::class, 'availablePrograms'])->name('colleges.available-programs');
        Route::post('/colleges/{encryptedId}/add-program', [App\Http\Controllers\Admin\CollegeController::class, 'addProgram'])->name('colleges.add-program');
        Route::post('/colleges/{encryptedId}/add-programs', [App\Http\Controllers\Admin\CollegeController::class, 'addPrograms'])->name('colleges.add-programs');
        Route::post('/colleges/{encryptedId}/remove-program', [App\Http\Controllers\Admin\CollegeController::class, 'removeProgram'])->name('colleges.remove-program');

        // Program Management - FIXED: Use explicit routes instead of resource
        Route::get('/programs', [AdminProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [AdminProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [AdminProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{encryptedId}', [AdminProgramController::class, 'show'])->name('programs.show');
        Route::get('/programs/{encryptedId}/edit', [AdminProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{encryptedId}', [AdminProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{encryptedId}', [AdminProgramController::class, 'destroy'])->name('programs.destroy');

        // ============ ENROLLMENT MANAGEMENT ============
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\EnrollmentController::class, 'index'])->name('index');
            Route::get('/students', [App\Http\Controllers\Admin\EnrollmentController::class, 'getStudents'])->name('students');
            Route::get('/course/{encryptedCourseId}/students', [App\Http\Controllers\Admin\EnrollmentController::class, 'getEnrolledStudents'])->name('course.students');
            Route::get('/course/{encryptedCourseId}/student-ids', [App\Http\Controllers\Admin\EnrollmentController::class, 'getEnrolledStudentIds'])->name('student-ids');
            Route::post('/enroll', [App\Http\Controllers\Admin\EnrollmentController::class, 'enroll'])->name('enroll');
            Route::post('/remove', [App\Http\Controllers\Admin\EnrollmentController::class, 'remove'])->name('remove');
            Route::get('/programs/{collegeId}', [App\Http\Controllers\Admin\EnrollmentController::class, 'getProgramsByCollege'])->name('programs');
        });

        // ============ TODO MANAGEMENT ============
        Route::get('/todo', [AdminTodoController::class, 'index'])->name('todo.index');
        
        // Quiz Access Management
        Route::get('/todo/quiz/{encryptedId}/access', [AdminTodoController::class, 'quizAccess'])->name('todo.quiz.access');
        Route::post('/todo/quiz/{encryptedId}/grant', [AdminTodoController::class, 'grantQuizAccess'])->name('todo.quiz.grant');
        Route::post('/todo/quiz/{encryptedId}/revoke', [AdminTodoController::class, 'revokeQuizAccess'])->name('todo.quiz.revoke');
        Route::post('/todo/quiz/{encryptedId}/toggle/{studentId}', [AdminTodoController::class, 'toggleQuizAccess'])->name('todo.quiz.toggle');
        
        // Assignment Access Management
        Route::get('/todo/assignment/{encryptedId}/access', [AdminTodoController::class, 'assignmentAccess'])->name('todo.assignment.access');
        Route::post('/todo/assignment/{encryptedId}/grant', [AdminTodoController::class, 'grantAssignmentAccess'])->name('todo.assignment.grant');
        Route::post('/todo/assignment/{encryptedId}/revoke', [AdminTodoController::class, 'revokeAssignmentAccess'])->name('todo.assignment.revoke');
        Route::post('/todo/assignment/{encryptedId}/toggle/{studentId}', [AdminTodoController::class, 'toggleAssignmentAccess'])->name('todo.assignment.toggle');
        
        // Progress Tracking
        Route::get('/todo/progress', [AdminTodoController::class, 'progress'])->name('todo.progress');
        Route::get('/todo/submission/{submissionId}', [AdminTodoController::class, 'viewSubmission'])->name('todo.submission.view');
        Route::post('/todo/submission/{submissionId}/grade', [AdminTodoController::class, 'gradeSubmission'])->name('todo.submission.grade');
        
        // AJAX Helper
        Route::get('/todo/colleges/{collegeId}/programs', [AdminTodoController::class, 'getProgramsByCollege'])->name('todo.colleges.programs');
    });
    
    // ==================== REGISTRAR ROUTES ====================
    Route::prefix('registrar')->name('registrar.')->middleware(['role:registrar'])->group(function () {

        // Cache Clearing Route
        Route::post('/dashboard/clear-cache', [App\Http\Controllers\Registrar\DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        Route::resource('users', RegistrarUserController::class)->parameters(['users' => 'encryptedId']);
        Route::post('/users/{encryptedId}/approve', [RegistrarUserController::class, 'approve'])->name('users.approve');
    });
    
    // ==================== TEACHER ROUTES ====================
    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher'])->group(function () {

        // Cache Clearing Route
        Route::post('/dashboard/clear-cache', [App\Http\Controllers\Teacher\DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');

        // Course Management
        Route::resource('courses', TeacherCourseController::class)->parameters(['courses' => 'encryptedId']);
        Route::get('/courses/{encryptedId}/available-topics', [TeacherCourseController::class, 'availableTopics'])->name('courses.available-topics');
        Route::post('/courses/{encryptedId}/add-topic', [TeacherCourseController::class, 'addTopic'])->name('courses.add-topic');
        Route::post('/courses/{encryptedId}/add-topics', [TeacherCourseController::class, 'addTopics'])->name('courses.add-topics');
        Route::post('/courses/{encryptedId}/remove-topic', [TeacherCourseController::class, 'removeTopic'])->name('courses.remove-topic');

        // Topic Management
        Route::resource('topics', TeacherTopicController::class)->parameters(['topics' => 'encryptedId']);

        // Quiz Management
        Route::resource('quizzes', TeacherQuizController::class)->parameters(['quizzes' => 'encryptedId']);
        Route::post('/quizzes/{encryptedId}/toggle-publish', [TeacherQuizController::class, 'togglePublish'])->name('quizzes.toggle-publish');
        Route::get('/quizzes/{encryptedId}/results', [TeacherQuizController::class, 'results'])->name('quizzes.results');
        
        // Assignment Management
        Route::resource('assignments', TeacherAssignmentController::class)->parameters(['assignments' => 'encryptedId']);
        
        // Progress & Analytics
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('/enrollments', [TeacherCourseController::class, 'enrollments'])->name('enrollments');

        // ============ TEACHER TODO MANAGEMENT ============
        Route::get('/todo', [TeacherTodoController::class, 'index'])->name('todo.index');
        
        // Quiz Access Management
        Route::get('/todo/quiz/{encryptedId}/access', [TeacherTodoController::class, 'quizAccess'])->name('todo.quiz.access');
        Route::post('/todo/quiz/{encryptedId}/grant', [TeacherTodoController::class, 'grantQuizAccess'])->name('todo.quiz.grant');
        Route::post('/todo/quiz/{encryptedId}/revoke', [TeacherTodoController::class, 'revokeQuizAccess'])->name('todo.quiz.revoke');
        Route::post('/todo/quiz/{encryptedId}/toggle/{studentId}', [TeacherTodoController::class, 'toggleQuizAccess'])->name('todo.quiz.toggle');
        
        // Assignment Access Management
        Route::get('/todo/assignment/{encryptedId}/access', [TeacherTodoController::class, 'assignmentAccess'])->name('todo.assignment.access');
        Route::post('/todo/assignment/{encryptedId}/grant', [TeacherTodoController::class, 'grantAssignmentAccess'])->name('todo.assignment.grant');
        Route::post('/todo/assignment/{encryptedId}/revoke', [TeacherTodoController::class, 'revokeAssignmentAccess'])->name('todo.assignment.revoke');
        Route::post('/todo/assignment/{encryptedId}/toggle/{studentId}', [TeacherTodoController::class, 'toggleAssignmentAccess'])->name('todo.assignment.toggle');
        
        // Progress Tracking
        Route::get('/todo/progress', [TeacherTodoController::class, 'progress'])->name('todo.progress');
        Route::get('/todo/submission/{submissionId}', [TeacherTodoController::class, 'viewSubmission'])->name('todo.submission.view');
        Route::post('/todo/submission/{submissionId}/grade', [TeacherTodoController::class, 'gradeSubmission'])->name('todo.submission.grade');
        
        // AJAX Helper
        Route::get('/todo/colleges/{collegeId}/programs', [TeacherTodoController::class, 'getProgramsByCollege'])->name('todo.colleges.programs');

    });
    
    // ==================== STUDENT ROUTES ====================
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {

        // Cache Clearing Route
        Route::post('/dashboard/clear-cache', [App\Http\Controllers\Student\DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        
        // Courses
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{encryptedId}', [StudentCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{encryptedId}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/courses/{encryptedId}/grades', [StudentCourseController::class, 'grades'])->name('courses.grades');
        
        // Topics
        Route::get('/topics', [StudentTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/{encryptedId}', [StudentTopicController::class, 'show'])->name('topics.show');
        Route::post('/topics/{encryptedId}/complete', [StudentTopicController::class, 'markComplete'])->name('topics.complete');
        
        // ============ TODO (UNIFIED INTERFACE) ============
        Route::get('/todo', [StudentTodoController::class, 'index'])->name('todo.index');
        
        // Quiz routes (through Todo)
        Route::get('/todo/quiz/{encryptedId}/take', [StudentTodoController::class, 'takeQuiz'])->name('todo.quiz.take');
        Route::post('/todo/quiz/{encryptedId}/submit', [StudentTodoController::class, 'submitQuiz'])->name('todo.quiz.submit');
        Route::post('/todo/quiz/{encryptedId}/retake', [StudentTodoController::class, 'retakeQuiz'])->name('todo.quiz.retake');
        
        // Assignment routes (through Todo)
        Route::get('/todo/assignment/{encryptedId}', [StudentTodoController::class, 'viewAssignment'])->name('todo.assignment.view');
        Route::post('/todo/assignment/{encryptedId}/submit', [StudentTodoController::class, 'submitAssignment'])->name('todo.assignment.submit');
        
        // Progress & Grades
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
        Route::get('/grades', [StudentProgressController::class, 'grades'])->name('grades.index');
        
        // College and Program routes
        Route::get('/colleges', [App\Http\Controllers\Student\CollegeController::class, 'index'])->name('colleges.index');
        Route::get('/colleges/{encryptedId}', [App\Http\Controllers\Student\CollegeController::class, 'show'])->name('colleges.show');
        Route::get('/programs', [App\Http\Controllers\Student\ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/{encryptedId}', [App\Http\Controllers\Student\ProgramController::class, 'show'])->name('programs.show');
        
        // Utility routes
        Route::get('/timetable', fn() => view('student.timetable'))->name('timetable');
        Route::get('/attendance', fn() => view('student.attendance'))->name('attendance');
        Route::get('/calendar', fn() => view('student.calendar'))->name('calendar');
        Route::get('/notifications', fn() => view('student.notifications'))->name('notifications');
        Route::get('/settings', fn() => view('student.settings'))->name('settings');
    });

    // Profile routes (common)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-encrypt/{id}', function($id) {
    $encrypted = Crypt::encrypt($id);
    $decrypted = Crypt::decrypt($encrypted);
    
    return [
        'original_id' => $id,
        'encrypted' => $encrypted,
        'decrypted' => $decrypted,
        'matches' => $id == $decrypted,
        'route_to_college' => route('admin.colleges.show', ['encryptedId' => $encrypted]),
        'route_to_program' => route('admin.programs.show', ['encryptedId' => $encrypted]),
    ];
});