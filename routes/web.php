<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('cache.headers:no_store')->group(function () {
    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::get('/dashboard/{user?}', [HomeController::class, 'dashboard'])->name('dashboard')->where('user', '[0-9]+');

    Route::get('/grade/{user?}', [HomeController::class, 'grade'])->name('grade')->where('user', '[0-9]+');

    Route::get('/grade/{user}/{grade}', [HomeController::class, 'gradeDetail'])->name('grade.detail')->where(['user' => '[0-9]+', 'grade' => '[0-9]+']);

    Route::get('/quiz/{quiz}', [QuizController::class, 'detail'])->where('quiz', '[0-9]*')->name('quiz.detail');

    Route::get('/quiz/{quiz}/answer', [QuizController::class, 'answerForm'])->where('quiz', '[0-9]*')->name('quiz.answer');

    Route::post('/quiz/{quiz}/answer/confirm', [QuizController::class, 'answerConfirm'])->where('quiz', '[0-9]*')->name('quiz.answer.conf');

    Route::post('/quiz/{quiz}/answer/result', [QuizController::class, 'answerResult'])->where('quiz', '[0-9]*')->name('quiz.answer.result');

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/quiz/create', [QuizController::class, 'showCreateForm'])->name('quiz.create');

        Route::post('/quiz/confirm', [QuizController::class, 'showCreateConfirm'])->name('quiz.create.conf');

        Route::post('/quiz/create', [QuizController::class, 'create']);

        Route::delete('/quiz/{quiz}', [QuizController::class, 'delete'])->middleware('can:delete,quiz');

        Route::middleware('can:update,quiz')->group(function() {
            Route::get('/quiz/{quiz}/edit', [QuizController::class, 'showEditForm'])->name('quiz.edit');

            Route::post('/quiz/{quiz}/edit/confirm', [QuizController::class, 'showEditConfirm'])->name('quiz.edit.conf');

            Route::patch('/quiz/{quiz}/edit', [QuizController::class, 'edit']);
        });

        Route::post('/quiz/{quiz}/comments', [QuizController::class, 'addComment'])->name('quiz.comment');

        Route::patch('/quiz/{quiz}/comments/{comment}', [QuizController::class, 'editComment'])->middleware('can:update,comment')->name('quiz.comment.edit');

        Route::delete('/quiz/{quiz}/comments/{comment}', [QuizController::class, 'deleteComment'])->middleware('can:delete,comment');

        Route::put('/quiz/{quiz}/like', [QuizController::class, 'like'])->name('quiz.like');

        Route::delete('/quiz/{quiz}/like', [QuizController::class, 'unlike']);

        Route::patch('/user/show/grade', [HomeController::class, 'showGradeSetting'])->name('show.grade');

    });
});
