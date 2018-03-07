<?php

namespace App\Http\Controllers;

use App\Services\ThingTaskService;
use App\Services\UserService;
use App\Services\WaterTaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    //
    private $thingService;
    private $waterService;
    private $userService;

    public function __construct(ThingTaskService $thingTaskService, WaterTaskService $waterTaskService, UserService $userService)
    {
        $this->thingService = $thingTaskService;
        $this->waterService = $waterTaskService;
        $this->userService = $userService;
    }

    public function showTaskByUserAndStatus(Request $request)
    {
        $user_id = $request->user->id;
        $status = $request->task_status;
        $thingTask = $this->thingService->showTaskByUserAndStatus($user_id, $status);
        $waterTask = $this->waterService->showTaskByUserAndStatus($user_id, $status);
        if ($waterTask->first()) {
//        foreach ($waterTask as $item) {
//            $userInfo=$this->userService->getSimpleUserInfo($item->user_id);
//            $item->user_name=$userInfo->name;
//            $item->user_avatar=$userInfo->avatar;
//        }
        }
        $waterTask = $waterTask->toArray();
        if ($thingTask->first()) {
//        foreach ($thingTask as $item) {
//            $userInfo=$this->userService->getSimpleUserInfo($item->user_id);
//            $item->user_name=$userInfo->name;
//            $item->user_avatar=$userInfo->avatar;
//        }
        }
        $thingTask = $thingTask->toArray();
        $totalTask = array_merge($thingTask, $waterTask);
        return response()->json([
            'code' => 1000,
            'data' => $totalTask
        ]);
    }

    public function showAcceptTaskByUserAndStatus(Request $request)
    {
        $user_id = $request->user->id;
        $status = $request->task_status;
        $thingTask=[];
        if ($status == 0) {
            $thingTask=$this->thingService->showTaskByAccepter($user_id)->toArray();
        } else {
            $thingTask = $this->thingService->showFinishTaskByAccepter($user_id)->toArray();
        }
        $waterTask=$this->waterService->showTaskByStatus($status)->toArray();
        $totalTask=array_merge($thingTask,$waterTask);
        return response()->json([
            'code' => 1000,
            'data' => $totalTask
        ]);
    }
}
