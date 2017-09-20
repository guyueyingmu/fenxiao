<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\Orders;

/**
 * 分成日志
 */
class Orderdislog extends Base
{
    //定义当前菜单id
    public $menu_id = 18;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 获佣分销商用户ID
     * @param int $status 获佣状态（1等待获佣， 2已获佣）  
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $status = input("param.status", "", 'intval');
                
        $where = "1=1";
        $where .= $status ? " AND dl.status = '$status'" : "";
        $where .= $keyword ? " AND dl.earn_user_id = '$keyword'" : "";
        
        $list = db('order_distribution_log')->alias("dl")
                ->join("__ORDERS__ o", "o.id=dl.order_id", "LEFT")
                ->join("__ADMIN_USER__ au", "au.id=dl.admin_user_id", "LEFT")
                ->where($where)
                ->field("dl.id,o.order_number,dl.order_user_id,dl.good_id,dl.earn_amount,dl.earn_user_id,dl.level,dl.status,dl.earn_time,dl.admin_user_id,au.nickname admin_user_name,dl.earn_amount_input")
                ->page($page,$limit)
                ->order('dl.id DESC')
                ->select();
        $total = db('order_distribution_log')->alias("dl")
                ->where($where)
                ->count();
        
        $total_page = ceil($total/$limit);
        
        $result = [
            "list" => $list,
            "pages" => [
                "total" => $total,
                "total_page" => $total_page,
                "limit" => $limit,
                "current_page" => $page,
                "manage_user" => session("admin.nickname")                
            ]
        ];
//        exit(json_encode($result));
        $this->success("成功", "", $result);
    }
    
    /**
     * 确定获佣操作
     * @param int $id 分成日志id
     * @param string $earn_amount 获佣金额
     * @return string 
     */
    public function handle(){
        $id = input("param.id", "", "intval");
        $earn_amount = input("param.earn_amount", "", "float");
//        echo $earn_amount;exit;
        if(!$id){
            $this->error("参数错误");
        }
        if(!$earn_amount || $earn_amount < 0){
            $this->error("请输入获佣金额");
        }
        
        $info = db("order_distribution_log")->find($id);
        if(!$info){
            $this->error("获佣数据不存在");
        }
        if($info['status'] == 2){
            $this->error("不能重复获佣");
        }
        
        Db::startTrans();
        try{
            //更新获佣记录
            $data['id'] = $id;
            $data['earn_amount_input'] = $earn_amount;
            $data['earn_time'] = date("Y-m-d H:i:s");
            $data['status'] = 2;
            $data['admin_user_id'] = session("admin.uid");
            db('order_distribution_log')->update($data);
            
            //更新获佣用户账户余额,获佣总额
            db("users")->where('id', $info['earn_user_id'])->setInc("account_balance", $earn_amount);
            db("users")->where('id', $info['earn_user_id'])->setInc("earn_total", $earn_amount);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("操作失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log($this->menu_id,['title' => '分成日志获佣操作', 'data' => $data]);
        
        $this->success("操作成功");
    }
           
}
