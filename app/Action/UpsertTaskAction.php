<?php

namespace App\Action;

use App\DataTransferObjects\TaskData;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class UpsertTaskAction
{
    public function execute(TaskData $taskData, Task $task): Task
    {
        $task->name = $taskData->name;
        $task->frequency_id = $taskData->frequency->id;
        $task->user_id = Auth::id();
        $task->status = $taskData->status;

        $task->save();

        return $task;

    }
}
