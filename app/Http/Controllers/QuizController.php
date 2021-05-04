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
        $genres = Genre::all();

        $request_data = $request->all();

        if ($request->image) {
            $img_path = $request->file('image')->store('images/tmp', 'public');
            $request_data['image'] = '/storage/' . $img_path;
        }

        return Inertia::render('Quiz/CreateConfirm', [
            'formData' => $request_data,
            'genres' => $genres,
        ]);
    }
}
