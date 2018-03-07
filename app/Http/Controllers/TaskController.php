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
        foreach ($waterTask as $item) {
            $item->user_name='lihua';
        }
        dd($waterTask);
    }
}
