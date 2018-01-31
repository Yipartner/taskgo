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
        $rules=[
            'user_id' => 'required',
            'address' =>'required',
            'type'    =>'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 1001,
                'message' => '表单验证失败'
            ]);
        }
        else{
            $taskInfo=ValidationHelper::getInputData($request,$rules);
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
                'taskList'=> $taskList
        ]);
        }
        else{
            return response()->json([
                'code' =>1002,
                'taskList'=> null
            ]);
        }
    }
    public function showTaskByUser($userId){
        $taskList=$this->waterService->showTaskByUser($userId);
        if ($taskList->first()) {
            return response()->json([
                'code' => 1000,
                'taskList'=> $taskList
            ]);
        }
        else{
            return response()->json([
                'code' =>1002,
                'taskList'=> null
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
    public function finishTask($taskId){
        $this->waterService->finishTask($taskId);
        return response()->json([
            'code' => 1000,
            'message' => '任务完成'
        ]);
    }

}
