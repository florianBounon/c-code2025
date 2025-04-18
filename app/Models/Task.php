<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'year_start',
        'year_end',
    ];

    protected $casts = [
        'year_start' => 'date',
        'year_end' => 'date',
    ];


    /**
     * This function retrieves the Task's role from the task_user table
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('is_completed', 'comment')->withTimestamps();
    }



    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
    }

}
