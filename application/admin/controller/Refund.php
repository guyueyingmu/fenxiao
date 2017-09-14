<?php
namespace app\admin\controller;

/**
 * 退款申请
 */
class Refund extends Base
{
    //定义当前菜单id
    private static $menu_id = 6;
    
    /**
     * 获取列表
     * @param string $keyword 关键词搜索
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
        $where .= $keyword ? " AND (o.order_number LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND r.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND r.add_time <= '$end_time 23:59:59'" : "";
        
        $list = db('orders_refund_apply')->alias("r")
                ->join("__ORDERS__ o", "o.id=r.order_id", "LEFT")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where($where)
                ->field("r.*,u.phone_number,u.nickname,o.order_number,o.add_time order_add_time")
                ->page($page,$limit)
                ->order('r.id DESC')
                ->select();
        if($list){
            foreach ($list as $k => $v){
                $list[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
                $list[$k]['order_add_time'] = date("Y-m-d H:i:s",$v['order_add_time']);
            }
        }
        $total = db('orders_refund_apply')->alias("r")
                ->join("__ORDERS__ o", "o.id=r.order_id", "LEFT")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
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
     * 退款操作
     * @param int $id 退款申请id
     * @param string $handle_user 处理员名字
     * @param datetime $handle_time 处理时间
     * @param int $status 处理状态（0未处理，1同意，2拒绝）
     * @param string $handle_note 处理备注
     */
    public function consignment(){
        $data = input("param.", "", "trim");
                
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'handle_user' => 'require|max:50',
            'handle_time' => 'require|dateFormat:Y-m-d H:i:s',
            'status' => 'require|in:1,2',
        ],[
            'id.require' => '参数错误',
            'handle_user.require' => '请输入处理员',
            'handle_user.max' => '处理员长度不能超过50',
            'handle_time.require' => '请选择处理时间',
            'handle_time.dateFormat' => '处理时间格式不正确',
            'status.require' => '请选择处理状态',
            'status.in' => '处理状态数据不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        Db::startTrans();
        try{
            //保存发货记录
            $data['admin_user_id'] = session("admin.uid");
            $data['add_time'] = time();
            db('OrderConsignment')->insert($data);
            
            //修改订单状态为已发货，订单分成处理改为已处理
            Orders::update(['id' => $data['order_id'], 'order_status' => 2, 'distribution_status' => 2]);
            
            if($good_type == 1){
                $this->save_log($order_info);
            }
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("操作失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log(self::$menu_id,['title' => '后台发货操作', 'data' => $data]);
        
        $this->success("操作成功");
    }
           
}
