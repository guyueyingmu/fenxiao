<?php
namespace app\admin\controller;

use think\Controller;
use think\captcha\Captcha;

use app\admin\model\AdminUser;
use app\admin\model\AdminUserLogin;
use app\admin\model\AdminUserRole;
use app\admin\model\AdminMenu;

class Login extends Controller
{
    public function index(){
        return view();
    }
    
    public function log_in()
    {
        $data = [
            'user_name'  => input("user_name","","trim"),
            'password' => input("password","","trim"),
            'verify' => input("verify","","trim"),
        ];
        
        //查看用户输错密码次数，输错次数超过8次就不能再登录
        $error_total = AdminUserLogin::where('user_name',$data['user_name'])->where("status",2)->where("FROM_UNIXTIME(login_time,'%Y-%m-%d')",date("Y-m-d"))->count();
        if($error_total > 8){
            $this->error("密码输入多次错误，今天不允许再登录");
        }
        
        $validate_res = $this->validate($data,[
            'user_name'  => 'require',
            'password' => 'require',
            'verify' => 'require|captcha',
        ],[
            'user_name.require' => '请输入账号',
            'password.require' => '请输入密码',
            'verify.require' => '请输入验证码',
            'verify.captcha' => '验证码不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $user_info = AdminUser::get(['user_name' => $data['user_name']]);
        if(!$user_info){
            $this->error("账号不存在");
        }
        if($user_info['password'] != md5($data['password'])){
            //记录密码输入错误记录
            AdminUserLogin::add_log($data['user_name'], 2);
            $this->error("密码输入错误");
        }
        
        if($user_info['status'] == 2){
            $this->error("用户被禁用，请联系管理员");
        }
        
        //记录成功登录记录
        AdminUserLogin::add_log($data['user_name'], 1);
        
        //角色信息
        $role_info = AdminUserRole::get($user_info['role_id']);
        if(!$role_info){
            //记录密码登录错误记录
            AdminUserLogin::add_log($data['user_name'], 2);
            $this->error("用户信息有误");
        }
        
        $where = "status = 1";
        if($role_info['menu_auth'] != 'all'){
            $where .= " AND id IN ({$role_info['menu_auth']})";
        }        
        $menu_list = AdminMenu::where($where)->order("sort DESC,id ASC")->select();
        
        $tree = new \tree\Tree();
        $menu_list = $tree->toTree(json_decode( json_encode( $menu_list),true), 'id', 'pid', '_child');        
//        echo "<pre>";print_r($menu_list);exit;
        
        //保存session
        $session_arr['id'] = $user_info['id'];
        $session_arr['user_name'] = $user_info['user_name'];
        $session_arr['nickname'] = $user_info['nickname'];
        $session_arr['role_id'] = $user_info['role_id'];
        $session_arr['role_name'] = $role_info['role_name'];
        $session_arr['menu_auth'] = $role_info['menu_auth'];
        $session_arr['menu'] = $menu_list;
//        print_r($session_arr);exit('1');
        
        session("admin",$session_arr);
        
        $this->success("登录成功",$menu_list[0]['_child'][0]['menu_link']);
    }
    
    /**
     * 获取菜单
     */
    public function get_menu(){
        $where = "status = 1";
        
        if(session("admin.menu_auth") && session("admin.menu_auth") != 'all'){
            $where .= " AND id IN (".session("admin.menu_auth").")"; 
        }
            
        $menu_list = AdminMenu::where($where)->order("sort DESC,id ASC")->select();
        
        $tree = new \tree\Tree();
        $menu_list = $tree->toTree(json_decode( json_encode( $menu_list),true), 'id', 'pid', 'child');  
        
        return json_encode($menu_list);
    }
}
