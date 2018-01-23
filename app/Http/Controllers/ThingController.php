<?php

namespace App\Http\Controllers;

use App\Services\ThingTaskService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class ThingController extends Controller
{
    private $thingService;

    public function __construct(ThingTaskService $thingTaskService)
    {
        $this->thingService = $thingTaskService;
    }

    public function addTask(Request $request)
    {

        $rules = [
            'user_id' => 'required',
            'name' => 'required',
            'type' => 'required',
            'picture_url' => 'required',
            'place' => 'required',
            'remarks' => 'required'
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
            $this->thingService->addTask($taskInfo);
            return response()->json([
                'code' => 1000,
                'message' => '添加成功'
            ]);
        }
    }
    public function acceptTask(Request $request){
        $rules=[
            'user_id' => 'required',
            'task_id' => 'required',
            'task_type' => 'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 2001,
                'message' => $res->errors()
            ]);
        }
        else{
            $taskInfo=ValidationHelper::getInputData($request,$rules);
            $this->thingService->acceptTask($taskInfo);
            return response()->json([
                'code' =>1000,
                'message'=> '任务接受'
            ]);
        }
    }
    public function finishTask(Request $request){
        $rules=[
            'task_id'=>'required',
            'user_id'=>'required',
            'task_type'=>'required'
        ];
        $res=ValidationHelper::validateCheck($request->all(),$rules);
        if ($res->fails()){
            return response()->json([
                'code' => 2001,
                'message' =>$res->errors()
            ]);
        }
        else {
            $taskInfo=ValidationHelper::getInputData($request,$rules);
            $this->thingService->finishTask($taskInfo);
            return response()->json([
                'code' =>1000,
                'message' =>'任务完成'
            ]);
        }
    }
    public function showTaskList(){
        $taskList=$this->thingService->showTaskList();
        return response()->json([
            'code' => 1000,
            'taskList' => $taskList
        ]);
    }
    public function showTaskById($task_id){
        $taskInfo=$this->thingService->showTaskById($task_id);
        return response()->json([
            'code' => 1000,
            'task' => $taskInfo
        ]);
    }
}
