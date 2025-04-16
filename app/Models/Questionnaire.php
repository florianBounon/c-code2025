<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'language',
        'difficulty',
        'questions_count',
        'answers_count',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}