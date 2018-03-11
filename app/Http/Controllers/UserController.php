<?php

namespace App\Http\Controllers;

use App\Services\TokenService;
use App\Services\UserService;
use App\Tool\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    private $tokenService;

    public function __construct(UserService $userService,TokenService $tokenService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    public function register(Request $request)
    {
        $rules = [
            'type' => 'bail|required|in:mobile,wechat_id,qq_id',
            'value' => 'required'
        ];
        if($request->type == 'mobile')
        {
            $rules = array_merge($rules,[
                'password' => 'required|min:6|max:20',
            ]);
            if(!$this->userService->checkCaptcha($request->value,$request->captcha))
            {
                return response()->json([
                    'code' => 6013,
                    'message' => '验证码错误'
                ]);
            }
        }
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
        $tokenStr = $this->tokenService->makeToken($userId, $request->ip());
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
                    'token' => $tokenStr,
                ]
            ]);
        }

    }

    public function logout(Request $request)
    {
        $this->tokenService->deleteToken($request->user->id);

        return response()->json([
            'code' => 6000,
            'message' => '退出成功'
        ]);
    }

    public function checkToken(Request $request)
    {
        $checkRes = $this->tokenService->verifyToken($request->token);
        if($checkRes == -1)
            return response()->json([
                'code' => 6011,
                'message' => 'token不存在'
            ]);
        else if($checkRes == 0)
            return response()->json([
                'code' => 6012,
                'message' => 'token过期'
            ]);
        else
            return response()->json([
                'code' => 6000,
                'message' => '验证通过'
            ]);
    }

    public function getUserInfo(Request $request)
    {
        $userId = $request->user->id;
        $userInfo = $this->userService->getUserInfo($userId);
        return response()->json([
        'code' => 6000,
        'message' => '请求成功',
        'data' => $userInfo
    ]);
    }

    public function getUserInfoById(Request $request)
    {
        $userId = $request->user_id;
        $allUserInfo = $this->userService->getUserInfo($userId);
        $userInfo = [];
        $rules = ['name','avatar','sex','mobile','followers_count','followings_count'];
        foreach ($rules as $key) {
            $userInfo[$key] = $allUserInfo[$key];
        }        return response()->json([
            'code' => 6000,
            'message' => '请求成功',
            'data' => $userInfo
        ]);
    }

    public function updateUserInfo(Request $request)
    {
        $rules = [
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
        $userId = $request->user->id;
        $userInfo['id'] = $userId;
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
        $userId = $request->user->id;
        $data['user_id'] = $userId;
        $code = $this->userService->binding($data);
        if($code == -2)
            return response()->json([
                'code' => 6007,
                'message' => '该号码已被绑定'
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
        $userId = $request->user->id;
        $data['user_id'] = $userId;
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
        $code = $this->userService->updateAuthStatus($request->user->id);
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

    public function updateLevel(Request $request)
    {
        if($this->userService->updateLevel($request->all()))
            return response()->json([
                'code' => 6000,
                'message' => '更新成功'
            ]);
        else
            return response()->json([
                'code' => 6005,
                'message' => '更新等级失败'
            ]);
    }

    public function resetPassword(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|max:20'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        $data=ValidationHelper::getInputData($request, $rules);
        $userId = $request->user->id;
        $data['user_id'] = $userId;
        if($this->userService->resetPassword($data))
            return response()->json([
                'code' => 6000,
                'message' => '密码修改成功'
            ]);
        else
            return response()->json([
                'code' => 6005,
                'message' => '密码修改失败,原密码错误'
            ]);
    }

    public function forgotPassword(Request $request)
    {
        $rules = [
            'mobile' =>'required',
            'captcha' => 'required',
            'new_password' => 'required|string|min:6|max:20'
        ];
        $validator =ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()){
            return response()->json([
                'code' => 6001,
                'message' => '表单验证失败'
            ]);
        }
        if(!$this->userService->checkCaptcha($request->mobile,$request->captcha))
        {
            return response()->json([
                'code' => 6013,
                'message' => '验证码错误'
            ]);
        }
        $this->userService->forgotPassword($request->mobile,$request->new_password);
        return response()->json([
            'code' => 6000,
            'message' => '密码重置成功'
        ]);
    }

    public function sendMessage(Request $request)
    {
        $mobile=$request->mobile;

        $data=[
            'account' => env('MESSAGE_ACCOUNT'),
            'pswd' => env('MESSAGE_PASSWORD'),
            'mobile' => $mobile
        ];

        $header = "【NEUQer】";
        $captcha = rand(1000,9999);
        $msg="您的验证码为".$captcha."，此验证码用于taskgo注册或忘记密码。";

        $newMsg = $header.$msg;
        $url =  "http://zapi.253.com/msg/HttpBatchSendSM?".http_build_query($data)."&msg=".$newMsg;
        $res=''.$this->doCurlGetRequest($url);
        $code = explode(',',$res)[1];
        if($code == 0)
        {
            $this->userService->addCaptcha($mobile,$captcha);
            return response()->json([
                'code' => 6000,
                'message' => '验证码发送成功'
            ]);
        }
        else
        {
            return response()->json([
                'code' => 6010,
                'message' => '验证码发送失败'
            ]);
        }
    }

    public function doCurlGetRequest(string $url)
    {
        $con = curl_init($url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($con, CURLOPT_TIMEOUT, 5);
        return curl_exec($con);
    }

    public function getFollowers(Request $request)
    {
        $userId = $request->user->id;
        $users = $this->userService->getFollowers($userId);
        return response()->json([
            'code'=> 6000,
            'message' => '关注列表为',
            'data' => $users
        ]);
    }

    public function getFollowings(Request $request)
    {
        $userId = $request->user->id;
        $users = $this->userService->getFollowings($userId);
        return response()->json([
            'code'=> 6000,
            'message' => '粉丝列表为',
            'data' => $users
        ]);
    }

    public function followUser(Request $request)
    {
        $userId = $request->user->id;
        $TofollowerId = $request->follower_id;
        if ($userId == $TofollowerId) {
            return response()->json([
                'code' => 6014,
                'message' => '用户不能关注自己'
            ]);
        }
        if($this->userService->addFollower($userId,$TofollowerId))
        {
            return response()->json([
                'code' => 6000,
                'message' => '关注成功'
            ]);
        }
        else
        {
            return response()->json([
                'code' => 6015,
                'message' => '已经关注该用户'
            ]);
        }
    }

    public function unFollowUser(Request $request)
    {
        $userId = $request->user->id;
        $TofollowerId = $request->follower_id;

        if ($this->userService->deleteFollower($userId, $TofollowerId)) {
            return response()->json([
                'code' => 6000,
                'message' => '取消关注成功'
            ]);
        } else {
            return response()->json([
                'code' => 6015,
                'message' => '未关注该用户'
            ]);
        }
    }
}
