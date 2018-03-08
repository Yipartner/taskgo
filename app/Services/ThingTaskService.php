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
            'user_name' => $taskInfo['user_name'],
            'avatar' => $taskInfo['avatar'],
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
                'user_name' => $taskInfo['user_name'],
                'avatar' => $taskInfo['avatar'],
                'task_name'=>$taskInfo['task_name'],
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

    public function showTaskByUserAndStatus($user_id, $status)
    {
        $taskList = DB::table('things')->where([
            ['user_id', '=', $user_id],
            ['status', '=', $status]
        ])->get();
        return $taskList;
    }

    public function showTaskUser($taskInfo)
    {
        $userList = DB::table('tasks')->where('task_type', $taskInfo['task_type'])->where('task_id', $taskInfo['task_id'])
            ->select('user_id', 'user_name', 'avatar')
            ->get();
        return $userList;
    }

    public function showFinishTaskByAccepter($user_id)
    {
        $taskList = DB::table('things')->where('finished_by', $user_id)
            ->select('')
            ->get();
        return $taskList;
    }

    public function showTaskByAccepter($user_id)
    {
        $taskList = DB::table('tasks')->where('tasks.user_id', $user_id)
            ->join('things','tasks.task_id','=','things.id')
            ->select('things.user_id','things.user_name','things.avatar','things.id','things.name','things.type','things.created_at')
            ->get();
        return $taskList;
    }
}