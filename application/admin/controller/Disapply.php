<?php
namespace app\admin\controller;

/**
 * 分销申请
 */
class Disapply extends Base
{
    //定义当前菜单id
    public $menu_id = 29;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户id
     * @param string $user_phone 手机号码
     * @param int $status 提现审核状态（1待处理 2已同意 3拒绝）
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $user_phone = input("param.user_phone", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%')" : "";
        $where .= $user_phone ? " AND (u.phone_number LIKE '%$user_phone%')" : "";
        
        $list = db('distribution_apply')->alias('da')
                ->join("__USERS__ u", "u.id=da.user_id", "LEFT")
                ->join("__ADMIN_USER__ au", "au.id=da.admin_user_id", "LEFT")
                ->where($where)
                ->field("da.id,da.user_id,u.nickname,u.phone_number,da.add_time,da.status,da.handle_time,da.admin_user_id,au.nickname admin_user_name")
                ->page($page,$limit)
                ->order('da.id DESC')
                ->select();
        $total = db('distribution_apply')->alias('da')
                ->join("__USERS__ u", "u.id=da.user_id", "LEFT")
                ->join("__ADMIN_USER__ au", "au.id=da.admin_user_id", "LEFT")
                ->where($where)->count();
        
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
     * 标记处理
     * @param int $id 提现申请id
     */
    public function handle(){
        $id = input("param.id", "", "intval");
        
        if(!$id){
            $this->error('参数错误');
        }
        
        $info = db("distribution_apply")->find($id);
        if(!$info){
            $this->error("分销申请数据不存在");
        }
        if($info['status'] != 1){
            $this->error("已处理，不能重复处理");
        }
        
        $data = [
            'id' => $id,
            'status' => 2,
            'handle_time' => date('Y-m-d H:i:s'),
            'admin_user_id' => session('admin.uid'),
        ];
        $res = db("distribution_apply")->update($data);
        if($res){
            //写日志
            $this->add_log($this->menu_id,['title' => '后台分销申请标记处理', 'data' => array_merge($info,$data)]);

            $this->success("已标记处理！");  
        }else{
            $this->error("操作失败");
        }
    }
}
