<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    // question belongs to one question
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
