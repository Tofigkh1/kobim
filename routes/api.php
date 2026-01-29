<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\KobimController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\VoenApiController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/trainings/planing/', [TrainingController::class, 'getPlaning']);
Route::get('/trainings/planing/soon/{count?}', [TrainingController::class, 'getLastPlaning']);
Route::get('/trainings/planing/{id}', [TrainingController::class, 'getPlaningById']);
Route::post('/training/view/{id}',[TrainingController::class,'incrementCount']);
Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');

Route::get('/kobim', [KobimController::class, 'index']);
Route::get('/kobim/{id}', [KobimController::class, 'show']);


