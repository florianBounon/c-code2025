<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Result;

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

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_questionnaire');
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

}