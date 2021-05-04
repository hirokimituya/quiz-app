<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Requests\QuizCreateRequest;

class QuizController extends Controller
{
    public function showCreateForm()
    {
        $genres = Genre::all();

        return Inertia::render('Quiz/CreateForm', [
            'genres' => $genres,
        ]);
    }

    public function showCreateConfirm(QuizCreateRequest $request)
    {
        return Inertia::render('Quiz/CreateConfirm', [
            'formData' => $request->all(),
        ]);
    }
}
