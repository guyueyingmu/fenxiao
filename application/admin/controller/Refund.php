<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\Orders;

/**
 * 退款申请
 */
class Refund extends Base
{
    //定义当前菜单id
    public $menu_id = 6;
    private static $order_status = ['', '待处理', '已发货', '已服务', '已取消', '已完成'];//订单状态
    private static $pay_status = ['', '未支付', '已支付', '已退款', '支付失败', '退款失败'];//支付状态
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 关键词搜索
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @param int $status 处理状态（1未处理，2同意，3拒绝）
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
        $status = input("param.status", "", 'trim');
                
        $where = "1=1";
        $where .= $status ? " AND r.status = '$status'" : "";
        $where .= $keyword ? " AND (o.order_number LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND r.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND r.add_time <= '$end_time 23:59:59'" : "";
        
        $list = db('orders_refund_apply')->alias("r")
                ->join("__ORDERS__ o", "o.id=r.order_id", "LEFT")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where($where)
                ->field("r.*,u.phone_number,u.nickname,o.order_number,o.add_time order_add_time,o.order_status,o.pay_status")
                ->page($page,$limit)
                ->order('r.id DESC')
                ->select();
        if($list){
            foreach($list as $k=>$v){
                $list[$k]['order_status_txt'] = self::$order_status[$v['order_status']];
                $list[$k]['pay_status_txt'] = self::$pay_status[$v['pay_status']];                
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
     * @param int $status 处理状态（1未处理，2同意，3拒绝）
     * @param string $handle_note 处理备注
     */
    public function handle(){
        $data = input("param.", "", "trim");
                
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'handle_user' => 'require|max:50',
            'handle_time' => 'require|dateFormat:Y-m-d H:i:s',
            'status' => 'require|in:2,3',
            'handle_note' => 'max:1000',
        ],[
            'id.require' => '参数错误',
            'handle_user.require' => '请输入处理员',
            'handle_user.max' => '处理员长度不能超过50',
            'handle_time.require' => '请选择处理时间',
            'handle_time.dateFormat' => '处理时间格式不正确',
            'status.require' => '请选择处理状态',
            'status.in' => '处理状态数据不正确',
            'handle_note.max' => '处理备注长度不能超过1000',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $refund = db("orders_refund_apply")->find($data['id']);
        if(!$refund){
            $this->error("退款申请不存在");
        }
        if($refund['status'] != 1){
            $this->error("已处理，不能重复处理");
        }
        
        $order = Orders::where('id', $refund['order_id'])->field("pay_method")->find();
        if($order['pay_method'] != 1){
            $this->error("只有微信支付的订单才能退款");
        }
        
        Db::startTrans();
        try{
            //保存发货记录
            $data['admin_user_id'] = session("admin.uid");
            db('orders_refund_apply')->update($data);
            
            if($data['status'] == 2){
                //调用微信退款接口处理退款
                $refund_trade_num = '1111';
                $refund_note = '';
                
                //订单状态更改为 已取消， 支付状态为已退款
                Orders::update(['id' => $refund['order_id'], 'order_status' => 4, 'pay_status' => 3, 'refund_time' => date("Y-m-d H:i:s"), 'refund_trade_num' => $refund_trade_num, 'refund_note' => $refund_note]);

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
        $this->add_log($this->menu_id,['title' => '后台退款操作', 'data' => $data]);
        
        $this->success("操作成功");
    }
           
}
