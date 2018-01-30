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
                'password' => bcrypt($userInfo['password']),
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
        $rules = ['name','mobile','avatar','sex','wechat_id','qq_id','birth','status','stuwithcard_pic','id_pic','stucard_pic','level','exp'];
        foreach ($rules as $key) {
            $data[$key] = $user->$key;
        }
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
        elseif (!Hash::check($data['password'], $user->password))
            return -2;
        else
            return $user->id;
    }


}