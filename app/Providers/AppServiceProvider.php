<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\User;
use App\Observers\AssignmentObserver;
use App\Observers\QuizObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        User::observe(UserObserver::class);
        Quiz::observe(QuizObserver::class);
        Assignment::observe(AssignmentObserver::class);
    }
}