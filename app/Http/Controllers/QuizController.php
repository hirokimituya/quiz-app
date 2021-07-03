<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Quiz;
use Inertia\Inertia;
use App\Models\Genre;
use App\Models\Grade;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreComment;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\QuizAnswerRequest;
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

    public function create(Request $request) 
    {
        $filename = $request->image ? basename($request->image) : null;

        if (!empty($filename)) {
            Storage::disk('public')->move('images/tmp/' . $filename, 'images/' .$filename);
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
                Storage::disk('public')->delete('images/' . $filename);
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
            Storage::disk('public')->delete('images/' . $quiz->filename);
        }

        return redirect()->route('dashboard');
    }

    public function showEditForm(Quiz $quiz)
    {
        $genres = Genre::all();

        $items_data = $this->getItems($quiz, true);

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

        if ($request->image) {
            $img_path = $request->file('image')->store('images/tmp', 'public');
            $request_data['image'] = '/storage/' . $img_path;
        }
        else if (!$request->imageDeleteFlg && $quiz->filename !== null) {
            $request_data['image'] = '/storage/images/' . $quiz->filename;
        }

        return Inertia::render('Quiz/EditConfirm', [
            'formData' => $request_data,
            'genres' => $genres,
            'quiz_id' => $quiz->id,
        ]);
    }

    public function edit(Quiz $quiz, Request $request) 
    {
        $filename = $request->image ? basename($request->image) : null;

        $image_change_flg = !empty($filename) && $filename != $quiz->filename;

        // 画像の変更または削除があった場合、現在の画像を削除する。
        if ($image_change_flg || $request->imageDeleteFlg) {
            Storage::disk('public')->delete('images/' . $quiz->filename);
        }

        if ($image_change_flg) {
            Storage::disk('public')->move('images/tmp/' . $filename, 'images/' .$filename);
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

            // クイズに関連するItemモデルの削除
            Item::where('quiz_id', $quiz->id)->delete();

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
            if ($image_change_flg) {
                Storage::disk('public')->delete('images/' . $filename);
            }
            throw $exception;
        }

        return redirect()->route('quiz.detail', ['quiz' => $quiz->id]);
    }

    public function detail(Quiz $quiz)
    {
        $comments = Comment::where('quiz_id', $quiz->id)->with('author')->orderBy('id', 'desc')->get();

        return Inertia::render('Quiz/Detail', [
            'quiz' => $quiz,
            'comments' => $comments,
        ]);
    }

    public function answerForm(Quiz $quiz)
    {
        $items_data = $this->getItems($quiz);

        return Inertia::render('Quiz/AnswerForm', [
            'quiz' => $quiz,
            'items' => $items_data,
        ]);
    }

    public function answerConfirm(Quiz $quiz, QuizAnswerRequest $request) 
    {
        $request_data = $request->all();

        return Inertia::render('Quiz/AnswerConfirm', [
            'quiz' => $quiz,
            'formData' => $request_data,
        ]);
    }

    public function answerResult(Quiz $quiz, Request $request)
    {
        $answers = $request->question;

        $corrects = $quiz->items()->get();

        $correct_count_ary = [];
        
        // 回答が正解かどうかを判定して$answersに結果を追加していく。
        foreach ($answers as $key => $answer) {
            $correct = $corrects->firstWhere('question_number', Item::STR_NUM[$key]);

            switch ($correct->format) {
                case Item::FORMAT_DESCRIPTION:
                    $answers[$key]['correct'] = $correct->answer;

                    if (empty($answer['answerText'])) {
                        $answers[$key]['pass']  = false;
                        break;
                    }

                    if($answer['answerText'] == $correct->answer) {
                        $answers[$key]['pass'] = true;
                        $correct_count_ary[] = Item::STR_NUM[$key];
                    }
                    else {
                        $answers[$key]['pass']  = false;
                    }
                    break;
                case Item::FORMAT_RADIO:
                    $correct_num = Item::STR_NUM[$correct->answer];
                    $answers[$key]['correct'] = $correct['choice' . $correct_num];

                    if (empty($answer['answerRadio'])) {
                        $answers[$key]['pass']  = false;
                        break;
                    }

                    if($answer['answerRadio'] == $correct_num) {
                        $answers[$key]['pass'] = true;
                        $correct_count_ary[] = Item::STR_NUM[$key];
                    }
                    else {
                        $answers[$key]['pass']  = false;
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

                    if (empty($answer['answerCheck'])) {
                        $answers[$key]['pass']  = false;
                        break;
                    }

                    if($this->array_equal_set($answer['answerCheck'], $correct_num_ary)) {
                        $answers[$key]['pass'] = true;
                        $correct_count_ary[] = Item::STR_NUM[$key];
                    }
                    else {
                        $answers[$key]['pass']  = false;
                    }
                    break;
            }
        }

        // gradesテーブルに回答を登録
        if (!empty($correct_count_ary)) {
            $correct_count = implode(',', $correct_count_ary);
        }
        else {
            $correct_count = 0;
        }
        

        $grade = new Grade([
            'user_id' => optional($request->user())->id,
            'correct_count' => $correct_count,
        ]);

        $quiz->grades()->save($grade);

        return Inertia::render('Quiz/AnswerResult', [
            'quiz' => $quiz,
            'answers' => $answers,
            'correct_count' => count($correct_count_ary),
        ]);
    }

    private function array_equal_set($a, $b)
    {
        $diff_a_to_b = array_diff($a, $b);
        $diff_b_to_a = array_diff($b, $a);
        return empty($diff_a_to_b) && empty($diff_b_to_a);
    }

    public function addComment(Quiz $quiz, StoreComment $request) {
        $comment = new Comment();
        $comment->content = $request->comment;
        $comment->user_id = $request->user()->id;
        $quiz->comments()->save($comment);

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

    private function getItems(Quiz $quiz, $ans_need = false)
    {
        $items = Item::where('quiz_id', $quiz->id)->get();

        $items_data = [];
        foreach ($items as $item) {
            $key = Item::NUM_STR[$item->question_number];
            $items_data[$key] = [
                'question' => $item->question,
                'answerFormat' => $item->format,
            ];

            if ($ans_need && $item->format == Item::FORMAT_DESCRIPTION) {
                $items_data[$key]['answerText'] = $item->answer;
            }

            if ($item->format == Item::FORMAT_RADIO || $item->format == Item::FORMAT_CHECK) {
                $num = 0;
                $text_ary = [];
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($item['choice' . $i])) {
                        $num++;
                        $text_ary[Item::NUM_STR[$i]] = $item['choice' . $i];
                    }
                }
                $items_data[$key]['selectItemsNum'] = $num;
                $items_data[$key]['selectItemText'] = $text_ary;

                if ($ans_need) {
                    if ($item->format == Item::FORMAT_RADIO) {
                        $answer = Item::STR_NUM[$item->answer];
                        $items_data[$key]['answerRadio'] = $answer;
                    }
                    else {
                        $ans_str = explode(',', $item->answer);
                        $answers = collect($ans_str)->map(fn($str) => Item::STR_NUM[$str]);
                        $items_data[$key]['answerCheck'] = $answers;
                    }
                }
            }
        }

        return $items_data;
    }
}
