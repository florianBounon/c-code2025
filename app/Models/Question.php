<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'question',
        'difficulty',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}