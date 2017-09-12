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
     * 写入操作日志
     * @param int $menu_id 菜单id
     * @param array $content 日志内容
     */
    public function add_log($menu_id, $content){
        if(!$content || !$menu_id){
            return ['code' => 1, 'msg' => '参数不完整'];
        }
        $res = db('AdminLog')->insert([
            'admin_user_id' => session("admin.uid"),
            'log_time' => time(),
            'menu_id' => $menu_id,
            'content' => json_encode($content),
        ]);
        if($res){
            return ['code' => 0, 'msg' => '写入日志成功'];
        }else{
            return ['code' => 2, 'msg' => '写入日志失败'];
        }
    }
}
