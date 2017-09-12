<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function _initialize(){
//        session("admin.uid",1);
//        session("admin.current_menu",3);
        
        if(!session("admin.uid")){
            $url = "/admin/login";
            if(\think\Request::instance()->isAjax()){
                $this->error("请登录后操作",$url);
            }else{
                $this->redirect($url);
            }
        }
    }
    
    /**
     * 存储当前菜单id
     * @param int $menu_id 菜单id
     */
    public function save_current_menu($menu_id){        
        //当前菜单id存入session，写入日志时使用
        session("admin.current_menu",$menu_id);
    }
    
    /**
     * 写入操作日志
     * @param array $content 日志内容
     */
    public function add_log($content){
        if(!$content){
            return ['code' => 1, 'msg' => '日志内容不能为空'];
        }
        $res = db('AdminLog')->insert([
            'admin_user_id' => session("admin.uid"),
            'log_time' => time(),
            'menu_id' => session("admin.current_menu"),
            'content' => json_encode($content),
        ]);
        if($res){
            return ['code' => 0, 'msg' => '写入日志成功'];
        }else{
            return ['code' => 2, 'msg' => '写入日志失败'];
        }
    }
}
