<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'title', 'user_id', 'slug', 'subject_id'];
    protected $with = ['user', 'answers', 'files'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'featured' => 'boolean',
    ];
    /**
     * Each question has a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
