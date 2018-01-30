<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-22
 * Time: 上午10:21
 */

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function register($userInfo)
    {
        if ($this->isUserExist($userInfo))
        {
            return false;
        }
        else
        {
            $time=new Carbon();
            //name 默认昵称为登录号码
            DB::table('users')->insert([
                'name' => $userInfo['value'],
                $userInfo['type'] => $userInfo['value'],
                'password' => isset($userInfo['password'])?bcrypt($userInfo['password']):null,
                'created_at'=> $time,
            ]);
            return true;
        }
    }

    public function isUserExist($data)
    {
        $userId = $this->getUserId($data);
        if($userId > 0)
            return true;
        else
            return false;
    }

    public function getUserId($data)
    {
        $userId = DB::table('users')->where($data['type'], $data['value'])->value('id');
        return $userId;
    }

    public function updateUserInfo($userInfo)
    {
        $time=new Carbon();
        $userInfo = array_merge($userInfo,[
            'updated_at' => $time,
        ]);
        if (DB::table('users')->where('id', $userInfo['id'])->update($userInfo))
            return true;
        else
            return false;
    }

    public function getUserInfo($userId)
    {
        $user = DB::table('users')->where('id', $userId)->first();
        $data = [];
        //取可以展示的信息
        $rules = ['name','mobile','avatar','sex','wechat_id','qq_id','birth','status','stuwithcard_pic','id_pic','stucard_pic','level','exp'];
        foreach ($rules as $key) {
            $data[$key] = $user->$key;
        }
        //取前10位 即 1997-07-01 00:00:00 中的年月日
        if($data['birth'])
            $data['birth'] = substr($data['birth'],0,10);
        return $data;
    }

    // identifier 1. mobile 2. weixin 3. qq
    public function login($data)
    {
        $user  = DB::table('users')->where($data['type'], $data['value'])->first();
//        dd(Hash::check($data['password'], $user->password));
        if ($user == null)
            return -1;
        elseif ((isset($user->password)?(!Hash::check($data['password'], $user->password)):false))
            return -2;
        else
            return $user->id;
    }

    public function binding($data)
    {
        // 只允许绑定手机号
        $id = $this->getUserId($data);
        if ($id > 0)
            return -2;
        $user  = DB::table('users')->where('id', $data['user_id'])->first();
        // 这样写才能访问到对象的键值
        $type = $data['type'];
        if ($user == null)
            return -1;
        elseif ($user ->$type != null)
            return 0;
        else
        {
            $Info =[
                $data['type'] => $data['value']
            ];
            if($data['type'] == 'mobile')
                $Info = array_merge($Info,[
                    'password' => bcrypt($data['password']),
                ]);
            DB::table('users')->where('id', $data['user_id'])->update($Info);
            return 1;
        }
    }

    public function addAuthInfo($data)
    {
        $user  = DB::table('users')->where('id', $data['user_id'])->first();
        if(!isset($user->mobile))
            return -2;
        elseif($user->status == 1)
            return -1;
        elseif($this->updateUserInfo([
            'id' => $data['user_id'],
            'stuwithcard_pic' => $data['stuwithcard_pic'],
            'id_pic' => $data['id_pic'],
            'stucard_pic' => $data['stucard_pic']
        ]))
            return 1;
        else
            return 0;
    }




}