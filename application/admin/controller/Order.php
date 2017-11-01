<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\Orders;
use app\admin\model\OrdersGoods;
use app\admin\model\Users;

/**
 * 订单列表
 */
class Order extends Base
{
    //定义当前菜单id
    public $menu_id = 5;
    private static $order_status = ['', '待处理', '已发货', '已服务', '已取消', '已完成'];//订单状态
    private static $pay_status = ['', '未支付', '已支付', '已退款', '支付失败', '退款失败'];//支付状态
    private static $order_from = ['', '微信'];//订单来源
    private static $cancel_reason = ['', '超时未支付', '后台取消'];//取消原因
    private static $distribution_status = ['', '未处理', '已处理'];//分销处理
    private static $deliver_method = ['', '上门自提', '快递', '其他'];//配送方式
    private static $pay_method = ['', '微信支付', '线下支付', '积分支付'];//支付方式
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    /**
     * 获取列表
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $user_phone = input("param.user_phone", "", 'trim');
        $order_status = input("param.order_status", "", 'intval');
        $pay_status = input("param.pay_status", "", 'intval');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND (o.order_number LIKE '%$keyword%')" : "";
        $where .= $user_phone ? " AND (u.phone_number LIKE '%$user_phone%')" : "";
        $where .= $order_status ? " AND o.order_status = $order_status" : "";
        $where .= $pay_status ? " AND o.pay_status = $pay_status" : "";
        $where .= $start_time ? " AND o.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND o.add_time <= '$end_time 23:59:59'" : "";
        
        $list = Orders::with("ordersGoods")->alias("o")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where($where)
                ->field("o.*,u.phone_number,u.nickname")
                ->page($page,$limit)
                ->order('o.id DESC')
                ->select();
        if($list){
            foreach ($list as $k => $v){                
                $list[$k]['pay_method_txt'] = self::$pay_method[$v['pay_method']];
                $list[$k]['order_status_txt'] = self::$order_status[$v['order_status']];
                $list[$k]['pay_status_txt'] = self::$pay_status[$v['pay_status']];
                $list[$k]['order_from_txt'] = self::$order_from[$v['order_from']];
            }
        }
        $total = Orders::alias("o")
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
     * 订单详情
     * @param int $order_id 订单id
     */
    public function get_order_info(){
        $order_id = input("param.order_id", "", "intval");
        if(!$order_id){
            $this->error("参数错误");
        }
        //订单是否存在
        $order_info = Orders::with('ordersGoods,orderService,orderConsignment')->alias("o")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where('o.id', $order_id)
                ->field("o.*, u.phone_number, u.nickname")
                ->find();
        if(!$order_info){
            $this->error("订单不存在");
        }
        
        $order_info['order_status_txt'] = self::$order_status[$order_info['order_status']];
        $order_info['order_from_txt'] = self::$order_from[$order_info['order_from']];
        $order_info['cancel_reason_txt'] = self::$cancel_reason[$order_info['cancel_reason']];
        $order_info['distribution_status_txt'] = self::$distribution_status[$order_info['distribution_status']];
        $order_info['pay_status_txt'] = self::$pay_status[$order_info['pay_status']];
        
        if($order_info['order_consignment']){
            $order_info['order_consignment']['deliver_method_txt'] = self::$deliver_method[$order_info['order_consignment']['deliver_method']];
        }
//        exit(json_encode($order_info));
        $this->success("成功", "", $order_info);
    }
    
