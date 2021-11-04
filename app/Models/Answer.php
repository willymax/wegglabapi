<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $with = ['user'];
    protected $fillable = ['body', 'user_id', 'question_id'];

    // question belongs to one question
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
    // each question belongs to a given user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // user has many files
    public function files()
    {
        return $this->hasMany(AnswerFile::class);
    }
}
