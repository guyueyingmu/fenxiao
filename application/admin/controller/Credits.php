<?php
namespace app\admin\controller;

/**
 * 积分记录列表
 */
class Credits extends Base
{
    //定义当前菜单id
    private static $menu_id = 25;
    private static $credits_from = ['', '签到收入', '购买商品收入', '分享收入', '积分兑换支出', '后台操作收入', '后台操作支出'];

    /**
     * 获取列表
     * @param string $keyword 用户id
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%' OR u.nickname LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND cr.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND cr.add_time <= '$end_time 23:59:59'" : "";
        
        $records = db('users_credits_records');
        $list = $records->alias('cr')
                ->join("__USERS__ u", "u.id=cr.user_id", "LEFT")
                ->where($where)
                ->field("cr.user_id,u.nickname,cr.credits_in,cr.credits_out,cr.credits_from,cr.track_id,cr.add_time")
                ->page($page,$limit)
                ->order('cr.id DESC')
                ->select();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['credits_from_txt'] = self::$credits_from[$v['credits_from']];
            }
        }
        $total = $records->alias('cr')
                ->join("__USERS__ u", "u.id=cr.user_id", "LEFT")
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
