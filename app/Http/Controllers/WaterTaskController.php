<?php

namespace App\Http\Controllers;

use App\Services\WaterTaskService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class WaterTaskController extends Controller
{
    private $waterService;

    public function __construct(WaterTaskService $waterTaskService)
    {
        $this->waterService=$waterTaskService;
    }

    public function addTask(Request $request){
        $user_id=$request->user->id;
        $user_name=$request->user->name;
        $avatar=$request->user->avatar;
        $rules=[
            'address' =>'required',
            'type'    =>'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => $res->errors()
            ]);
        }
        else{
            $taskInfo=ValidationHelper::getInputData($request,$rules);
            $taskInfo['user_id']=$user_id;
            $taskInfo['user_name']=$user_name;
            $taskInfo['avatar']=$avatar;
            $this->waterService->addTask($taskInfo);
            return response()->json([
                'code' => 1000,
                'message' => '添加成功'
            ]);
        }
    }
    public function showTask(){
        $taskList=$this->waterService->showTask();
        if ($taskList->first()) {
            return response()->json([
                'code' => 1000,
                'data'=> $taskList
        ]);
        }
        else{
            return response()->json([
                'code' =>1002,
                'data'=> null
            ]);
        }
    }
    public function showTaskByStatus($status){
        $taskList=$this->waterService->showTaskByStatus($status);
        if ($taskList->first()) {
            return response()->json([
                'code' => 1000,
                'data'=> $taskList
            ]);
        }
        else{
            return response()->json([
                'code' =>1002,
                'data'=> null
            ]);
        }
    }
    public function showTaskByUser($userId){
        $taskList=$this->waterService->showTaskByUser($userId);
        if ($taskList->first()) {
            return response()->json([
                'code' => 1000,
                'data'=> $taskList
            ]);
        }
        else{
            return response()->json([
                'code' =>1002,
                'data'=> null
            ]);
        }
    }
    public function acceptTask($taskId){
        $this->waterService->acceptTask($taskId);
        return response()->json([
            'code' => 1000,
            'message' => '任务接受'
        ]);
    }
    public function acceptAllTask(){
        $this->waterService->acceptAllTask();
        return response()->json([
            'code' => 1000,
            'message' => '所有任务接受'
        ]);
    }
    public function finishTask($taskId){
        $this->waterService->finishTask($taskId);
        return response()->json([
            'code' => 1000,
            'message' => '任务完成'
        ]);
    }

}
