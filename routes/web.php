<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\PDFController;

// ==================== ADMIN CONTROLLERS ====================
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController; // Fixed: Added App\
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\TodoController as AdminTodoController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Admin\CollegeController as AdminCollegeController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

// ==================== REGISTRAR CONTROLLERS ====================
use App\Http\Controllers\Registrar\UserController as RegistrarUserController;
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboardController;

// ==================== TEACHER CONTROLLERS ====================
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\TopicController as TeacherTopicController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Teacher\TodoController as TeacherTodoController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\EnrollmentController as TeacherEnrollmentController;

// ==================== STUDENT CONTROLLERS ====================
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TopicController as StudentTopicController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\TodoController as StudentTodoController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\CollegeController as StudentCollegeController;
use App\Http\Controllers\Student\ProgramController as StudentProgramController;

// ==================== PUBLIC ROUTES ====================
Route::get('/', function () {
    return view('welcome');
});

// Contact routes
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');

// ==================== PUBLIC API ROUTES FOR REGISTRATION ====================
Route::get('/api/registration/colleges', [AdminCollegeController::class, 'getActiveColleges'])
    ->name('api.registration.colleges');
    
Route::get('/api/registration/colleges/{collegeId}/programs', [AdminCollegeController::class, 'getPrograms'])
    ->name('api.registration.colleges.programs');
    
Route::get('/api/registration/colleges/{id}/years', [AdminCollegeController::class, 'getYears'])
    ->name('api.registration.colleges.years');

