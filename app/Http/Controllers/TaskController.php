<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TasksResource;
use App\Traits\HttpResponses;

class TaskController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TasksResource::collection(
            // Task::where('user_id', Auth::user()->id)->get(),
            $this->taskService->usreTasks(auth()->user())
        );
    }



    // public function userTasks(User $user)
    // {
    //     return $user->tasks();
    // }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated();
        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
        ]);
        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TasksResource($task);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task) // store
    {
        if(Auth::user()->id !== $task->user_id) {
            return $this-> error('', 'you are not authorized to make this request', 403);
        }

        $task->update($request->all());
        return $this->success([
            'task' => TasksResource::make($task)
        ])
        // return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();

    }
    private function isNotAuthorized($task) {
        if(Auth::user()->id !== $task->user_id) {
            return $this-> error('', 'you are not authorized to make this request', 403);
        }
    }
}
