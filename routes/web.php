<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;

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
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        // Route::post('/users/{encryptedId}/approve', [UserController::class, 'approve'])->name('admin.users.approve');
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance');
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs');
        Route::post('/users/{user}/approve', [\App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
    });
    
    // Registrar routes
    Route::prefix('registrar')->name('registrar.')->middleware(['auth', 'check.approval', 'role:registrar'])->group(function () {
        Route::get('/users', [\App\Http\Controllers\Registrar\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\Registrar\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\Registrar\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [\App\Http\Controllers\Registrar\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Registrar\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Registrar\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Registrar\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/approve', [\App\Http\Controllers\Registrar\UserController::class, 'approve'])->name('users.approve');
    });
    
    // Teacher routes
    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher'])->group(function () {
        Route::resource('courses', Teacher\CourseController::class);
        Route::resource('quizzes', Teacher\QuizController::class);
        Route::get('/progress', [Teacher\ProgressController::class, 'index'])->name('progress.index');
        Route::get('/attendance', [Teacher\AttendanceController::class, 'index'])->name('attendance');
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
        Route::get('/dashboard', [Student\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/courses', [Student\CourseController::class, 'index'])->name('courses.index');
        Route::post('/courses/{course}/enroll', [Student\CourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/progress', [Student\ProgressController::class, 'index'])->name('progress');
        Route::get('/attendance', [Student\AttendanceController::class, 'index'])->name('attendance');
    });

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Common routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});