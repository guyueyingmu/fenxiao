<?php
namespace app\admin\validate;

use think\Validate;

/*
 * 后台用户验证规则
 */
class AdminUser extends Validate
{
    protected $rule = [
            'user_name'  => 'require',
            'password' => 'require',
            'verify' => 'require'
        ];
    
    protected $message = [
            'user_name.require' => '请输入账号',
            'password.require' => '请输入密码',
            'verify.require' => '请输入验证码'
        ];
    
    protected $scene = [
        'login'   =>  ['mobile','user_type','status'],
    ];

}