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
        if (DB::table('users')->where('id', $userInfo['userId'])->update($userInfo))
            return true;
        else
            return false;
    }

    public function getUserInfo($userId)
    {
        return  DB::table('users')->where('id', $userId)->first();
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