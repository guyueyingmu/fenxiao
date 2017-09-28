<?php
namespace app\mini\controller;

use app\admin\model\Orders;

/**
 * 换货
 */
class Exchange extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'o.user_id='. session('mini.uid'). ' AND e.id IS NOT NULL';
        $list = Orders::with('ordersGoods')->alias('o')
                ->join('__ORDERS_EXCHANGE_APPLY__ e', 'e.order_id=o.id', 'LEFT')
                ->where($where)
                ->field('o.id,o.order_number,e.status')
                ->order('e.id DESC')->page($page, $limit)
                ->select();
        
        $total = Orders::alias('o')
                ->join('__ORDERS_REFUND_APPLY__ e', 'e.order_id=o.id', 'LEFT')
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
     * 申请换货
     */
    public function add(){
        $order_id = input('param.order_id', '', 'intval');
        if(!$order_id){
            $this->error('参数错误');
        }
        $info = db('Orders')->where('id', $order_id)->where('user_id', session('mini.uid'))->field('id,user_id,order_status,pay_status,')->find();
        if(!$info){
            $this->error('订单不存在');
        }
        if($info['order_status'] != 2){
            $this->error('已发货的订单才能申请换货');
        }
        //是否已申请换货
        $exist = db('orders_exchange_apply')->where('user_id', session('mini.uid'))->where('order_id', $order_id)->where('status', '!=', '3')->count();
        if($exist){
            $this->success('您的申请正在处理');
        }
        
        $add_data['user_id'] = session('mini.uid');
        $add_data['order_id'] = $order_id;
        $add_data['add_time'] = date('Y-m-d H:i:s');
        $add_data['status'] = 1;
        $res = db('orders_exchange_apply')->insert($add_data);
        if($res){
            $this->success('申请成功');
        }else{
            $this->error('申请失败');
        }
    }
}
