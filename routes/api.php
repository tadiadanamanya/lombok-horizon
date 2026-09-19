<?php

use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\KavlingController;
use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Kontrak PRD §9: semua endpoint publik di bawah /api/v1
Route::prefix('v1')->group(function () {
    // Project routes
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{slug}', [ProjectController::class, 'show']);
    Route::get('/projects/{slug}/kavlings', [ProjectController::class, 'kavlings']);
    Route::get('/projects/{slug}/geojson', [ProjectController::class, 'geojson']);

    // Kavling routes
    Route::get('/kavlings/{id}', [KavlingController::class, 'show']);
    Route::get('/kavlings/{id}/geojson', [KavlingController::class, 'geojson']);

    // Inquiry routes
    Route::post('/inquiries', [InquiryController::class, 'store'])
        ->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
});