    /**
     * 发货操作
     * @param int $order_id 订单id
     * @param string $consignment_user 发货人员名字
     * @param datetime $consignment_time 发货时间
     * @param int $deliver_method 配送方式（1上门自提、2快递、3其他）
     * @param string $tracking_number 快递编号
     */
    public function consignment(){
        $data = input("param.", "", "trim");
        
        if(!$data['order_id']){
            $this->error("参数错误");
        }
        
        //订单是否存在
        $order_info = Orders::get($data['order_id']);
        if(!$order_info){
            $this->error("订单不存在");
        }
        $good_type = $order_info->ordersGoods()->value('good_type');
		
        if(!in_array($good_type,[1, 4])){
            $this->error("非发货类型订单");
        }
        
        $validate_res = $this->validate($data,"OrdersConsignment.consignment"); 
		
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        if($order_info['order_status'] != 1 || $order_info['pay_status'] != 2){
            $this->error("订单不满足发货条件");
        }
        
        Db::startTrans();
        try{
            //保存发货记录
			$fh_data['consignment_time'] = $data['consignment_time'];
			$fh_data['consignment_user'] = $data['consignment_user'];
			$fh_data['deliver_method'] = $data['deliver_method'];
			$fh_data['order_id'] = $data['order_id'];
			$fh_data['tracking_number'] = $data['tracking_number'];
            $fh_data['admin_user_id'] = session("admin.uid");
            $fh_data['add_time'] = date("Y-m-d H:i:s");
			
            db('OrderConsignment')->insert($fh_data);
            
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
//            $this->error("发货失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log($this->menu_id,['title' => '后台发货操作', 'data' => $fh_data]);
        
        $this->success("发货成功");
    }
    
    /**
     * 发货和服务操作的后续操作，如：保存分成日志等
     * @param float $input_total_amount 后台输入的收款总额
     */
    protected function save_log($order_info, $input_total_amount = 0){
        //写入分成日志表
        $user_info = Users::where('id', $order_info['user_id'])->field('pid,distribution_level')->find();
        if($user_info['distribution_level'] != 0 && $user_info['pid'] > 0){
            $good_list = $order_info->ordersGoods;
            if($good_list){
                $presenter_credits = 0;

                $second_user_id = Users::where('id', $user_info['pid'])->value('pid');

                $log_data['order_id'] = $order_info['id'];
                $log_data['order_user_id'] = $order_info['user_id'];
                $log_data['status'] = 1;
                $log_data['admin_user_id'] = session("admin.uid");
                $log_data['add_time'] = date("Y-m-d H:i:s");
                foreach($good_list as $k=>$v){
                    if($v['distribution'] == 1){
                        $log_data['good_id'] = $v['good_id'];
                        $log_data['earn_amount'] = get_earn_amount($input_total_amount ? $input_total_amount : $v['price']*$v['buy_num'], 1);
                        $log_data['earn_user_id'] = $user_info['pid'];
                        $log_data['level'] = 1;
                        $log_data_all[] = $log_data;

                        //二级分销
                        if($second_user_id){
                            $log_data['earn_amount'] = get_earn_amount($input_total_amount ? $input_total_amount : $v['price']*$v['buy_num'], 2);
                            $log_data['earn_user_id'] = $second_user_id;
                            $log_data['level'] = 2;
                            $log_data_all[] = $log_data;
                        }
                    }

                    //赠送积分
                    if($v['presenter_credits']){
                        $presenter_credits += $v['presenter_credits'];
                    }
                }
                if(isset($log_data_all)){
                    db("OrderDistributionLog")->insertAll($log_data_all);
                }

                //赠送积分
                if($presenter_credits){
                    //修改用户表积分字段
                    db('Users')->where('id', $order_info['user_id'])->setInc('credits', $presenter_credits);

                    //写入积分记录表
                    $user_credit_log['user_id'] = $order_info['user_id'];
                    $user_credit_log['credits_in'] = $presenter_credits;
                    $user_credit_log['credits_from'] = 2;
                    $user_credit_log['track_id'] = $order_info['id'];
                    $user_credit_log['add_time'] = date("Y-m-d H:i:s");
                    db('users_credits_records')->insert($user_credit_log);
                }
            }
        }
    }


    /**
     * 服务操作
     * @param int $order_id 订单id
     * @param string $service_user 服务人员名字
     * @param datetime $service_time 服务时间
     * @param string $note 备注
     * @param float $total_amount 线下支付收款总额
     */
    public function service(){
        $data = input("param.", "", "trim");
        
        if(!$data['order_id']){
            $this->error("参数错误");
        }
        
        //订单是否存在
        $order_info = Orders::get($data['order_id']);
        if(!$order_info){
            $this->error("订单不存在");
        }
        $good_type = $order_info->ordersGoods()->value('good_type');
        if(in_array($good_type,[2, 5])){
            if($order_info['order_status'] != 1 || $order_info['pay_status'] != 2){
                $this->error("订单不满足服务条件");
            }
            $validate_scene = "OrdersConsignment.service";
        }elseif($good_type == 3){
            if($order_info['order_status'] != 1 || $order_info['pay_status'] != 1){
                $this->error("订单不满足服务条件");
            }
            $validate_scene = "OrdersConsignment.service_amount";
        }else{
            $this->error("非服务类型订单");
        }
        
        $validate_res = $this->validate($data,$validate_scene); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }        
        
        Db::startTrans();
        try{
            //保存服务记录
            $service_data['order_id'] = $data['order_id'];
            $service_data['service_user'] = $data['service_user'];
            $service_data['service_time'] = $data['service_time'];
            $service_data['note'] = $data['note'];
            $service_data['admin_user_id'] = session("admin.uid");
            $service_data['add_time'] = date("Y-m-d H:i:s");
            db('OrderService')->insert($service_data);
            
            //修改订单状态为已服务，订单分成处理改为已处理
            $order_edit = ['id' => $data['order_id'], 'order_status' => 3, 'distribution_status' => 2];
            if($good_type == 3){
                $order_edit['pay_method'] = 2;
                $order_edit['pay_time'] = date('Y-m-d H:i:s');
                $order_edit['pay_status'] = 2;
                $order_edit['total_amount'] = $data['total_amount'];
            }
            Orders::update($order_edit);
            
            if($good_type == 2){
                $this->save_log($order_info);
            }elseif($good_type == 3){
                $this->save_log($order_info, $data['total_amount']);
            }
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("发货失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log($this->menu_id,['title' => '后台服务操作', 'data' => $data]);
        
        $this->success("发货成功");
    }
    
    /**
     * 取消订单
     * @param int $order_id 订单id
     */
    public function cancel(){
        $order_id = input("param.order_id", "", "intval");
        if(!$order_id){
            $this->error("参数错误");
        }
        
        //订单是否存在
        $order_info = Orders::get($order_id);
        if(!$order_info){
            $this->error("订单不存在");
        }
        
        if($order_info['pay_status'] != 1){
            $this->error("只有未支付的订单可以取消");
        }
        
        $model = Orders::update(['id' => $order_id, 'order_status' => 4, 'cancel_reason' => 2, 'cancel_time' => date("Y-m-d H:i:s")]);
                
        //写日志
        $this->add_log($this->menu_id,['title' => '取消订单', 'data' => $model]);
        
        $this->success("取消成功");
    }
        
}
