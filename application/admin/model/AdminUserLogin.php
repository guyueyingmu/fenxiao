<?php
namespace app\admin\model;

use think\Model;

class AdminUserLogin extends Model
{
    public static function add_log($user_name, $add_type){
        return self::create([
            'user_name'  =>  $user_name,
            'login_time' =>  time(),
            'status' =>  $add_type
        ]);
    }
}