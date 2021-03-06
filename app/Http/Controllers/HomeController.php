<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Genre;
use App\Models\Grade;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\CorrectRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home(Request $request) 
    {
        $genre_id = $request->genre;
        $search_query = $request->q;
        $sort_item = $request->sort ?? 'new';

        $genres = Genre::all();
        $genres = $genres->sortByDesc('quiz_count');
        $genres->prepend(['id' => 0, 'name' => 'すべて', 'quiz_count' => Quiz::count()]);

        $users = ['data' => User::orderByDesc('total_correct_count')->limit(5)->get()->toArray()];
        $users_ranking_list = self::getUsersRankingCorrect($users);

        if (empty(Genre::find($genre_id))) {
            $quizes = Quiz::searchWith('%' . $search_query . '%')->sort($sort_item)->paginate()->toArray();
            
            $genre_list_id = 0;
        }
        else {
            $quizes = Quiz::searchWith('%' . $search_query . '%')->where('genre_id', $request->genre)->sort($sort_item)->paginate()->toArray();
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
            'usersRanking' => $users_ranking_list,
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
            'dashboardUser' => $user,
            'quizCount' => $quizes['total'],
            'currentPage' => $quizes['current_page'],
            'perPage' => $quizes['per_page'],
            'items' => $items,
            'itemListId' => $item_list_id,
            'sortItem' => $sort_item,
            'quizCreateCount' => $quiz_create_count,
            'likesTotalCount' => $likes_total_count,
            'quizGradesCount' => $quiz_grades_count,
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
                'genre_id' => $grade['quiz']['genre']['id'],
                'genre_name' => $grade['quiz']['genre']['name'],                
                'user_id' => $grade['quiz']['user']['id'],
                'user_name' => $grade['quiz']['user']['name'],
                'items_count' => count($grade['quiz']['items']),
                'correct_count' => Grade::find($grade['id'])->correctCount(),
                'created_at' => Carbon::parse($grade['created_at'])->addHour(9)->format('Y/m/d H:i:s'),
            ];
        }

        return Inertia::render('Quiz/GradeList', [
            'gradesList' => $grades_list,
            'gradeUser' => $user,
            'gradeCount' => $grades['total'],
            'currentPage' => $grades['current_page'],
            'perPage' => $grades['per_page'],
            'sortItem'=> $sort_item,
        ]);
    }

    public function gradeDetail(User $user, Grade $grade, Request $request)
    {
        if (!$user->show_grade) {
            if ($user->id !== optional($request->user())->id) {
                return abort(403);
            }
        }

        $quiz = $grade->quiz()->first();
        $correct_count = $grade->correctCount();

        // クイズ実行履歴の詳細回答をキャッシュするようにする。
        $answers = Cache::remember('quiz_grade_id_' . $grade->id, now()->addHours(2), function () use ($grade, $quiz) {
            $answers = [];
            $answers_table = $grade->answers()->get();

            $correct_rates = $quiz->getItemCorrectRate();
            $items = $quiz->items()->get();
            foreach ($items as $item) {
                $question_num = $item->question_number;
                $item_no = Item::NUM_STR[$question_num];
                $answer_table = $answers_table->firstWhere('item_id', $item->id);

                $answers[$item_no]['question'] = $item->question;
                $answers[$item_no]['answerFormat'] = $item->format;
                $answers[$item_no]['correct'] = $item->getCorrectStr();
                $answers[$item_no]['pass'] = optional($answer_table)->pass;
                $answers[$item_no]['correctRate'] = $correct_rates[$question_num];

                switch ($item->format) {
                    case Item::FORMAT_DESCRIPTION:
                        $answers[$item_no]['answerText'] = optional($answer_table)->answer;

                        break;
                    case Item::FORMAT_RADIO:
                        $answers[$item_no]['answerRadio'] = (int)optional($answer_table)->answer;
                        $answers[$item_no]['selectItemsNum'] = $item->getChoicesNum();
                        $answers[$item_no]['selectItemText'] = $item->getChoices();

                        break;
                    case Item::FORMAT_CHECK:
                        $answer_check_ary = explode(',', optional($answer_table)->answer);
                        $answer_check_ary = array_map(fn($n) => (int)$n, $answer_check_ary);

                        $answers[$item_no]['answerCheck'] = $answer_check_ary;
                        $answers[$item_no]['selectItemsNum'] = $item->getChoicesNum();
                        $answers[$item_no]['selectItemText'] = $item->getChoices();

                        break;
                }
            }
            return $answers;
        });

        return Inertia::render('Quiz/AnswerResult', [
            'quiz' => $quiz,
            'answers' => $answers,
            'correctCount' => $correct_count,
            'referrerPage' => 'GradeDetail',
        ]);
    }

    public function showGradeSetting(Request $request)
    {
        $user = $request->user();

        $user->show_grade = $request->show_grade;

        $user->save();

        return redirect()->route('profile.show');
    }

    public function rankingCorrect(Request $request)
    {
        $page = $request->page ? intval($request->page) : 1;
        $disp_num = $request->disp_num ? intval($request->disp_num) : 30;

        $users = User::orderByDesc('total_correct_count')->paginate($disp_num)->toArray();

        $users_list = self::getUsersRankingCorrect($users, $disp_num, $page);

        return Inertia::render('Quiz/RankingCorrect', [
            'usersList' => $users_list,
            'userCount' => $users['total'],
            'currentPage' => $users['current_page'],
            'perPage' => $users['per_page'],
        ]);
    }

    public function rankingWeekly(Request $request)
    {
        $correctCount = [];

        CorrectRecord::weekly()->chunk(100, function($correctRecords) use (&$correctCount) {
            foreach($correctRecords as $correctRecord) {
                if (!isset($correctCount[$correctRecord->user_id])) {
                    $correctCount[$correctRecord->user_id] = 0;
                }
                $correctCount[$correctRecord->user_id]++;
            }
        });

        $correctCount = Arr::sort($correctCount);
        arsort($correctCount);

        return self::getUsersRankingArrange($correctCount);
    }

    public function rankingmonthly(Request $request)
    {
        $correctCount = [];

        CorrectRecord::monthly()->chunk(100, function($correctRecords) use (&$correctCount) {
            foreach($correctRecords as $correctRecord) {
                if (!isset($correctCount[$correctRecord->user_id])) {
                    $correctCount[$correctRecord->user_id] = 0;
                }
                $correctCount[$correctRecord->user_id]++;
            }
        });

        $correctCount = Arr::sort($correctCount);
        arsort($correctCount);

        return self::getUsersRankingArrange($correctCount);
    }

    /**
     * 配列をランキング表示用に整形する。
     * @param $users ユーザ情報の配列
     * @param $disp_num ランキングの表示数
     * @param $page ページ数
     * @return ランキング表示用に整形した配列
     */
    public static function getUsersRankingCorrect(array $users, int $disp_num = 0, int $page = 0)
    {
        $users_list = [];
        $ranking = $disp_num * ($page - 1);
        $prev_total_count_correct = -1;
        $carry_over = 0;
        foreach ($users['data'] as $user) {
            if ($user['total_correct_count'] != $prev_total_count_correct) {
                $ranking = $ranking + 1 + $carry_over;
                $carry_over = 0;
                $prev_total_count_correct = $user['total_correct_count'];
            }
            else {
                $carry_over++;
            }
            
            $users_list[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'ranking' => $ranking,
                'total_correct_count' => $user['total_correct_count'],
                'show_grade' => $user['show_grade'],
                'profile_photo_url' => $user['profile_photo_url'],
            ];
        }

        return $users_list;
    }
    
    /**
     * 週間・月間ランキングの配列を表示用に整形する。
     * @param $correctCounts キーがユーザIDで値が正解数の配列
     * @return ランキング表示用に整形した配列
     */
    public static function getUsersRankingArrange(array $correctCounts)
    {
        $users_list = [];
        $ranking = 0;
        $prev_total_count_correct = -1;
        $carry_over = 0;
        foreach ($correctCounts as $user_id => $correctCount) {
            $user = User::find($user_id);

            if ($correctCount != $prev_total_count_correct) {
                $ranking = $ranking + 1 + $carry_over;
                $carry_over = 0;
                $prev_total_count_correct = $correctCount;
            }
            else {
                $carry_over++;
            }
            
            $users_list[] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'ranking' => $ranking,
                'total_correct_count' => $correctCount,
                'show_grade' => $user['show_grade'],
                'profile_photo_url' => $user['profile_photo_url'],
            ];
        }

        return $users_list;
    }
}
