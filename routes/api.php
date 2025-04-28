<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MetricController;
use App\Models\Metric;
use Illuminate\Support\Facades\Log;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/logs/receive', [LogController::class, 'receive']);
Route::get('/logs/{id}', [LogController::class, 'show']);

Route::post('/metrics/receive', [MetricController::class, 'receive']);
Route::get('/metrics/{id}', [MetricController::class, 'show']);

Route::post('/metrics/cpu', [MetricController::class, 'cpu']);
Route::post('/metrics/mem', [MetricController::class, 'mem']);
