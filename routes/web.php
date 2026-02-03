<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\AssignmentController as AdminAssignmentController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Registrar\UserController as RegistrarUserController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\TopicController as TeacherTopicController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TopicController as StudentTopicController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Protected routes
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::resource('users', AdminUserController::class)->parameters([
            'users' => 'encryptedId'
        ]);
        
        Route::resource('courses', AdminCourseController::class)->parameters([
            'courses' => 'encryptedId'
        ]);
        
        Route::resource('topics', AdminTopicController::class)->parameters([
            'topics' => 'encryptedId'
        ]);
        
        Route::resource('assignments', AdminAssignmentController::class)->parameters([
            'assignments' => 'encryptedId'
        ]);
        
        // Quiz routes - ONLY submit route
        Route::post('quizzes/{encryptedId}/submit', [AdminQuizController::class, 'submit'])->name('quizzes.submit');
        
        // Resource route (exclude submit since we defined it above)
        Route::resource('quizzes', AdminQuizController::class)->parameters([
            'quizzes' => 'encryptedId'
        ])->except(['submit']);
        
        Route::post('/users/{encryptedId}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        
        // Add these topic management routes for courses
        Route::get('/courses/{encryptedId}/available-topics', [AdminCourseController::class, 'availableTopics'])
            ->name('courses.availableTopics');
        
        Route::post('/courses/{encryptedId}/add-topic', [AdminCourseController::class, 'addTopic'])
            ->name('courses.addTopic');
        
        Route::post('/courses/{encryptedId}/add-topics', [AdminCourseController::class, 'addTopics'])
            ->name('courses.addTopics');
        
        Route::post('/courses/{encryptedId}/remove-topic', [AdminCourseController::class, 'removeTopic'])
            ->name('courses.removeTopic');
    });
    
    // Registrar routes
    Route::prefix('registrar')->name('registrar.')->middleware(['role:registrar'])->group(function () {
        Route::get('/users', [RegistrarUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [RegistrarUserController::class, 'create'])->name('users.create');
        Route::post('/users', [RegistrarUserController::class, 'store'])->name('users.store');
        Route::get('/users/{encryptedId}', [RegistrarUserController::class, 'show'])->name('users.show');
        Route::get('/users/{encryptedId}/edit', [RegistrarUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{encryptedId}', [RegistrarUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{encryptedId}', [RegistrarUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{encryptedId}/approve', [RegistrarUserController::class, 'approve'])->name('users.approve');
    });
    
    // Teacher routes
    Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'check.approval', 'role:teacher'])->group(function () {
        // Course Routes
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TeacherCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{encryptedId}', [TeacherCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{encryptedId}/edit', [TeacherCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{encryptedId}', [TeacherCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{encryptedId}', [TeacherCourseController::class, 'destroy'])->name('courses.destroy');
        
        // Course Topic Management Routes
        Route::get('/courses/{encryptedId}/available-topics', [TeacherCourseController::class, 'availableTopics'])->name('courses.available-topics');
        Route::post('/courses/{encryptedId}/add-topic', [TeacherCourseController::class, 'addTopic'])->name('courses.add-topic');
        Route::post('/courses/{encryptedId}/add-topics', [TeacherCourseController::class, 'addTopics'])->name('courses.add-topics');
        Route::post('/courses/{encryptedId}/remove-topic', [TeacherCourseController::class, 'removeTopic'])->name('courses.remove-topic');

        // Topic Routes
        Route::get('/topics', [TeacherTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/create', [TeacherTopicController::class, 'create'])->name('topics.create');
        Route::post('/topics', [TeacherTopicController::class, 'store'])->name('topics.store');
        Route::get('/topics/{encryptedId}', [TeacherTopicController::class, 'show'])->name('topics.show');
        Route::get('/topics/{encryptedId}/edit', [TeacherTopicController::class, 'edit'])->name('topics.edit');
        Route::put('/topics/{encryptedId}', [TeacherTopicController::class, 'update'])->name('topics.update');
        Route::delete('/topics/{encryptedId}', [TeacherTopicController::class, 'destroy'])->name('topics.destroy');

        // Quiz Routes
        Route::resource('quizzes', TeacherQuizController::class)->parameters([
            'quizzes' => 'encryptedId'
        ]);

        // Additional teacher quiz routes
        Route::post('/quizzes/{encryptedId}/toggle-publish', [TeacherQuizController::class, 'togglePublish'])
            ->name('quizzes.toggle-publish');
        Route::get('/quizzes/{encryptedId}/results', [TeacherQuizController::class, 'results'])
            ->name('quizzes.results');
        Route::get('/quizzes/{encryptedId}/attempts/{attemptId}', [TeacherQuizController::class, 'showAttempt'])
            ->name('quizzes.attempts.show');
        Route::get('/quizzes/{encryptedId}/preview', [TeacherQuizController::class, 'preview'])
            ->name('quizzes.preview');

        // Add take and submit routes (if not already in resource)
        Route::get('/quizzes/{encryptedId}/take', [TeacherQuizController::class, 'take'])
            ->name('quizzes.take');
        Route::post('/quizzes/{encryptedId}/submit', [TeacherQuizController::class, 'submit'])
            ->name('quizzes.submit');
        
        // Assignment Routes
        Route::get('/assignments', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'show'])->name('assignments.show');
        Route::get('/assignments/{encryptedId}/edit', [TeacherAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{encryptedId}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');
        
        // Progress & Analytics
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        
        // Enrollment routes
        Route::get('/enrollments', [TeacherCourseController::class, 'enrollments'])->name('enrollments');
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('courses', StudentCourseController::class)->parameters([
            'courses' => 'encryptedId'
        ]);
        
        Route::get('/topics', [StudentTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/{encryptedId}', [StudentTopicController::class, 'show'])->name('topics.show');
        
        Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{encryptedId}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        
        // Student quiz routes - ONLY submit route
        Route::post('/quizzes/{encryptedId}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        
        Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{encryptedId}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        
        Route::post('/courses/{encryptedId}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress');
        
        // Additional student routes
        Route::get('/timetable', function() {
            return view('student.timetable');
        })->name('timetable');
        Route::get('/grades', function() {
            return view('student.grades');
        })->name('grades');
        
        // Course specific routes
        Route::get('/course/{encryptedId}', [StudentCourseController::class, 'show'])->name('course.show');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});