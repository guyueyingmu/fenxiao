<?php
namespace app\admin\controller;

/**
 * 签到列表
 */
class Signin extends Base
{
    //定义当前菜单id
    public $menu_id = 12;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户id，手机号码
     * @param string $start_time 签到开始时间
     * @param string $end_time 签到结束时间
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND s.signin_date >= '$start_time'" : "";
        $where .= $end_time ? " AND s.signin_date <= '$end_time'" : "";
        
        $withdraw = db('signin');
        $list = $withdraw->alias('s')
                ->join("__USERS__ u", "u.id=s.user_id", "LEFT")
                ->where($where)
                ->field("s.id,s.user_id,u.nickname,u.phone_number,s.signin_date,s.add_time")
                ->page($page,$limit)
                ->order('s.id DESC')
                ->select();
        $total = $withdraw->alias('s')
                ->join("__USERS__ u", "u.id=s.user_id", "LEFT")
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
}
