<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Registrar\UserController as RegistrarUserController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;

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
        // Update resource routes to use encryptedId
        Route::resource('users', AdminUserController::class)->parameters([
            'users' => 'encryptedId'
        ]);
        
        Route::resource('courses', AdminCourseController::class);
        Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance');
        Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs');
        Route::post('/users/{encryptedId}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    });
    
    // Registrar routes - UPDATED to use {encryptedId}
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
        Route::resource('courses', TeacherCourseController::class);
        Route::resource('quizzes', TeacherQuizController::class);
        Route::get('/progress', [TeacherProgressController::class, 'index'])->name('progress.index');
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance');
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress');
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');
    });

    // Profile routes - REMOVED DUPLICATES
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});