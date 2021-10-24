<?php

namespace App\JsonApi\V1\Questions;

use App\Models\Question;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Fields\Number;
use LaravelJsonApi\Eloquent\Fields\Boolean;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class QuestionSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Question::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        $user = auth()->user();
        return [
            ID::make(),
            Number::make('user_id')->deserializeUsing(
                static fn ($value) => $user->id
            ),
            HasMany::make('answers')->readOnly(),
            Str::make('body'),
            Str::make('title'),
            // Number::make('views'),
            Str::make('image'),
            Str::make('slug'),
            Boolean::make('active'),
            Boolean::make('featured'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }
}
