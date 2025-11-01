<?php

declare(strict_types=1);

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

// API v1 routes - protected with Sanctum authentication
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth routes will be added in Task 2.7
    // Route::post('/auth/login', [AuthController::class, 'login']);
    // Route::post('/auth/logout', [AuthController::class, 'logout']);
    // Route::get('/auth/user', [AuthController::class, 'user']);
    
    // Resource routes will be added in Phase 2
    // Route::apiResource('courses', CourseController::class);
    // Route::apiResource('sessions', SessionController::class);
    // ... etc
});

