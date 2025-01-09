<?php

use App\Http\Controllers\RouteController;
use App\Http\Controllers\TailwickController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\FilterController;
use App\Http\Controllers\Web\JobController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('index/{locale}', [TailwickController::class, 'lang']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get("/", [RouteController::class, 'index'])->name('dashboard');
    Route::resource('jobs', JobController::class);
    Route::get('job/accept/{matricule}', [JobController::class, 'accept'])->name('jobs.accept');
    Route::get('job/reject/{matricule}', [JobController::class, 'reject'])->name('jobs.reject');
    Route::post('job/store', [JobController::class, 'hireUser'])->name('jobs.hire');
    Route::resource('customer', CustomerController::class);
    Route::resource('users', UserController::class);
    Route::controller(FilterController::class)->name('filter.')->prefix('filter')->group(function () {
        Route::get('create','create')->name('create');
        Route::post('search','search')->name('search');
    });
    Route::get("{any}", [RouteController::class, 'routes']);
});
