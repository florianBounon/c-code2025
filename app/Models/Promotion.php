<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year', 'school_id'];


    public function school()
    {
        return $this->belongsTo(School::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'promotion_user')->withTimestamps();
    }


    public function questionnaires()
    {
        return $this->belongsToMany(Questionnaire::class, 'promotion_questionnaire');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

}
