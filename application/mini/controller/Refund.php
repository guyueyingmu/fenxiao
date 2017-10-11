<?php
namespace app\mini\controller;

use app\admin\model\Orders;

/**
 * 退款
 */
class Refund extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'o.user_id='. session('mini.uid'). ' AND r.id IS NOT NULL';
        $list = Orders::with('ordersGoods')->alias('o')
                ->join('__ORDERS_REFUND_APPLY__ r', 'r.order_id=o.id', 'LEFT')
                ->where($where)
                ->field('o.id,o.order_number,r.status')
                ->order('r.id DESC')->page($page, $limit)
                ->select();
        
        $total = Orders::alias('o')
                ->join('__ORDERS_REFUND_APPLY__ r', 'r.order_id=o.id', 'LEFT')
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
        $this->success('成功', '', $result);
    }
    
    /**
     * 申请退款
     */
    public function add(){
        $order_id = input('param.order_id', '', 'intval');
        if(!$order_id){
            $this->error('参数错误');
        }
        $info = db('Orders')->where('id', $order_id)->where('user_id', session('mini.uid'))->field('id,user_id,order_status,pay_status,pay_method')->find();
        if(!$info){
            $this->error('订单不存在');
        }
        if($info['pay_status'] != 2 && !in_array($info['order_status'], [1, 2])){
            $this->error('已支付未完成的订单才能申请退款');
        }
        if($info['pay_method'] != 1){
            $this->error('微信支付的订单才能退款');
        }
        //是否已申请退款
        $exist = db('orders_refund_apply')->where('user_id', session('mini.uid'))->where('order_id', $order_id)->where('status', '<>', '3')->count();
        if($exist){
            $this->success('您的申请正在处理');
        }
        
        $add_data['user_id'] = session('mini.uid');
        $add_data['order_id'] = $order_id;
        $add_data['add_time'] = date('Y-m-d H:i:s');
        $add_data['status'] = 1;
        $res = db('orders_refund_apply')->insert($add_data);
        if($res){
            $this->success('申请成功');
        }else{
            $this->error('申请失败');
        }
    }
}
