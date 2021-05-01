<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Inertia\Inertia;
use App\Models\Genre;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request) 
    {
        $genre_id = $request->genre;
        $sort_item = $request->sort ?? 'quiz';

        $genres = Genre::all();
        $genres->prepend(['id' => 0, 'name' => 'すべて']);

        if (empty(Genre::find($genre_id))) {
            $quizes = Quiz::paginate()->toArray();
            $genre_list_id = 0;
        }
        else {
            $quizes = Quiz::where('genre_id', $request->genre)->paginate()->toArray();
            $genre_list_id = $genres->search(function($genre) use ($genre_id) {
                return $genre['id'] == $genre_id;
            });
        }

        return Inertia::render('Home', [
            'quizes' => $quizes['data'],
            'quizCount' => $quizes['total'],
            'currentPage' => $quizes['current_page'],
            'perPage' => $quizes['per_page'],
            'genres' => $genres,
            'genreListId' => $genre_list_id,
            'sortItem' => $sort_item,
        ]);
    }

    public function dashboard(Request $request) 
    {
        $user_id = $request->user()->id;
        $quizes = Quiz::where('user_id', $user_id)->paginate()->toArray();

        $sort_item = $request->sort ?? 'quiz';
        $item_list_id = $request->item ?? 'make';

        $items = collect([
            ['name' => '作成クイズ', 'value' => 'make'],
            ['name' => 'いいねクイズ', 'value' => 'like'],
            ['name' => 'コメントクイズ', 'value' => 'comment'],
            ['name' => 'クイズ成績', 'value' => 'grade'],
        ]);
        $item_list_id = $items->search(function($item) use ($item_list_id) {
            return $item['value'] == $item_list_id;
        });

        return Inertia::render('Dashboard', [
            'quizes' => $quizes['data'],
            'quizCount' => $quizes['total'],
            'currentPage' => $quizes['current_page'],
            'perPage' => $quizes['per_page'],
            'items' => $items,
            'itemListId' => $item_list_id,
            'sortItem' => $sort_item,
        ]);
    }
}
