<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// SPA Route - Catch all and serve Vue app
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
