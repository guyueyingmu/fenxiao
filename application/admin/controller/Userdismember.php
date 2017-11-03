<?php
namespace app\admin\controller;

use app\admin\model\Users;

/**
 * 分销会员列表
 */
class Userdismember extends Base
{
    //定义当前菜单id
    public $menu_id = 16;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户id
     * @param string $user_phone 手机号码
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $user_phone = input("param.user_phone", "", 'trim');
                
        $where = "distribution_level != 0 AND pid != 0 AND status = 1";
        $where .= $keyword ? " AND (id LIKE '%$keyword%')" : "";
        $where .= $user_phone ? " AND (phone_number LIKE '%$user_phone%')" : "";

        $users = new Users();
        $list = $users->where($where)
                ->field("id,nickname,phone_number,dis_scan_time,pid,distribution_level,distributor_time")
                ->page($page,$limit)
                ->order('id DESC')
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
     * @param int $distribution_level 1退出分销商 2设为分销商
     */
    public function handle(){
        $data = input("param.", "", "trim");
        
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'distribution_level' => 'require|in:1,2',
        ],[
            'id.require' => '参数错误',
            'distribution_level.require' => '缺少操作类型',
            'distribution_level.in' => '操作类型不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $user = Users::get($data['id']);
        if(!$user){
            $this->error("用户不存在");
        }
        if($user['pid'] == 0){
            $this->error('顶级分销商不允许操作');
        }
        if($user['distribution_level'] == $data['distribution_level']){
            $this->error('重复设置');
        }
        
        if($data['distribution_level'] == 1){
            $data['out_distributor_time'] = date("Y-m-d H:i:s");
            $data['sepcial_dis'] = 2;
            $log_title = '退出分销商';
        }else{
            $data['distributor_time'] = date("Y-m-d H:i:s");
            $log_title = '设为分销商';
            
            //生成分销二维码
            $qrcode = new Qrcode();
            $qrcode->get_qrcode($data['id']);            
        }
        
        $res = $user->update($data);
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => $log_title, 'data' => $data]);
            
            $this->success('设置成功');
        }else{
            $this->error('设置失败');
        }
    }
}
