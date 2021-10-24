<?php

namespace App\JsonApi\V1\Answers;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class AnswerResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'user_id' => $this->user_id,
            'body' => $this->body,
            'price' => $this->price,
            'image' => $this->image,
            'active' => $this->active,
            'featured' => $this->featured,
            // 'question' => $this->question,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('question'),
        ];
    }
}
