<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class CommonLifeController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Get the currently authenticated user

        // Retrieve all tasks with their relationships (users)
        $tasks = Task::with('users')->get();

        // Filter tasks that are in progress (not completed)
        $tasksInProgress = $tasks->filter(function ($task) use ($user) {
            $userPivot = $task->users->firstWhere('id', $user->id)?->pivot;
            return !$userPivot || $userPivot->is_completed == false;
        });

        // Filter tasks completed by the currently logged-in user
        $completedTasksByUser = $tasks->filter(function ($task) use ($user) {
            $userPivot = $task->users->firstWhere('id', $user->id)?->pivot;
            return $userPivot && $userPivot->is_completed == true;
        });

        // Filter tasks completed by all students (for admins)
        $completedTasks = $tasks->filter(function ($task) {
            return $task->users->contains(function ($user) {
                return $user->pivot->is_completed == true;
            });
        });

        // Return the view with the necessary data
        return view('pages.commonLife.index', [
            'user' => $user,
            'tasksInProgress' => $tasksInProgress,
            'completedTasksByUser' => $completedTasksByUser,
            'completedTasks' => $completedTasks,
        ]);
    }
}
