<?php

namespace App\Http\Controllers;

use App\Models\Task; 
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year_start' => 'required|date',
            'year_end' => 'required|date',
        ]);

        $task = new Task();
        $task->name = $validatedData['name'];
        $task->description = $validatedData['description'] ?? null;
        $task->year_start = $validatedData['year_start'];
        $task->year_end = $validatedData['year_end'];
        $task->save();

        return redirect()->back()->with('success', 'Tâche ajoutée avec succès.');
    }


    public function edit(Task $task)
    {
        $user = auth()->user();
        if ($user->userSchool->role !== 'admin') {
            abort(403);
        }

        return view('pages.commonLife.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'year_start' => 'required|date',
            'year_end' => 'required|date',
        ]);

        $task->update($request->only(['name', 'description', 'year_start', 'year_end']));

        return redirect()->route('common-life.index')->with('success', 'Tâche mise à jour.');
    }

    public function destroy(Task $task)
    {
        $user = auth()->user();
        if ($user->userSchool->role !== 'admin') {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Tâche supprimée.');
    }

    public function complete(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();

        $user->tasks()->syncWithoutDetaching([
            $task->id => [
                'is_completed' => true,
                'comment' => $request->input('comment'),
            ]
        ]);

        return redirect()->back()->with('success', 'Tâche marquée comme terminée.');
    }

}

