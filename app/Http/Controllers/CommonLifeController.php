<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Promotion;

class CommonLifeController extends Controller
{
    /**
     * Display the common life tasks page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Get all tasks with related users and promotions
        $tasks = Task::with(['users', 'promotions'])->get();

        if ($user->userSchool->role === 'student') {
            // Get the promotions linked to the user
            $userPromotions = $user->promotions;

            // Filter tasks where at least one promotion matches the user's promotions
            $tasks = $tasks->filter(function ($task) use ($userPromotions) {
                return $task->promotions->intersect($userPromotions)->isNotEmpty();
            });
        }

        // Tasks that are in progress (not completed by the user)
        $tasksInProgress = $tasks->filter(function ($task) use ($user) {
            $pivot = $task->users->firstWhere('id', $user->id)?->pivot;
            return !$pivot || $pivot->is_completed == false;
        });
        

        // Tasks completed by the current user
        $completedTasksByUser = $tasks->filter(function ($task) use ($user) {
            // Use the pivot values here directly
            $userTask = $task->users->firstWhere('id', $user->id);
            return $userTask && $userTask->pivot->is_completed;
        });

        // For admin: tasks completed by at least one student
        // Here, we use the whereHas method with a custom filter
        $completedTasks = Task::with(['users', 'promotions'])
            ->whereHas('users', function ($q) {
                // Check if the user in the pivot table has completed the task
                $q->where('task_user.is_completed', true); // Explicitly specify the table name in where clause
            })
            ->get();

        return view('pages.commonLife.index', [
            'user' => $user,
            'tasksInProgress' => $tasksInProgress,
            'completedTasksByUser' => $completedTasksByUser,
            'completedTasks' => $completedTasks,
        ]);
    }
}
