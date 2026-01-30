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
        Route::post('quizzes/{encryptedId}/submit', [AdminQuizController::class, 'submit'])->name('admin.quizzes.submit');
        
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
    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher'])->group(function () {
        // Main resource routes
        Route::resource('courses', TeacherCourseController::class)->parameters([
            'courses' => 'encryptedId'
        ]);
        
        Route::resource('topics', TeacherTopicController::class)->parameters([
            'topics' => 'encryptedId'
        ]);
        
        Route::resource('assignments', TeacherAssignmentController::class)->parameters([
            'assignments' => 'encryptedId'
        ]);
        
        // Quiz routes for teacher - ONLY submit route
        Route::post('quizzes/{encryptedId}/submit', [TeacherQuizController::class, 'submit'])->name('teacher.quizzes.submit');
        
        Route::resource('quizzes', TeacherQuizController::class)->parameters([
            'quizzes' => 'encryptedId'
        ])->except(['submit']);
        
        // Progress routes
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        
        // Enrollments route
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
        Route::post('/quizzes/{encryptedId}/submit', [StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
        
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