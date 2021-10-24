<?php

namespace App\JsonApi\V1\Questions;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class QuestionResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        //user_id
        //title
        //body
        //views
        //image
        //slug
        //active
        //featured
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'body' => $this->body,
            'views' => $this->views,
            'image' => $this->image,
            'slug' => $this->slug,
            'active' => $this->active,
            'featured' => $this->featured,
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
            $this->relation('answers'),
        ];
    }
}
