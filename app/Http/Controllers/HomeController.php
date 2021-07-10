<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Genre;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function dashboard(User $user, Request $request) 
    {
        if ($user->id === null) {
            if (Auth::check()) {
                $user = $request->user();
            }
            else {
                return redirect()->route('login');
            }
        }
        $user_id = $user->id;
        $sort_item = $request->sort ?? 'new';
        $item_list_name = $request->item ?? 'make';
        
        $quizes = Quiz::ofType($user_id, $item_list_name, $sort_item)->sort($sort_item)->paginate()->toArray();

        // ダッシュボードの左側に記載する数を算出
        $quiz_create_count = Quiz::where('user_id', $user_id)->count();
        $likes_total_count = Quiz::rightJoin('likes', 'quizzes.id', '=', 'likes.quiz_id')->where('quizzes.user_id', $user_id)->count();
        $quiz_grades_count = $user->grades()->count();

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
            'dashboard_user' => $user,
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

    public function grade(User $user, Request $request)
    {
        $sort_item = $request->sort ?? 'latest';
        $disp_num = $request->disp_num ? intval($request->disp_num) : 30;

        if ($user->id === null) {
            if (Auth::check()) {
                $user = $request->user();
            }
            else {
                return abort(404);
            }
        }
        else {
            if (!$user->show_grade) {
                if ($user->id !== optional($request->user())->id) {
                    return abort(403);
                }
            }
        }

        $grades = Grade::where('grades.user_id', $user->id)->sort($sort_item)->with('quiz.items')->paginate($disp_num)->toArray();

        $grades_list = [];
        foreach ($grades['data'] as $grade) {
            $grades_list[] = [
                'grade_id' => $grade['id'],
                'quiz_id' => $grade['quiz']['id'],
                'quiz_title' => $grade['quiz']['title'],
                'genre_name' => $grade['quiz']['genre']['name'],
                'user_id' => $grade['quiz']['user']['id'],
                'user_name' => $grade['quiz']['user']['name'],
                'items_count' => count($grade['quiz']['items']),
                'correct_count' => $grade['correct_count'] != 0 ? count(explode(',', $grade['correct_count'])) : 0,
                'created_at' => Carbon::parse($grade['created_at'])->addHour(9)->format('Y/m/d H:i:s'),
            ];
        }

        return Inertia::render('Quiz/GradeList', [
            'grades_list' => $grades_list,
            'grade_user' => $user,
            'gradeCount' => $grades['total'],
            'currentPage' => $grades['current_page'],
            'perPage' => $grades['per_page'],
            'sortItem'=> $sort_item,
        ]);
    }

    public function showGradeSetting(Request $request)
    {
        $user = $request->user();

        $user->show_grade = $request->show_grade;

        $user->save();

        return redirect()->route('profile.show');
    }
}
