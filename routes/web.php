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

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/quiz/create', [QuizController::class, 'showCreateForm'])->name('quiz.create');

    Route::post('/quiz/confirm', [QuizController::class, 'showCreateConfirm'])->name('quiz.create.conf');

    Route::post('/quiz/create', [QuizController::class, 'create']);

    Route::post('/quiz/{quiz}/comments', [QuizController::class, 'addComment'])->name('quiz.comment');

    Route::put('/quiz/{quiz}/like', [QuizController::class, 'like'])->name('quiz.like');

    Route::delete('/quiz/{quiz}/like', [QuizController::class, 'unlike']);
});

Route::get('/quiz/{quiz}', [QuizController::class, 'detail'])->where('quiz', '[0-9]*')->name('quiz.detail');

Route::get('/quiz/{quiz}/answer', [QuizController::class, 'answerForm'])->where('quiz', '[0-9]*')->name('quiz.answer');

Route::post('/quiz/{quiz}/answer/confirm', [QuizController::class, 'answerConfirm'])->where('quiz', '[0-9]*')->name('quiz.answer.conf');

Route::post('/quiz/{quiz}/answer/result', [QuizController::class, 'answerResult'])->where('quiz', '[0-9]*')->name('quiz.answer.result');

Route::get('/{any?}', fn() => redirect()->route('home'))->where('any', '.+');
