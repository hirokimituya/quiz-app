<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Quiz;
use Inertia\Inertia;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function detail(Quiz $quiz)
    {
        return Inertia::render('Quiz/Detail', [
            'quiz' => $quiz,
        ]);
    }

    public function answerForm(Quiz $quiz)
    {
        $items = Item::where('quiz_id', $quiz->id)->get();

        $items_data = [];
        foreach ($items as $item) {
            $key = Item::NUM_STR[$item->question_number];
            $items_data[$key] = [
                'question' => $item->question,
                'answerFormat' => $item->format,
            ];

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
            }
        }

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
}
