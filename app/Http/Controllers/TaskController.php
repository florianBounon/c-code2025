<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Auth;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'year_start' => 'required|date',
            'year_end' => 'required|date',
            'promotions' => 'required|array',
            'promotions.*' => 'exists:promotions,id',
        ]);

        // Create the task
        $task = Task::create($request->only(['name', 'description', 'year_start', 'year_end']));

        // Attach the task to the selected promotions
        $task->promotions()->attach($request->input('promotions'));

        return redirect()->route('commonLife.index')->with('success', 'Task successfully added.');
    }

    public function edit(Task $task)
    {
        // Check if the current user is an admin
        $user = auth()->user();
        if ($user->userSchool->role !== 'admin') {
            abort(403);
        }

        return view('pages.commonLife.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year_start' => 'required|date',
            'year_end' => 'required|date',
        ]);

        // Update the task details
        $task->update($request->only(['name', 'description', 'year_start', 'year_end']));

        // Sync the task with the selected promotions
        $task->promotions()->sync($request->input('promotions'));

        return redirect()->route('common-life.index')->with('success', 'Task successfully updated.');
    }

    public function destroy(Task $task)
    {
        // Check if the current user is an admin
        $user = auth()->user();
        if ($user->userSchool->role !== 'admin') {
            abort(403);
        }

        // Delete the task
        $task->delete();

        return redirect()->back()->with('success', 'Task successfully deleted.');
    }

    public function complete(Request $request, Task $task)
    {
        // Validate the comment input
        $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Mark the task as completed for the user and add the comment
        $user->tasks()->syncWithoutDetaching([
            $task->id => [
                'is_completed' => true,
                'comment' => $request->input('comment'),
            ]
        ]);

        return redirect()->back()->with('success', 'Task marked as completed.');
    }

    public function index()
    {
        $user = auth()->user();

        $tasksInProgress = Task::whereHas('promotions', function ($query) use ($user) {
            $query->whereIn('id', $user->promotions->pluck('id'));
        })->get();

        

        return view('pages.vie-commune.index', compact('tasksInProgress', 'user'));
    }

}

