<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function task($id)
    {
        $tasks = Task::where('user_id', $id)->get();

        $formattedTasks = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'created_at' => Carbon::parse($task->updated_at)->format('Y-m-d'),
                'task' => $task->task,
                'description' => $task->description,
                'link' => $task->link,
                'is_completed' => $task->is_completed,
                
                'due_date' => $task->due_date,
            ];
        });
        $formattedTasks = $formattedTasks->sortBy('updated_at');

        return response()->json($formattedTasks);
        
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:255',
            'due_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors(),
            ], 422);
        }

        try {
            Task::create([
                'task' => $request->task,
                'description' => $request->description,
                'user_id' => $request->user_id,
                'link' => $request->link,
                'is_completed' => false,
                'due_date' => $request->due_date,
            ]);
            return response()->json(['status', 'Task created successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Unable to create task"
            ], 400);
        }
    }

    public function show($searchId, $userId)
    {
        $searchedTasks = Task::where('task', 'id', 'like', '%' . $searchId . '%')
            ->where('user_id', $userId)
            ->get();

        if ($searchedTasks->isEmpty()) {
            return response()->json(['error' => 'No matching tasks found'], 404);
        }

        return response()->json($searchedTasks);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $task = Task::findOrFail($id);
        $task->task = $input['task'];
        $task->description = $input['description'];
        $task->link = $input['link'];
        $task->is_completed = $request->is_completed;
        $task->due_date = $input['due_date'];

        $task->save();

        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json($task, 200);
    }
}
