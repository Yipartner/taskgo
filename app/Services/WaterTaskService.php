<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WaterTaskService
{
    public function addTask($taskInfo)
    {
        $time=new Carbon();
        DB::table('water_tasks')->insert([
            'user_id' => $taskInfo['user_id'],
            'address' => $taskInfo['address'],
            'type' => $taskInfo['type'],
            'created_at'=> $time,
        ]);
    }
    public function finishTask($taskId)
    {
        DB::table('water_tasks')->where('id',$taskId)->update([
            'status' => 2
        ]);
    }
    public function acceptTask($taskId)
    {
        DB::table('water_tasks')->where('id',$taskId)->update([
            'status' => 1
        ]);
    }
    public function taskNum(){
        $num=DB::table('water_tasks')->where('status',0)->count();
        return $num;
    }
    public function showTask(){
        $tasks=DB::table('water_tasks')->where('status',0)->get();
        return $tasks;
    }
    public function showTaskByUser($userId){
        $tasks=DB::table('water_tasks')->where('user_id',$userId)->get();
        return $tasks;
    }
    public function showTaskByUserAndStatus($user_id,$status){
        $taskList=DB::table('water_tasks')->where([
            ['user_id','=',$user_id],
            ['status','=',$status]
        ])->get();
        return $taskList;
    }
    public function showTaskByStatus($status){
        $tasks=DB::table('water_tasks')->where('status',$status)->get();
        return $tasks;
    }
    public function acceptAllTask(){
        DB::table('water_tasks')->where('id','>',0)->update([
            'status' => 1
        ]);
    }
}