// ==================== GUEST ROUTES ====================
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// ==================== EMAIL VERIFICATION ROUTES ====================
Route::get('/email/verify', [VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

Route::get('/email/verify/{encryptedId}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');

// ==================== PROTECTED ROUTES ====================
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/clear-cache', [AdminDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/change-password', [AdminProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.update-password');

        // ============ USER MANAGEMENT ============
        Route::resource('users', AdminUserController::class)->parameters(['users' => 'encryptedId']);
        Route::post('/users/{encryptedId}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{encryptedId}/resend-verification', [AdminUserController::class, 'resendVerification'])->name('users.resend-verification');

        // ============ COURSE MANAGEMENT ============
        Route::resource('courses', AdminCourseController::class)->parameters(['courses' => 'encryptedId']);
        
        // Publish/Unpublish
        Route::patch('/courses/{encryptedId}/publish', [AdminCourseController::class, 'publish'])->name('courses.publish');
        
        // Access management (individual toggle only - NO BULK)
        Route::get('/courses/{encryptedId}/access-modal', [AdminCourseController::class, 'accessModal'])->name('courses.access.modal');
        Route::post('/courses/{encryptedId}/toggle-enrollment', [AdminCourseController::class, 'toggleEnrollment'])->name('courses.toggle-enrollment');
        
        // Topic management
        Route::get('/courses/{encryptedId}/available-topics', [AdminCourseController::class, 'availableTopics'])->name('courses.available-topics');
        Route::post('/courses/{encryptedId}/add-topic', [AdminCourseController::class, 'addTopic'])->name('courses.add-topic');
        Route::post('/courses/{encryptedId}/add-topics', [AdminCourseController::class, 'addTopics'])->name('courses.add-topics');
        Route::post('/courses/{encryptedId}/remove-topic', [AdminCourseController::class, 'removeTopic'])->name('courses.remove-topic');

        // ============ TOPIC MANAGEMENT ============
        Route::resource('topics', AdminTopicController::class)->parameters(['topics' => 'encryptedId']);
        // Topic publish/unpublish
        Route::patch('/topics/{encryptedId}/publish', [AdminTopicController::class, 'publish'])->name('topics.publish');

        // ============ ASSIGNMENT MANAGEMENT ============
        // REMOVED INDEX - redirect to todo
        Route::get('/assignments', function() {
            return redirect()->route('admin.todo.index', ['type' => 'assignment']);
        })->name('assignments.index');
        
        Route::get('/assignments/create', [AdminAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [AdminAssignmentController::class, 'store'])->name('assignments.store');
        // REMOVED show route - now handled by todo
        Route::get('/assignments/{encryptedId}/edit', [AdminAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{encryptedId}', [AdminAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{encryptedId}', [AdminAssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::patch('/assignments/{encryptedId}/publish', [AdminAssignmentController::class, 'togglePublish'])->name('assignments.publish');

        // ============ QUIZ MANAGEMENT ============
        // REMOVED INDEX - redirect to todo
        Route::get('/quizzes', function() {
            return redirect()->route('admin.todo.index', ['type' => 'quiz']);
        })->name('quizzes.index');
        
        Route::get('/quizzes/create', [AdminQuizController::class, 'create'])->name('quizzes.create');
        Route::post('/quizzes', [AdminQuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{encryptedId}', [AdminQuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{encryptedId}/edit', [AdminQuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{encryptedId}', [AdminQuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{encryptedId}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::post('/quizzes/{encryptedId}/toggle-publish', [AdminQuizController::class, 'togglePublish'])->name('quizzes.toggle-publish');

        // ============ COLLEGE MANAGEMENT ============
        Route::get('/colleges', [AdminCollegeController::class, 'index'])->name('colleges.index');
        Route::get('/colleges/create', [AdminCollegeController::class, 'create'])->name('colleges.create');
        Route::post('/colleges', [AdminCollegeController::class, 'store'])->name('colleges.store');
        Route::get('/colleges/{encryptedId}', [AdminCollegeController::class, 'show'])->name('colleges.show');
        Route::get('/colleges/{encryptedId}/edit', [AdminCollegeController::class, 'edit'])->name('colleges.edit');
        Route::put('/colleges/{encryptedId}', [AdminCollegeController::class, 'update'])->name('colleges.update');
        Route::delete('/colleges/{encryptedId}', [AdminCollegeController::class, 'destroy'])->name('colleges.destroy');
        
        // Additional college routes
        Route::get('/colleges/{encryptedId}/students', [AdminCollegeController::class, 'students'])->name('colleges.students');
        Route::get('/colleges/{encryptedId}/available-programs', [AdminCollegeController::class, 'availablePrograms'])->name('colleges.available-programs');
        Route::post('/colleges/{encryptedId}/add-program', [AdminCollegeController::class, 'addProgram'])->name('colleges.add-program');
        Route::post('/colleges/{encryptedId}/add-programs', [AdminCollegeController::class, 'addPrograms'])->name('colleges.add-programs');
        Route::post('/colleges/{encryptedId}/remove-program', [AdminCollegeController::class, 'removeProgram'])->name('colleges.remove-program');

        // ============ PROGRAM MANAGEMENT ============
        Route::get('/programs', [AdminProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [AdminProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [AdminProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{encryptedId}', [AdminProgramController::class, 'show'])->name('programs.show');
        Route::get('/programs/{encryptedId}/edit', [AdminProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{encryptedId}', [AdminProgramController::class, 'update'])->name('programs.update');
        Route::delete('/programs/{encryptedId}', [AdminProgramController::class, 'destroy'])->name('programs.destroy');

        // ============ ENROLLMENT MANAGEMENT ============
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [AdminEnrollmentController::class, 'index'])->name('index');
            Route::get('/students', [AdminEnrollmentController::class, 'getStudents'])->name('students');
            Route::get('/course/{encryptedCourseId}/students', [AdminEnrollmentController::class, 'getEnrolledStudents'])->name('course.students');
            Route::get('/course/{encryptedCourseId}/student-ids', [AdminEnrollmentController::class, 'getEnrolledStudentIds'])->name('student-ids');
            Route::post('/enroll', [AdminEnrollmentController::class, 'enroll'])->name('enroll');
            Route::post('/remove', [AdminEnrollmentController::class, 'remove'])->name('remove');
            Route::get('/programs/{collegeId}', [AdminEnrollmentController::class, 'getProgramsByCollege'])->name('programs'); 
        });

        // ============ TODO MANAGEMENT (UNIFIED DASHBOARD) ============
        Route::prefix('todo')->name('todo.')->group(function () {

            // Main dashboard
            Route::get('/', [AdminTodoController::class, 'index'])->name('index');
            
            // Quiz Access Management
            Route::get('/quiz/{encryptedId}/access', [AdminTodoController::class, 'quizAccess'])->name('quiz.access');
            Route::post('/quiz/{encryptedId}/grant', [AdminTodoController::class, 'grantQuizAccess'])->name('quiz.grant');
            Route::post('/quiz/{encryptedId}/revoke', [AdminTodoController::class, 'revokeQuizAccess'])->name('quiz.revoke');
            Route::post('/quiz/{encryptedId}/toggle/{studentId}', [AdminTodoController::class, 'toggleQuizAccess'])->name('quiz.toggle');
            
            // Assignment Access Management
            Route::get('/assignment/{encryptedId}/access', [AdminTodoController::class, 'assignmentAccess'])->name('assignment.access');
            Route::post('/assignment/{encryptedId}/grant', [AdminTodoController::class, 'grantAssignmentAccess'])->name('assignment.grant');
            Route::post('/assignment/{encryptedId}/revoke', [AdminTodoController::class, 'revokeAssignmentAccess'])->name('assignment.revoke');
            Route::post('/assignment/{encryptedId}/toggle/{studentId}', [AdminTodoController::class, 'toggleAssignmentAccess'])->name('assignment.toggle');
            
            // Assignment Show (Unified page) - NEW ROUTE
            Route::get('/assignment/{encryptedId}/show', [AdminTodoController::class, 'assignmentShow'])->name('assignment.show');
            
            // Assignment Access Modal (AJAX)
            Route::get('/assignment/{encryptedId}/access-modal', [AdminTodoController::class, 'assignmentAccessModal'])->name('assignment.access.modal');
            
            // Progress Tracking (Unified)
            Route::get('/progress', [AdminTodoController::class, 'progress'])->name('progress');
            
            // Submissions
            Route::post('/submission/{submissionId}/grade', [AdminTodoController::class, 'gradeSubmission'])->name('submission.grade');
            
            // AJAX Helpers
            Route::get('/colleges/{collegeId}/programs', [AdminTodoController::class, 'getProgramsByCollege'])->name('colleges.programs');

            // Quiz Show (Unified page) - NEW ROUTE
            Route::get('/quiz/{encryptedId}/show', [AdminTodoController::class, 'quizShow'])->name('quiz.show');
            
            // Quiz Access Modal (AJAX) - NEW ROUTE
            Route::get('/quiz/{encryptedId}/access-modal', [AdminTodoController::class, 'quizAccessModal'])->name('quiz.access.modal');
        });
    });
    
    // ==================== REGISTRAR ROUTES ====================
    Route::prefix('registrar')->name('registrar.')->middleware(['role:registrar'])->group(function () {
        Route::get('/dashboard', [RegistrarDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/clear-cache', [RegistrarDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
        Route::resource('users', RegistrarUserController::class)->parameters(['users' => 'encryptedId']);
        Route::post('/users/{encryptedId}/approve', [RegistrarUserController::class, 'approve'])->name('users.approve');
    });
    
    // ==================== TEACHER ROUTES ====================
    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/clear-cache', [TeacherDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [TeacherProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [TeacherProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');

        // ============ COURSE MANAGEMENT (FULL CRUD) ============
        Route::resource('courses', TeacherCourseController::class)->parameters(['courses' => 'encryptedId']);
        
        // Publish/Unpublish course
        Route::patch('courses/{encryptedId}/publish', [TeacherCourseController::class, 'publish'])->name('courses.publish');
        
        // Access management
        Route::get('courses/{encryptedId}/access-modal', [TeacherCourseController::class, 'accessModal'])->name('courses.access.modal');
        Route::post('courses/{encryptedId}/toggle-enrollment', [TeacherCourseController::class, 'toggleEnrollment'])->name('courses.toggle-enrollment');
        
        // Topic management for courses
        Route::get('courses/{encryptedId}/available-topics', [TeacherCourseController::class, 'availableTopics'])->name('courses.available-topics');
        Route::post('courses/{encryptedId}/add-topic', [TeacherCourseController::class, 'addTopic'])->name('courses.add-topic');
        Route::post('courses/{encryptedId}/add-topics', [TeacherCourseController::class, 'addTopics'])->name('courses.add-topics');
        Route::post('courses/{encryptedId}/remove-topic', [TeacherCourseController::class, 'removeTopic'])->name('courses.remove-topic');

        // ============ TOPIC MANAGEMENT ============
        Route::resource('topics', TeacherTopicController::class)->parameters(['topics' => 'encryptedId']);
        
        // FIX: Add publish route for topics (this was missing)
        Route::patch('topics/{encryptedId}/publish', [TeacherTopicController::class, 'publish'])->name('topics.publish');
        
        // Optional: Add clear cache route for topics
        Route::get('topics/clear-cache', [TeacherTopicController::class, 'clearCache'])->name('topics.clear-cache');

        // ============ QUIZ MANAGEMENT ============
        Route::get('/quizzes', function() {
            return redirect()->route('teacher.todo.index', ['type' => 'quiz']);
        })->name('quizzes.index');
        
        Route::get('/quizzes/create', [TeacherQuizController::class, 'create'])->name('quizzes.create');
        Route::post('/quizzes', [TeacherQuizController::class, 'store'])->name('quizzes.store');
        Route::get('/quizzes/{encryptedId}', [TeacherQuizController::class, 'show'])->name('quizzes.show');
        Route::get('/quizzes/{encryptedId}/edit', [TeacherQuizController::class, 'edit'])->name('quizzes.edit');
        Route::put('/quizzes/{encryptedId}', [TeacherQuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{encryptedId}', [TeacherQuizController::class, 'destroy'])->name('quizzes.destroy');
        Route::post('/quizzes/{encryptedId}/toggle-publish', [TeacherQuizController::class, 'togglePublish'])->name('quizzes.toggle-publish');
        Route::get('/quizzes/{encryptedId}/results', [TeacherQuizController::class, 'results'])->name('quizzes.results');
        
        // ============ ASSIGNMENT MANAGEMENT ============
        Route::get('/assignments', function() {
            return redirect()->route('teacher.todo.index', ['type' => 'assignment']);
        })->name('assignments.index');

        Route::get('/assignments/create', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{encryptedId}/edit', [TeacherAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::patch('/assignments/{encryptedId}/publish', [TeacherAssignmentController::class, 'togglePublish'])->name('assignments.publish');
        
        // ============ PROGRESS & ANALYTICS ============
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('/enrollments', [TeacherCourseController::class, 'enrollments'])->name('enrollments');

        // ============ ENROLLMENT MANAGEMENT ============
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [TeacherEnrollmentController::class, 'index'])->name('index');
            Route::get('/students', [TeacherEnrollmentController::class, 'getStudents'])->name('students');
            Route::get('/course/{encryptedCourseId}/students', [TeacherEnrollmentController::class, 'getEnrolledStudents'])->name('course.students');
            Route::get('/course/{encryptedCourseId}/student-ids', [TeacherEnrollmentController::class, 'getEnrolledStudentIds'])->name('student-ids');
            Route::post('/enroll', [TeacherEnrollmentController::class, 'enroll'])->name('enroll');
            Route::post('/remove', [TeacherEnrollmentController::class, 'remove'])->name('remove');
            Route::get('/programs/{collegeId}', [TeacherEnrollmentController::class, 'getProgramsByCollege'])->name('programs');
        });

        // ============ TEACHER TODO MANAGEMENT ============
        Route::prefix('todo')->name('todo.')->group(function () {
            // Main dashboard
            Route::get('/', [TeacherTodoController::class, 'index'])->name('index');
            
            // Quiz routes - ADD THESE MISSING ROUTES
            Route::get('/quiz/{encryptedId}/show', [TeacherTodoController::class, 'quizShow'])->name('quiz.show');
            Route::get('/quiz/{encryptedId}/access-modal', [TeacherTodoController::class, 'quizAccessModal'])->name('quiz.access.modal');
            
            // Quiz Access Management (you already have these)
            Route::get('/quiz/{encryptedId}/access', [TeacherTodoController::class, 'quizAccess'])->name('quiz.access');
            Route::post('/quiz/{encryptedId}/grant', [TeacherTodoController::class, 'grantQuizAccess'])->name('quiz.grant');
            Route::post('/quiz/{encryptedId}/revoke', [TeacherTodoController::class, 'revokeQuizAccess'])->name('quiz.revoke');
            Route::post('/quiz/{encryptedId}/toggle/{studentId}', [TeacherTodoController::class, 'toggleQuizAccess'])->name('quiz.toggle');
            
            // Assignment Access Management (you already have these)
            Route::get('/assignment/{encryptedId}/access', [TeacherTodoController::class, 'assignmentAccess'])->name('assignment.access');
            Route::post('/assignment/{encryptedId}/grant', [TeacherTodoController::class, 'grantAssignmentAccess'])->name('assignment.grant');
            Route::post('/assignment/{encryptedId}/revoke', [TeacherTodoController::class, 'revokeAssignmentAccess'])->name('assignment.revoke');
            Route::post('/assignment/{encryptedId}/toggle/{studentId}', [TeacherTodoController::class, 'toggleAssignmentAccess'])->name('assignment.toggle');
            
            // Assignment Show (Unified page)
            Route::get('/assignment/{encryptedId}/show', [TeacherTodoController::class, 'assignmentShow'])->name('assignment.show');
            
            // Assignment Access Modal (AJAX)
            Route::get('/assignment/{encryptedId}/access-modal', [TeacherTodoController::class, 'assignmentAccessModal'])->name('assignment.access.modal');
            
            // Progress Tracking (Unified)
            Route::get('/progress', [TeacherTodoController::class, 'progress'])->name('progress');
            
            // Submissions
            Route::get('/submission/{submissionId}', [TeacherTodoController::class, 'viewSubmission'])->name('submission.view');
            Route::post('/submission/{submissionId}/grade', [TeacherTodoController::class, 'gradeSubmission'])->name('submission.grade');
            
            // AJAX Helpers
            Route::get('/colleges/{collegeId}/programs', [TeacherTodoController::class, 'getProgramsByCollege'])->name('colleges.programs');
        });

        // ============ AJAX HELPERS (OUTSIDE TODO) ============
        Route::get('/colleges/{collegeId}/programs', [TeacherCourseController::class, 'getProgramsByCollege'])->name('colleges.programs');
    });
    
    // ==================== STUDENT ROUTES ====================
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/clear-cache', [StudentDashboardController::class, 'clearCache'])->name('dashboard.clear-cache');

        // Profile
        Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
        
        // Courses
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{encryptedId}', [StudentCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{encryptedId}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/courses/{encryptedId}/grades', [StudentCourseController::class, 'grades'])->name('courses.grades');
        
        // Topics
        Route::get('/topics', [StudentTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/{encryptedId}', [StudentTopicController::class, 'show'])->name('topics.show');
        Route::post('/topics/{encryptedId}/complete', [StudentTopicController::class, 'markComplete'])->name('topics.complete');
        
        // ============ STUDENT TODO MANAGEMENT (UNIFIED) ============
        Route::prefix('todo')->name('todo.')->group(function () {
            // Main dashboard - shows both quizzes and assignments
            Route::get('/', [StudentTodoController::class, 'index'])->name('index');
            
            // Quiz routes
            Route::get('/quiz/{encryptedId}', [StudentTodoController::class, 'viewQuiz'])->name('quiz.view');
            Route::get('/quiz/{encryptedId}/take', [StudentTodoController::class, 'takeQuiz'])->name('quiz.take');
            Route::post('/quiz/{encryptedId}/submit', [StudentTodoController::class, 'submitQuiz'])->name('quiz.submit');
            Route::post('/quiz/{encryptedId}/retake', [StudentTodoController::class, 'retakeQuiz'])->name('quiz.retake');
            
            // Assignment routes
            Route::get('/assignment/{encryptedId}', [StudentTodoController::class, 'viewAssignment'])->name('assignment.view');
            Route::post('/assignment/{encryptedId}/submit', [StudentTodoController::class, 'submitAssignment'])->name('assignment.submit');
        });
        
        // Progress & Grades
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
        Route::get('/grades', [StudentProgressController::class, 'grades'])->name('grades.index');
        
        // College and Program routes
        Route::get('/colleges', [StudentCollegeController::class, 'index'])->name('colleges.index');
        Route::get('/colleges/{encryptedId}', [StudentCollegeController::class, 'show'])->name('colleges.show');
        Route::get('/programs', [StudentProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/{encryptedId}', [StudentProgramController::class, 'show'])->name('programs.show');
        
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

// PDF viewing routes
Route::middleware(['auth'])->group(function () {
    Route::get('/pdf/view/{filename}', [PDFController::class, 'view'])->name('pdf.view');
    Route::get('/pdf/download/{filename}', [PDFController::class, 'download'])->name('pdf.download');
});

// ==================== TEST ROUTE (REMOVE IN PRODUCTION) ====================
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