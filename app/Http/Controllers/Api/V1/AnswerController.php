<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class AnswerController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'body' => 'required|string',
            'answerFiles' => 'array',
            'answerFiles.*' => 'file|max:1000|mimes:doc,pdf,docx,zip,jpg,png'
        ]);

        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($validator->fails()) {
            $err = array();
            $errors = $validator->errors();
            foreach ($errors->get('question_id') as $message) {
                //
                array_push($err, (object) array('source' => 'question_id', 'detail' => $message));
            }
            foreach ($errors->get('answerFiles') as $message) {
                //
                array_push($err, (object) array('source' => 'answerFiles', 'detail' => $message));
            }
            foreach ($errors->get('body') as $message) {
                //
                array_push($err, (object) array('source' => 'body', 'detail' => $message));
            }
            foreach ($errors->get('answerFiles.*') as $message) {
                //
                array_push($err, (object) array('source' => 'answerFiles', 'detail' => $message));
            }
            return $this->respondValidationError($err);
        }

        $answer = $user->answers()->create([
            'question_id' => $request->question_id,
            'body' => $request->body,
        ]);


        if ($request->hasfile('answerFiles')) {
            foreach ($request->file('answerFiles') as $file) {
                $name = $answer->id . '_' . md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('answers/files', $file, $name);
                $answerFile = $answer->files()->create([
                    'name' => $name,
                    'file_url' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
        $answer = Answer::with('files')->find($answer->id);
        return $this->itemCreatedResponse($answer);
    }
}
