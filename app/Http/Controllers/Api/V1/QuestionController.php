<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $paginator = Question::orderBy('id', 'desc')->paginate($request->perPage);
        return $this->respondWithPagination($paginator, $paginator->items());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //

    }

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
            'title' => 'required|string|unique:questions,title',
            'subject_id' => 'required|exists:subjects,id',
            'body' => 'required|string',
            'questionFiles' => 'array',
            'questionFiles.*' => 'file|max:1000|mimes:doc,pdf,docx,zip,jpg,png'
        ]);

        /**
         * @var User $user
         */
        $user = auth()->user();

        if ($validator->fails()) {
            $err = array();
            $errors = $validator->errors();
            foreach ($errors->get('title') as $message) {
                //
                array_push($err, (object) array('source' => 'title', 'detail' => $message));
            }
            foreach ($errors->get('subject_id') as $message) {
                //
                array_push($err, (object) array('source' => 'subject_id', 'detail' => $message));
            }
            foreach ($errors->get('questionFiles') as $message) {
                //
                array_push($err, (object) array('source' => 'questionFiles', 'detail' => $message));
            }
            foreach ($errors->get('body') as $message) {
                //
                array_push($err, (object) array('source' => 'body', 'detail' => $message));
            }
            foreach ($errors->get('questionFiles.*') as $message) {
                //
                array_push($err, (object) array('source' => 'questionFiles', 'detail' => $message));
            }
            return $this->respondValidationError($err);
        }

        $question = $user->questions()->create([
            'title' => $request->title,
            'body' => $request->body,
            'subject_id' => $request->subject_id,
            'slug' => Str::slug($request->title)
        ]);


        if ($request->hasfile('questionFiles')) {
            foreach ($request->file('questionFiles') as $file) {
                $name = $question->id . '_' . md5(uniqid()) . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('questions/files', $file, $name);
                $questionFile = $question->files()->create([
                    'name' => $name,
                    'file_url' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
        $question = Question::with('files')->find($question->id);
        return $this->itemCreatedResponse($question);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $question = Question::where(['slug' => $id])->first();
        if ($question == null) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        return $this->responseWithItem($question);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
