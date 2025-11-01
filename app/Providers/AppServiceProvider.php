<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\AssignmentRepository;
use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\LessonRepository;
use App\Repositories\OrderRepository;
use App\Repositories\QuizRepository;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use App\Repositories\Interfaces\SessionRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\GoogleCalendarService;
use App\Services\GoogleCloudStorageService;
use App\Services\OAuthService;
use App\Services\StripeService;
use App\Services\Interfaces\FileStorageServiceInterface;
use App\Services\Interfaces\GoogleCalendarServiceInterface;
use App\Services\Interfaces\OAuthServiceInterface;
use App\Services\Interfaces\StripeServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository bindings
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(LessonRepositoryInterface::class, LessonRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(AssignmentRepositoryInterface::class, AssignmentRepository::class);
        $this->app->bind(QuizRepositoryInterface::class, QuizRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);

        // Register Service bindings
        $this->app->bind(GoogleCalendarServiceInterface::class, GoogleCalendarService::class);
        $this->app->bind(FileStorageServiceInterface::class, GoogleCloudStorageService::class);
        $this->app->bind(StripeServiceInterface::class, StripeService::class);
        $this->app->bind(OAuthServiceInterface::class, OAuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
