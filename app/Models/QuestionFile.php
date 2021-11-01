<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionFile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'file_url', 'file_type'];
}
