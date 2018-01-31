<?php
/**
 * Created by PhpStorm.
 * User: andyhui
 * Date: 18-1-31
 * Time: 下午5:50
 */

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TokenService
{
    private static  $EXPIRE_TIME = 10800000; // 3小时

    public function createToken(int $userId,string $ip):string
    {
        $tokenStr = md5(uniqid());
        $time = new Carbon();
        $data = [
            'user_id' => $userId,
            'token' => $tokenStr,
            'created_at' => $time,
            'updated_at' => $time,
            'expires_at' => $time + self::$EXPIRE_TIME,
            'ip' => $ip
        ];
        DB::table('tokens')->insert($data);
        return $tokenStr;
    }

    private function updateToken(int $userId,string $ip):string
    {
        $time = new Carbon();
        $tokenStr = md5(uniqid());
        $data = [
            'token' => $tokenStr,
            'updated_at' => $time,
            'expires_at' => $time+self::$EXPIRE_TIME,
            'ip' => $ip
        ];

        DB::table('tokens')->where('user_id', $userId)->update($data);
        return $tokenStr;
    }

    public function makeToken(int $userId,string $ip):string
    {
        $user  = DB::table('users')->where('id', $userId)->first();

        if($user == null)
            return -1;
        $token  = DB::table('tokens')->where('id', $userId)->first();

        if($token == null)
        {
            return $this->createToken($userId,$ip);
        }
        else
        {
            return $this->updateToken($userId,$ip);
        }
    }

    public function deleteToken($userId)
    {
        DB::table('tokens')->where('user_id', $userId)->delete();
    }

    public function getToken($tokenStr)
    {
        return DB::table('tokens')->where('token',$tokenStr)->first();
    }

    public function verifyToken($tokenStr)
    {
        $res = $this->getToken($tokenStr);
        if($res == null)
            return -1;
        else{
            $time = new Carbon();
            if ($res->expired_at > $time) {
                return 1;
            } else {
                return 0;
            }
        }

    }
}