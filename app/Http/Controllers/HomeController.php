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
        $search_query = $request->q;
        $sort_item = $request->sort ?? 'new';

        $genres = Genre::all();
        $genres->prepend(['id' => 0, 'name' => 'すべて']);

        if (empty(Genre::find($genre_id))) {
            if (empty($search_query)) {
                $quizes = Quiz::sort($sort_item)->paginate()->toArray();
            }
            else {
                $quizes = Quiz::searchWith('%' . $search_query . '%')->sort($sort_item)->paginate()->toArray();
            }
            
            $genre_list_id = 0;
        }
        else {
            $quizes = Quiz::where('genre_id', $request->genre)->sort($sort_item)->paginate()->toArray();
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
        $sort_item = $request->sort ?? 'new';
        $item_list_name = $request->item ?? 'make';
        
        $quizes = Quiz::ofType($item_list_name, $sort_item)->sort($sort_item)->paginate()->toArray();

        // ダッシュボードの左側に記載する数を算出
        $quiz_create_count = Quiz::where('user_id', $user_id)->count();
        $likes_total_count = Quiz::rightJoin('likes', 'quizzes.id', '=', 'likes.quiz_id')->where('quizzes.user_id', $user_id)->count();
        $quiz_grades_count = $request->user()->grades()->count();

        $items = collect([
            ['name' => '作成クイズ', 'value' => 'make'],
            ['name' => 'いいねクイズ', 'value' => 'like'],
            ['name' => 'コメントクイズ', 'value' => 'comment'],
            ['name' => '実施クイズ', 'value' => 'grade'],
        ]);
        $item_list_id = $items->search(function($item) use ($item_list_name) {
            return $item['value'] == $item_list_name;
        });

        return Inertia::render('Dashboard', [
            'quizes' => $quizes['data'],
            'quizCount' => $quizes['total'],
            'currentPage' => $quizes['current_page'],
            'perPage' => $quizes['per_page'],
            'items' => $items,
            'itemListId' => $item_list_id,
            'sortItem' => $sort_item,
            'quiz_create_count' => $quiz_create_count,
            'likes_total_count' => $likes_total_count,
            'quiz_grades_count' => $quiz_grades_count,
        ]);
    }
}
