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
            'value' => 'required'
        ];
        if($request->type == 'mobile')
            $rules = array_merge($rules,[
                'password' => 'required|min:6|max:20',
            ]);
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
            'value' => 'required'
        ];
        if($request->type == 'mobile')
            $rules = array_merge($rules,[
                'password' => 'required|min:6|max:20',
            ]);
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
                'message' => '用户不存在'
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
                'message' => '更新成功'
            ]);
        else
            return response()->json([
                'code' => 6005,
                'message' => '更新失败'
            ]);
    }

    public function bindLoginAccount(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'type' => 'bail|required|in:mobile,wechat_id,qq_id',
            'value' => 'required'
        ];
        if($request->type == 'mobile')
            $rules = array_merge($rules,[
                'password' => 'required|min:6|max:20',
            ]);
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        $data=ValidationHelper::getInputData($request, $rules);
        $code = $this->userService->binding($data);
        if($code == -2)
            return response()->json([
                'code' => 6007,
                'message' => '该号码已绑定'
            ]);
        elseif($code == -1)
            return response()->json([
                'code' => 6003,
                'message' => '用户不存在'
                ]);
        elseif($code == 0)
            return response()->json([
                'code' => 6006,
                'message' => '用户已绑定该登录方式'
            ]);
        else
            return response()->json([
                'code' => 6000,
                'message' => '绑定'.$data['type'].'成功'
            ]);
    }

    public function addAuthInfo(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'stuwithcard_pic' => 'required',
            'id_pic' => 'required',
            'stucard_pic' => 'required'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        $data=ValidationHelper::getInputData($request, $rules);
        $code = $this->userService->addAuthInfo($data);
        if($code == -2)
            return response()->json([
                'code' => 6009,
                'message' => '请先绑定手机号'
            ]);
        elseif($code == -1)
            return response()->json([
                'code' => 6008,
                'message' => '用户已认证无需重复认证'
            ]);
        elseif($code == 0)
            return response()->json([
                'code' => 6005,
                'message' => '更新认证信息失败'
            ]);
        else
            return response()->json([
                'code' => 6000,
                'message' => '更新成功'
            ]);
    }

    public function updateAuthStatus(Request $request)
    {
        $code = $this->userService->updateAuthStatus($request->user_id);
        if ($code == -1)
            return response()->json([
                'code' => 6008,
                'message' => '用户已认证无需重复认证'
            ]);
        elseif($code == 1)
            return response()->json([
                'code' => 6000,
                'message' => '认证成功'
            ]);
        else
            return response()->json([
                'code' => 6010,
                'message' => '认证失败,请补全信息'
            ]);
    }
}
