<?php

namespace App\JsonApi\V1\Questions;

use App\Models\Question;
use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class QuestionRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {

        $unique = Rule::unique('questions');

        /** @var \App\Models\Question|null $question */
        if ($question = $this->model()) {
            $unique->ignore($question);
        }
        return [
            //user_id
            //title
            //body
            //views
            //image
            //slug
            //active
            //featured
            // 'author' => JsonApiRule::toOne(),
            'title' => ['required', 'string', $unique],
            'body' => ['required', 'string'],
            'user_id' => ['integer', 'exists:users,id'],
            // 'tags' => JsonApiRule::toMany(),
            // 'name' => 'required|string',
            // 'password' => "required|string",
            // 'passwordConfirmation' => "required_with:password|same:password"
        ];
    }

    /**
     * Modify the existing resource before it is merged with client values.
     *
     * @param \App\Models\Question $model
     * @return array|null
     */
    protected function withExisting(Question $model, array $resource): ?array
    {
        $resource['attributes']['user_id'] = 21;
        // dd($resource['attributes']['user_id']);
        return $resource;
    }
}
