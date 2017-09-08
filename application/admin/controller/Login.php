<?php
namespace app\admin\controller;

use think\captcha\Captcha;

class Login
{
	 public function index()
    {
       	return view();
    }
    public function get_verify(){        
        $captcha = new Captcha();
        return $captcha->entry();
    }
    
    public function log_in()
    {
    }
}
