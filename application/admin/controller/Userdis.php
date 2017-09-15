<?php
namespace app\admin\controller;

use app\admin\model\Users;

/**
 * 分销商列表
 */
class Userdis extends Base
{
    //定义当前菜单id
    private static $menu_id = 17;
    
    /**
     * 获取列表
     * @param string $keyword 用户id，手机号码
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
                
        $where = "u.distribution_level = 2 AND u.status = 1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";

        $users = new Users();
        $list = $users->where($where)->alias('u')
                ->join('__USERS__ pu', 'pu.id=u.pid', 'LEFT')
                ->field("u.id,u.nickname,u.phone_number,u.distributor_time,u.account_balance,u.earn_total,u.pid,pu.nickname p_user,dis_qrcode,(SELECT COUNT(*) FROM mb_users WHERE status=1 AND pid=u.`id`) c_total,(SELECT COUNT(*) FROM mb_users WHERE status = 1 AND pid IN (SELECT id FROM mb_users WHERE status = 1 AND distribution_level=2 AND pid=u.`id`)) c_total2")
                ->page($page,$limit)
                ->order('u.id DESC')
                ->select();
        $total = $users->alias('u')->where($where)->count();
        
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
     * 会员列表
     * @param int $id 上级分销商id
     * @param int $type 会员等级 1一级会员 2二级会员
     * @param string $keyword 用户id，手机号码
     * @return string
     */
    public function get_member_list(){
        $id = input("param.id", "", "intval");
        $type = input("param.type", "", "intval");
        if(!$id){
            $this->error("缺少上级分销商id");
        }
        if(!in_array($type, [1, 2])){
            $this->error("缺少会员等级");
        }
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
                
        $where = "status = 1";
        $where .= $type == 1 ? " AND pid = $id" : " AND pid IN (SELECT id FROM mb_users WHERE status = 1 AND distribution_level=2 AND pid=$id)";
        $where .= $keyword ? " AND (id LIKE '%$keyword%' OR phone_number LIKE '%$keyword%')" : "";

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
        exit(json_encode($result));
        $this->success("成功", "", $result);
    }
}
