<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'title', 'user_id', 'slug'];
    protected $with = ['answers', 'files'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];

    // user has many answers
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
    // user has many files
    public function files()
    {
        return $this->hasMany(QuestionFile::class);
    }
}
