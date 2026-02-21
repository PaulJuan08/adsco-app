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
use App\Http\Controllers\Registrar\UserController as RegistrarUserController;
use App\Http\Controllers\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Teacher\TopicController as TeacherTopicController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\QuizController as TeacherQuizController;
use App\Http\Controllers\Teacher\ProgressController as TeacherProgressController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\TopicController as StudentTopicController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\QuizController as StudentQuizController;
use App\Http\Controllers\Student\ProgressController as StudentProgressController;
use App\Http\Controllers\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Auth\VerificationController; // Add this

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Contact routes - MUST be before any other POST routes
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
Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])
    ->middleware('auth')
    ->name('verification.notice');

// Use encrypted ID route (REMOVE the standard id route)
Route::get('/email/verify/{encryptedId}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])
    ->middleware(['signed']) // Note: 'auth' middleware is removed because the user might not be logged in when clicking the link
    ->name('verification.verify');

Route::post('/email/verification-notification', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.resend');


// TEST EMAIL ROUTE - Remove after testing
Route::get('/test-email', function() {
    try {
        // Log the current mail configuration
        \Log::info('Testing mail configuration:', [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from_address' => config('mail.from.address'),
        ]);

        // Send a test email
        \Mail::raw('This is a test email from ADSCO LMS using Brevo!', function ($message) {
            $message->to('elishaphatpauljuan@gmail.com') // Send to yourself for testing
                    ->subject('Brevo Test Email - ' . now());
        });
        
        return '✅ Test email sent! Check your inbox at elishaphatpauljuan@gmail.com';
    } catch (\Exception $e) {
        \Log::error('Test email failed: ' . $e->getMessage());
        return '❌ Error: ' . $e->getMessage();
    }
});


// Protected routes
Route::middleware(['auth', 'check.approval'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard routes - This handles all roles (admin, registrar, teacher, student)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {

        // Admin Profile Routes
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'showChangePasswordForm'])->name('profile.change-password');
        Route::put('/profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

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

        // College Management
        Route::resource('colleges', App\Http\Controllers\Admin\CollegeController::class)->parameters([
            'colleges' => 'encryptedId'
        ]);
        
        Route::get('/colleges/{id}/years', [App\Http\Controllers\Admin\CollegeController::class, 'getYears'])
            ->name('colleges.years');

        Route::get('/colleges/{encryptedId}/students', [App\Http\Controllers\Admin\CollegeController::class, 'students'])
            ->name('admin.colleges.students');
        
        // Program Management
        Route::resource('programs', App\Http\Controllers\Admin\ProgramController::class)->parameters([
            'programs' => 'encryptedId'
        ]);

        // Program Student Management
        Route::get('/programs/{encryptedId}/students/search',
            [AdminProgramController::class, 'searchStudents'])
            ->name('programs.students.search');

        Route::post('/programs/{encryptedId}/students/assign',
            [AdminProgramController::class, 'assignStudent'])
            ->name('programs.students.assign');

        Route::delete('/programs/{encryptedId}/students/unassign',
            [AdminProgramController::class, 'unassignStudent'])
            ->name('programs.students.unassign');

        Route::post('/users/{encryptedId}/resend-verification', [AdminUserController::class, 'resendVerification'])
            ->name('admin.users.resend-verification');
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

        // Teacher Profile Routes 
        Route::get('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');

        // Course Routes
        Route::get('/courses', [TeacherCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/create', [TeacherCourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [TeacherCourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{encryptedId}', [TeacherCourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{encryptedId}/edit', [TeacherCourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{encryptedId}', [TeacherCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{encryptedId}', [TeacherCourseController::class, 'destroy'])->name('courses.destroy');
        Route::get('/courses/available', [StudentCourseController::class, 'available'])->name('courses.available');
        
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
        
        // Student Profile Routes
        Route::get('/profile', [App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
        
        // Courses
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{encryptedId}', [StudentCourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{encryptedId}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
        Route::get('/courses/{encryptedId}/topics', [StudentCourseController::class, 'topics'])->name('courses.topics');
        Route::get('/courses/{encryptedId}/materials', [StudentCourseController::class, 'materials'])->name('courses.materials');
        Route::get('/courses/{encryptedId}/grades', [StudentCourseController::class, 'grades'])->name('courses.grades');
        
        // Topics
        Route::get('/topics', [StudentTopicController::class, 'index'])->name('topics.index');
        Route::get('/topics/{encryptedId}', [StudentTopicController::class, 'show'])->name('topics.show');
        Route::post('/topics/{encryptedId}/complete', [StudentTopicController::class, 'markComplete'])->name('topics.complete');
        Route::post('/topics/{encryptedId}/incomplete', [StudentTopicController::class, 'markIncomplete'])->name('topics.incomplete');
        Route::post('/topics/{encryptedId}/notes', [StudentTopicController::class, 'saveNotes'])->name('topics.notes');
        
        // Quiz routes
        Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes.index');
        Route::get('/quizzes/{encryptedId}', [StudentQuizController::class, 'show'])->name('quizzes.show');
        Route::post('/quizzes/{encryptedId}/submit', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
        Route::post('/quizzes/{encryptedId}/retake', [StudentQuizController::class, 'retake'])->name('quizzes.retake');
        
        // Clear results (for modal)
        Route::post('/quizzes/clear-results', function() {
            session()->forget('quiz_results');
            return response()->json(['success' => true]);
        })->name('quizzes.clear-results');
        
        // Assignments
        Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{encryptedId}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{encryptedId}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
        Route::get('/assignments/{encryptedId}/view', [StudentAssignmentController::class, 'viewSubmission'])->name('assignments.view');
        
        // Progress & Grades
        Route::get('/progress', [StudentProgressController::class, 'index'])->name('progress.index');
        Route::get('/grades', [StudentProgressController::class, 'grades'])->name('grades.index');
        
        // College and Program routes
        Route::get('/colleges', [App\Http\Controllers\Student\CollegeController::class, 'index'])->name('colleges.index');
        Route::get('/colleges/{encryptedId}', [App\Http\Controllers\Student\CollegeController::class, 'show'])->name('colleges.show');
        Route::get('/colleges/{collegeId}/programs', [App\Http\Controllers\Student\CollegeController::class, 'getPrograms'])->name('colleges.programs');
        
        // Programs
        Route::get('/programs', [App\Http\Controllers\Student\ProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/{encryptedId}', [App\Http\Controllers\Student\ProgramController::class, 'show'])->name('programs.show');
        
        // Additional student routes
        Route::get('/timetable', function() {
            return view('student.timetable');
        })->name('timetable');
        
        Route::get('/attendance', function() {
            return view('student.attendance');
        })->name('attendance');
        
        Route::get('/calendar', function() {
            return view('student.calendar');
        })->name('calendar');
        
        Route::get('/notifications', function() {
            return view('student.notifications');
        })->name('notifications');
        
        Route::get('/settings', function() {
            return view('student.settings');
        })->name('settings');
        
        // TEST ROUTE
        Route::get('/test-route', function() {
            return response()->json(['message' => 'Student routes are working!']);
        })->name('test.route');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});