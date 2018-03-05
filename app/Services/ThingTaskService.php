<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ThingTaskService
{

    public function addTask($taskInfo)
    {
        $time = new Carbon();
        DB::table('things')->insert([
            'user_id' => $taskInfo['user_id'],
            'name' => $taskInfo['name'],
            'type' => $taskInfo['type'],
            'picture_url' => $taskInfo['picture_url'],
            'place' => $taskInfo['place'],
            'remarks' => $taskInfo['remarks'],
            'created_at' => $time,
        ]);
    }

    public function acceptTask($taskInfo)
    {
        $time = new Carbon();
        $res = DB::table('tasks')->where('user_id', $taskInfo['user_id'])
            ->where('task_id', $taskInfo['task_id'])
            ->where('task_type', $taskInfo['task_type'])
            ->get();
        if (!$res->first()) {
            DB::table('tasks')->insert([
                'user_id' => $taskInfo['user_id'],
                'task_id' => $taskInfo['task_id'],
                'task_type' => $taskInfo['task_type'],
                'created_at' => $time
            ]);
            return true;
        } else
            return false;


    }

    public function finishTask($taskInfo)
    {
        $time = new Carbon();
        DB::transaction(function () use ($taskInfo, $time) {

            DB::table('things')->where('id', $taskInfo['task_id'])->update([
                'status' => 2,
                'finished_by' => $taskInfo['user_id']
            ]);

            DB::table('tasks')->where('task_type', $taskInfo['task_type'])->where('task_id', $taskInfo['task_id'])->delete();

        });
    }

    public function showTaskList()
    {
        $taskList = DB::table('things')->orderby('status')->get();
        return $taskList;
    }

    public function showTaskById($task_id)
    {
        $taskInfo = DB::table('things')->where('id', $task_id)->first();
        return $taskInfo;
    }

    public function showTaskUser($taskInfo)
    {
        $userList = DB::table('tasks')->where('task_type', $taskInfo['task_type'])->where('task_id', $taskInfo['task_id'])
            ->select('user_id')
            ->get();
        return $userList;
    }
}