<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-22
 * Time: 上午10:21
 */

namespace App\Service;
use Illuminate\Support\Facades\DB;

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
            DB::table('users')->insert([
                $userInfo->type => $userInfo->value,
                'password' => $userInfo->password
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
    public function loginBymobile($data)
    {
        $user  = DB::table('users')->where('mobile', $data['value'])->first();
        if ($user == null)
            return -1;
        elseif($user['password'] != $data['password'])
            return -2;
        else
            return $user->id;
    }

    public function loginByOther($data)
    {
        $user  = DB::table('users')->where($data['type'], $data['value'])->first();
        if ($user == null)
        {
            $newdata = [
                'type' => $data['type'],
                'value' => $data['value'],
                'password' => '123456'
            ];
            if($this->register($newdata))
            {
                return $this->getUserId($newdata);
            }
        }
        elseif($user['password'] != $data['password'])
            return -2;
        else
            return $user->id;
    }


    

}