<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Quiz;
use Inertia\Inertia;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
                    'format' => intval($question['answerFormmat']),
                    'question' => $question['question'],
                ];

                switch($data['format']) {
                    case 1:
                        $data['answer'] = $question['answerText'];
                        break;
                    case 2:
                        $data['answer'] = Item::NUM_STR[$question['answerRadio']];

                        foreach($question['selectItemText'] as $num => $text) {
                            $data['choice' . Item::STR_NUM[$num]] = $text;
                        }
                        break;
                    case 3:
                        $answer_chech = array_map(function($item) {
                            return Item::NUM_STR[$item];
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
        return redirect()->route('home');
    }
}
