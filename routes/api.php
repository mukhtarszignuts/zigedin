<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ConnectionController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\EducationController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SkillController;
use App\Http\Controllers\API\WorkExperienceController;


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

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('registration', 'registration');
    Route::post('forget-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('change-password', 'changePassword');
    });

    // Admin Route 
    Route::prefix('admin')->group(function () {

        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'dashboard');
        });

        Route::prefix('user')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::post('list', 'index');
                Route::get('/{id}', 'show');
                Route::post('create', 'store');
                Route::post('update', 'update');
                Route::get('delete/{id}', 'delete');
                Route::post('bulk-delete', 'bulkDelete');
                Route::get('status-change/{id}', 'statusChange');
            });
        });
    });

    Route::prefix('user')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/{id}', 'show');
        });
    });

    Route::prefix('skill')->group(function () {
        Route::controller(SkillController::class)->group(function () {
            Route::post('list', 'index');
            Route::get('/{id}', 'show');
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('job')->group(function () {
        Route::controller(JobController::class)->group(function () {
            Route::post('list', 'index');
            Route::get('/{id}', 'show');
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('work')->group(function () {
        Route::controller(WorkExperienceController::class)->group(function () {
            Route::post('list', 'index');
            Route::get('/{id}', 'show');
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('education')->group(function () {
        Route::controller(EducationController::class)->group(function () {
            Route::post('list', 'index');
            Route::get('/{id}', 'show');
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('connections')->group(function () {
        Route::controller(ConnectionController::class)->group(function () {
            Route::post('list', 'index');
            Route::post('suggest', 'suggestedConnection');
            Route::post('create', 'store');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('posts')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::post('list', 'index');
            Route::post('create', 'store');
            Route::get('like/{id}', 'likePost');
            Route::get('/{id}', 'show');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });

    Route::prefix('comments')->group(function () {
        Route::controller(CommentController::class)->group(function () {
            Route::post('list', 'index');
            Route::post('create', 'store');
            Route::get('/{id}', 'show');
            Route::post('update', 'update');
            Route::get('delete/{id}', 'delete');
        });
    });
});
