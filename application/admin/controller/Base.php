<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function _initialize(){
//        if(!session("admin.uid")){
//            $url = "/admin/login";
//            if(\think\Request::instance()->isAjax()){
//                $this->error("请登录后操作",$url);
//            }else{
//                $this->redirect($url);
//            }
//        }
    }
}
