<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $rules = [
            'type' => 'bail|required|in:mobile,wechat_id,qq_id',
            'value' => 'required',
            'password' => 'required|min:6|max:20'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        else {
            $userInfo=ValidationHelper::getInputData($request, $rules);
            $result = $this ->userService->register($userInfo);
            if (!$result) {
                return response()->json([
                    'code' => 6002,
                    'message' => '用户已注册'
                ]);
            }
            else {
                $userId = $this -> userService->getUserId($userInfo);
                return response()->json([
                    'code'=> 6000,
                    'message' => '注册成功',
                    'data' => [
                        'user_id' => $userId
                    ]
                ]);
            }
        }
    }

    public function login(Request $request)
    {
        $rules = [
            'type' => 'bail|required|in:mobile,wechat_id,qq_id',
            'value' => 'required',
            'password' => 'required|min:6|max:20'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        $userId = $this->userService->login($request->all());
        //$tokenStr = $this->tokenService->makeToken($user->id, $request->ip());
        if ($userId == -1) {
            return response()->json([
                'code' => 6003,
                'message' => '用户未注册'
            ]);
        }
        elseif($userId == -2){
            return response()->json([
                'code' => 6004,
                'message' => '密码错误'
            ]);
        }
        else{
            return response()->json([
                'code' => 6000,
                'message' => '登陆成功',
                'data' => [
                    'user_id' => $userId,
                    //'token' => $tokenStr,
                ]
            ]);
        }

    }

    public function getUserInfo($userId)
    {
        $userInfo = $this->userService->getUserInfo($userId);
        return response()->json([
        'code' => 6000,
        'message' => '请求成功',
        'data' => $userInfo
    ]);
    }

    public function updateUserInfo(Request $request)
    {
        $rules = [
            'id' => 'required',
            'avatar' => 'required',
            'name' => 'required',
            'sex' => 'required',
            'birth' => 'required'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        $userInfo=ValidationHelper::getInputData($request, $rules);
        if($this->userService->updateUserInfo($userInfo))
            return response()->json([
                'code' => 6000,
                'message' => '请求成功',
                'data' => $userInfo
            ]);
        else
            return response()->json([
                'code' => 6005,
                'message' => '更新失败',
                'data' => $userInfo
            ]);
    }
}
