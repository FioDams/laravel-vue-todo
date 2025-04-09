<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->tasks()->orderBy('order')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return $request->user()->tasks()->create($request->all());

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        // Vérification des droits
        if ($request->user()->cannot('update', $task)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task->update($request->all());

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Task $task)
    {
        // méthode manuelle pour vérifier les droits
        if ($request->user()->cannot('delete', $task)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        $tasks = $request->input('tasks');

        foreach ($tasks as $index => $task) {
            Task::where('id', $task['id'])->update(['order' => $index]);
        }

        return response()->json(['message' => 'Tasks reordered successfully']);
    }
}
