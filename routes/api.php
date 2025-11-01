<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API v1 routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Auth routes (public)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// Protected API v1 routes (requires Sanctum authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth routes (protected)
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\Api\UserController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\UserController::class, 'updateProfile']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\UserController::class, 'dashboard']);
        Route::get('/progress', [\App\Http\Controllers\Api\UserController::class, 'progress']);
        Route::get('/achievements', [\App\Http\Controllers\Api\UserController::class, 'achievements']);
    });
    
    // Course routes
    Route::apiResource('courses', \App\Http\Controllers\Api\CourseController::class);
    Route::post('/courses/{course}/enroll', [\App\Http\Controllers\Api\CourseController::class, 'enroll']);
    
    // Session routes
    Route::apiResource('sessions', \App\Http\Controllers\Api\SessionController::class);
    Route::get('/sessions/{session}/join', [\App\Http\Controllers\Api\SessionController::class, 'join']);
    
    // Assignment routes
    Route::apiResource('assignments', \App\Http\Controllers\Api\AssignmentController::class);
    Route::post('/assignments/{assignment}/submit', [\App\Http\Controllers\Api\AssignmentController::class, 'submit']);
    
    // Quiz routes
    Route::apiResource('quizzes', \App\Http\Controllers\Api\QuizController::class);
    Route::post('/quizzes/{quiz}/attempt', [\App\Http\Controllers\Api\QuizController::class, 'attempt']);
    Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\Api\QuizController::class, 'submit']);
    
    // Checkout routes
    Route::prefix('checkout')->group(function () {
        Route::post('/course/{course}', [\App\Http\Controllers\Api\CheckoutController::class, 'start']);
        Route::get('/success', [\App\Http\Controllers\Api\CheckoutController::class, 'success']);
        Route::get('/cancel', [\App\Http\Controllers\Api\CheckoutController::class, 'cancel']);
    });
    
    // Admin routes (admin only)
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\AdminController::class, 'dashboard']);
        Route::get('/users', [\App\Http\Controllers\Api\AdminController::class, 'users']);
        Route::get('/teachers', [\App\Http\Controllers\Api\AdminController::class, 'teachers']);
        Route::get('/students', [\App\Http\Controllers\Api\AdminController::class, 'students']);
        Route::get('/analytics', [\App\Http\Controllers\Api\AdminController::class, 'analytics']);
    });
});

// Public webhook routes (no authentication)
Route::prefix('v1/webhooks')->group(function () {
    Route::post('/stripe', [\App\Http\Controllers\Api\WebhookController::class, 'stripe']);
});

