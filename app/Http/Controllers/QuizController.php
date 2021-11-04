<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Quiz;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Genre;
use App\Models\Grade;
use App\Models\Answer;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\QuizAnswerRequest;
use App\Http\Requests\QuizCreateRequest;
use App\Models\CorrectRecord;

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

        $request_data = self::answerTextTrim($request_data);

        if ($request->image) {
            $img_path = $request->file('image')->store('images/tmp', 's3');
            $img_path = Storage::disk('s3')->url($img_path);
            $request_data['image'] = $img_path;
        }

        return Inertia::render('Quiz/CreateConfirm', [
            'formData' => $request_data,
            'genres' => $genres,
        ]);
    }

    public function create(Request $request) 
    {
        $filename = $request->image ? basename($request->image) : null;

        if (!empty($filename)) {
            Storage::disk('s3')->move('images/tmp/' . $filename, 'images/' .$filename);
        }

        DB::beginTransaction();

        try {
            $quiz = Quiz::create([
                'user_id' => $request->user()->id,
                'genre_id' => intval($request->genre),
                'title' => $request->title,
                'description' => $request->description,
                'filename' => $filename
            ]);

            $items= [];
            foreach ($request->question as $key => $question) {
                $data = [
                    'question_number' => Item::STR_NUM[$key],
                    'format' => intval($question['answerFormat']),
                    'question' => $question['question'],
                ];

                switch($data['format']) {
                    case Item::FORMAT_DESCRIPTION:
                        $data['answer'] = $question['answerText'];
                        break;
                    case Item::FORMAT_RADIO:
                        $data['answer'] = Item::NUM_STR[intval($question['answerRadio'])];

                        foreach($question['selectItemText'] as $num => $text) {
                            $data['choice' . Item::STR_NUM[$num]] = $text;
                        }
                        break;
                    case Item::FORMAT_CHECK:
                        $answer_chech = array_map(function($item) {
                            return Item::NUM_STR[intval($item)];
                        }, $question['answerCheck']);

                        $data['answer'] = implode(',', $answer_chech);

                        foreach($question['selectItemText'] as $num => $text) {
                            $data['choice' . Item::STR_NUM[$num]] = $text;
                        }
                        break;
                    default:
                        abort(400);
                        break;
                }

                $items[] = new Item($data);
            }

            $quiz->items()->saveMany($items);

            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            if (!empty($filename)) {
                Storage::disk('s3')->delete('images/' . $filename);
            }
            throw $exception;
        }

        return redirect()->route('quiz.detail', ['quiz' => $quiz->id]);
    }

    public function delete(Quiz $quiz)
    {
        $filename = $quiz->filename;

        $quiz->delete();

        if (!empty($filename)) {
            Storage::disk('s3')->delete('images/' . $quiz->filename);
        }

        return redirect()->route('dashboard', ['user' => $quiz->user_id]);
    }

    public function showEditForm(Quiz $quiz)
    {
        $genres = Genre::all();

        $items_data = $quiz->getItems(true);

        return Inertia::render('Quiz/EditForm', [
            'genres' => $genres,
            'quiz' => $quiz,
            'items' => $items_data,
        ]);
    }

    public function showEditConfirm(Quiz $quiz, QuizCreateRequest $request)
    {
        $genres = Genre::all();

        $request_data = $request->all();

        $request_data = self::answerTextTrim($request_data);

        if ($request->image) {
            $img_path = $request->file('image')->store('images/tmp', 's3');
            $img_path = Storage::disk('s3')->url($img_path);
            $request_data['image'] = $img_path;
        }
        else if (!$request->imageDeleteFlg && $quiz->filename !== null) {
            $request_data['image'] = Storage::disk('s3')->url('images/' . $quiz->filename);
        }

        return Inertia::render('Quiz/EditConfirm', [
            'formData' => $request_data,
            'genres' => $genres,
            'quizId' => $quiz->id,
        ]);
    }

    public function edit(Quiz $quiz, Request $request) 
    {
        $filename = $request->image ? basename($request->image) : null;

        $image_change_flg = !empty($filename) && $filename != $quiz->filename;

        // 画像の変更または削除があった場合、現在の画像を削除する。
        if ($image_change_flg || $request->imageDeleteFlg) {
            Storage::disk('s3')->delete('images/' . $quiz->filename);
        }

        if ($image_change_flg) {
            Storage::disk('s3')->move('images/tmp/' . $filename, 'images/' .$filename);
        }

        DB::beginTransaction();

        try {
            $quiz->genre_id = intval($request->genre);
            $quiz->title = $request->title;
            $quiz->description = $request->description;
            if ($image_change_flg || $request->imageDeleteFlg) {
                $quiz->filename = $filename;
            }
            $quiz->save();

            $last_question_number = 0;
            foreach ($request->question as $key => $question) {
                $data = [
                    'question_number' => Item::STR_NUM[$key],
                    'format' => intval($question['answerFormat']),
                    'question' => $question['question'],
                ];

                // 「choice*」カラムを一旦リセットする。
                for ($i = 1; $i <= 4; $i++) {
                    $data['choice' . $i] = null;
                }

                switch($data['format']) {
                    case Item::FORMAT_DESCRIPTION:
                        $data['answer'] = $question['answerText'];
                        break;
                    case Item::FORMAT_RADIO:
                        $data['answer'] = Item::NUM_STR[intval($question['answerRadio'])];

                        foreach($question['selectItemText'] as $num => $text) {
                            $data['choice' . Item::STR_NUM[$num]] = $text;
                        }
                        break;
                    case Item::FORMAT_CHECK:
                        $answer_chech = array_map(function($item) {
                            return Item::NUM_STR[intval($item)];
                        }, $question['answerCheck']);

                        $data['answer'] = implode(',', $answer_chech);

                        foreach($question['selectItemText'] as $num => $text) {
                            $data['choice' . Item::STR_NUM[$num]] = $text;
                        }
                        break;
                    default:
                        abort(400);
                        break;
                }

                if (isset($question['id'])) {
                    // アイテムの更新
                    Item::where('id', $question['id'])->update($data);
                }
                else {
                    // アイテムの作成
                    $item_data = new Item($data);
                    $quiz->items()->save($item_data);
                }

                $last_question_number = $data['question_number'];
            }

            // 作成や更新されなかったアイテムの削除
            Item::where('quiz_id', $quiz->id)
                ->where('question_number', '>', $last_question_number)
                ->delete();
            
            DB::commit();
        }
        catch (\Exception $exception) {
            DB::rollBack();
            if ($image_change_flg) {
                Storage::disk('s3')->delete('images/' . $filename);
            }
            throw $exception;
        }

        return redirect()->route('quiz.detail', ['quiz' => $quiz->id]);
    }

    public function detail(Quiz $quiz)
    {
        $comments = Comment::where('quiz_id', $quiz->id)->with('author')->orderBy('id', 'desc')->get();

        $user_grades_ary = [];
        if (Auth::check()) {
            $grades = Grade::quizUserGet($quiz, Auth::user())->get();
            $grades = $grades->sortBy('created_at')->values();
            $user_grades_ary = $grades->map(function($grade) {
                return $grade->correctCount();
            });
        }

        return Inertia::render('Quiz/Detail', [
            'quiz' => $quiz,
            'comments' => $comments,
            'userGradesAry' => $user_grades_ary,
        ]);
    }

    public function answerForm(Quiz $quiz)
    {
        $items_data = $quiz->getItems();

        return Inertia::render('Quiz/AnswerForm', [
            'quiz' => $quiz,
            'items' => $items_data,
        ]);
    }

    public function answerConfirm(Quiz $quiz, QuizAnswerRequest $request) 
    {
        $request_data = $request->all();

        $request_data = self::answerTextTrim($request_data);

        return Inertia::render('Quiz/AnswerConfirm', [
            'quiz' => $quiz,
            'formData' => $request_data,
        ]);
    }

    public function answerResult(Quiz $quiz, Request $request)
    {
        $answers = $request->question;

        $corrects = $quiz->items()->get();

        $answers_table = [];
        
        // 回答が正解かどうかを判定して$answersに結果を追加していく。
        foreach ($answers as $key => $answer) {
            $correct = $corrects->firstWhere('question_number', Item::STR_NUM[$key]);

            $answer_table = [];
            $answer_table['item_id'] = $correct->id;

            switch ($correct->format) {
                case Item::FORMAT_DESCRIPTION:
                    $answers[$key]['correct'] = $correct->answer;

                    $answer_table['answer'] = $answer['answerText'] ?? '';

                    if (empty($answer['answerText'])) {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                        break;
                    }

                    if($answer['answerText'] == $correct->answer) {
                        $answers[$key]['pass'] = true;
                        $answer_table['pass'] = true;
                    }
                    else {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                    }
                    break;
                case Item::FORMAT_RADIO:
                    $correct_num = Item::STR_NUM[$correct->answer];
                    $answers[$key]['correct'] = $correct['choice' . $correct_num];

                    $answer_table['answer'] = $answer['answerRadio'] ?? '';

                    if (empty($answer['answerRadio'])) {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                        break;
                    }

                    if($answer['answerRadio'] == $correct_num) {
                        $answers[$key]['pass'] = true;
                        $answer_table['pass'] = true;
                    }
                    else {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                    }
                    break;
                case Item::FORMAT_CHECK:
                    $correct_num_ary = array_map(function($el) {
                        return Item::STR_NUM[$el];
                    }, explode(',', $correct->answer));

                    $correct_ary = [];
                    foreach ($correct_num_ary as $correct_num) {
                        $correct_ary[] = $correct['choice' . $correct_num];
                    }

                    $answers[$key]['correct'] = $correct_ary;

                    $answer_table['answer'] = isset($answer['answerCheck']) ? implode(',', $answer['answerCheck']) : '';

                    if (empty($answer['answerCheck'])) {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                        break;
                    }

                    if($this->array_equal_set($answer['answerCheck'], $correct_num_ary)) {
                        $answers[$key]['pass'] = true;
                        $answer_table['pass'] = true;
                    }
                    else {
                        $answers[$key]['pass']  = false;
                        $answer_table['pass'] = false;
                    }
                    break;
            }

            $answer_instance = new Answer($answer_table);
            if ($answer_instance->isNeverBeforeCorrect()) {
                optional($request->user())->increment('total_correct_count');

                optional($request->user())->correct_records()->save(new CorrectRecord());
            }

            $answers_table[] = $answer_instance;
        }

        $grade = $quiz->grades()->create([
            'user_id' => optional($request->user())->id,
        ]);

        $grade->answers()->saveMany($answers_table);

        return Inertia::render('Quiz/AnswerResult', [
            'quiz' => $quiz,
            'answers' => $answers,
            'correctCount' => $grade->correctCount(),
        ]);
    }

    private function array_equal_set($a, $b)
    {
        $diff_a_to_b = array_diff($a, $b);
        $diff_b_to_a = array_diff($b, $a);
        return empty($diff_a_to_b) && empty($diff_b_to_a);
    }

    public function addComment(Quiz $quiz, StoreComment $request) 
    {
        $comment = new Comment();
        $comment->content = $request->comment;
        $comment->user_id = $request->user()->id;
        $quiz->comments()->save($comment);

        return redirect()->route('quiz.detail', [
            'quiz' => $quiz->id,
        ]);
    }

    public function editComment(Quiz $quiz, Comment $comment, StoreComment $request) 
    {
        $comment->content = $request->comment;
        $comment->save();

        return redirect()->route('quiz.detail', [
            'quiz' => $quiz->id,
        ]);
    }

    public function deleteComment(Quiz $quiz, Comment $comment) 
    {
        $comment->delete();

        return redirect()->route('quiz.detail', [
            'quiz' => $quiz->id,
        ]);
    }

    public function like(Quiz $quiz, Request $request)
    {
        $quiz->likes()->detach($request->user()->id);
        $quiz->likes()->attach($request->user()->id);

        return response('');
    }

    public function unlike(Quiz $quiz, Request $request)
    {
        $quiz->likes()->detach($request->user()->id);

        return response('');
    }

    /**
     * 確認画面へのリクエスデータ内の記述式の回答の両端の全角・半角スペースを取り除く。
     * @param $request_data リクエストデータ
     * @return 回答の両端の全角・半角スペースを取り除いたリクエストデータ
     */
    private static function answerTextTrim(array $request_data)
    {
        $ret_request_data = $request_data;
        $questions = collect($request_data['question']);

        $questions->transform(function($question) {
            if (isset($question['answerText'])) {
                $question['answerText'] = preg_replace('/\A[\x00\s]++|[\x00\s]++\z/u', '', $question['answerText']);
            }
            return $question;
        });

        $ret_request_data['question'] = $questions->toArray();
        return $ret_request_data;
    }
}
