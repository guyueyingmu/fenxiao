<?php
namespace app\admin\controller;

use app\admin\model\Users;

/**
 * 用户列表
 */
class User extends Base
{
    //定义当前菜单id
    public $menu_id = 10;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户昵称，手机号码
     * @param string $start_time 注册开始时间
     * @param string $end_time 注册结束时间
     * @param int $status 状态（1启用，2禁用）  
     * @param int $credits_sort 积分排序（1降序 2升序） 
     * @param int $account_sort 账户余额排序（1降序 2升序） 
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
        $status = input("param.status", "", 'intval');
        $credits_sort = input("param.credits_sort", "", 'intval');
        $account_sort = input("param.account_sort", "", 'intval');
                
        $where = "1=1";
        $where .= $keyword ? " AND (nickname LIKE '%$keyword%' OR phone_number LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND register_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND register_time <= '$end_time 23:59:59'" : "";
        $where .= $status ? " AND status = $status" : "";
        
        $sort[] = $credits_sort == 1 ? "credits DESC" : ($credits_sort == 2 ? "credits ASC" : "");
        $sort[] = $account_sort == 1 ? "account_balance DESC" : ($account_sort == 2 ? "account_balance ASC" : "");
        $sort[] = "id DESC";
        $sort = array_filter($sort);
        $sort = implode(",", $sort);
//        print_r($sort);exit;
        $users = new Users();
        $list = $users->where($where)
                ->field("id,nickname,img_url,sex,province,city,phone_number,credits,account_balance,status,register_time,last_login_time")
                ->page($page,$limit)
                ->order($sort)
                ->select();
        $total = $users->where($where)->count();
        
        $total_page = ceil($total/$limit);
        
        $result = [
            "list" => $list,
            "pages" => [
                "total" => $total,
                "total_page" => $total_page,
                "limit" => $limit,
                "current_page" => $page,
            ]            
        ];
//        exit(json_encode($result));
        $this->success("成功", "", $result);
    }
    
    /**
     * 设置
     * @param int $id 用户id
     * @param int $credits 用户积分
     * @param int $status 用户状态
     */
    public function handle(){
        $data = input("param.", "", "trim");
        
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'credits' => 'require|number|between:0,9999999',
            'status' => 'require|in:1,2',
        ],[
            'id.require' => '参数错误',
            'credits.require' => '请输入用户积分',
            'credits.number' => '用户积分必须是整数',
            'credits.between' => '用户积分只能在0-9999999之间',
            'status.require' => '请选择用户状态',
            'status.in' => '用户状态数据格式不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $user = Users::get($data['id']);
        if(!$user){
            $this->error("数据不存在");
        }
        $res = $user->update($data);
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '用户设置', 'data' => $data]);
            
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
}
