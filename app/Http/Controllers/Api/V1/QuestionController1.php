<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\JsonApi\V1\Questions\QuestionQuery;
use App\JsonApi\V1\Questions\QuestionRequest;
use App\JsonApi\V1\Questions\QuestionSchema;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;
use Illuminate\Support\Str;

class QuestionController1 extends Controller
{

    use Actions\FetchMany;
    use Actions\FetchOne;
    use Actions\Update;
    use Actions\Destroy;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
    use Actions\UpdateRelationship;
    use Actions\AttachRelationship;
    use Actions\DetachRelationship;
    /**
     * Create a new resource.
     *
     * @param QuestionSchema $schema
     * @param QuestionRequest $request
     * @param QuestionQuery $query
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\Response
     */
    public function store(QuestionSchema $schema, QuestionRequest $request, QuestionQuery $query)
    {
        $validated = $request->validated();
        $model = $schema
            ->repository()
            ->create()
            ->withRequest($query)
            ->store(array_merge($validated, ['slug' => Str::slug($validated['title'], '-')]));

        $model = $model->find($model->id);
        // do something custom...

        return new DataResponse($model);
    }
}
