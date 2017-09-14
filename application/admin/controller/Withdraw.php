<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\Users;

/**
 * 提现申请
 */
class Withdraw extends Base
{
    //定义当前菜单id
    private static $menu_id = 11;
    
    /**
     * 获取列表
     * @param string $keyword 用户id，手机号码
     * @param int $status 提现审核状态（1待处理 2已同意 3拒绝）
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $status = input("param.status", "", 'intval');
                
        $where = "1=1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $status ? " AND w.status = $status" : "";
        
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
        exit(json_encode($result));
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
            $this->add_log(self::$menu_id,['title' => '用户设置', 'data' => $data]);
            
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
}
