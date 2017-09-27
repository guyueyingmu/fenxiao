<?php
namespace app\mini\controller;

use think\Db;
use app\admin\model\Orders;
use app\admin\model\OrdersGoods;
/**
 * 订单
 */
class Order extends Base
{
    /**
     * 确认订单
     */
    public function check_order(){
        
    }
    
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'user_id='.session('mini.uid');
        
        $list = Orders::with("ordersGoods")
                ->where($where)
                ->field("id,order_number,order_status,pay_status,total_amount")
                ->page($page,$limit)
                ->order('id DESC')
                ->select();
        
        if($list){
            $list = json_decode(json_encode($list),true);
            foreach($list as $k=>$v){
                if($v['order_status'] == 5){
                    foreach($list[$k]['orders_goods'] as $gk=>$gv){                        
                        $list[$k]['orders_goods'][$gk]['comment_status'] = 0;//未评价
                        $comment = db('goods_comments')->where('good_id', $gv['good_id'])->where('order_id', $v['id'])->where('user_id', session('mini.uid'))->count();
                        if($comment){
                            $list[$k]['orders_goods'][$gk]['comment_status'] = 1;//已评价
                        }
                    }
                }
            }
        }
        
        $total = Orders::where($where)->count();
        
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
     * 订单详情
     */
    public function detail(){
        $order_id = input('param.order_id', '', 'intval');
        if(!$order_id){
            $this->error('参数错误');
        }
        $info = Orders::with('ordersGoods')->where('id', $order_id)->where('user_id', session('mini.uid'))
                ->field('id,order_status,order_number,pay_status,total_amount,add_time,consignee_name,consignee_address,consignee_phone')->find();
        if(!$info){
            $this->error('订单不存在');
        }
        $info = json_decode(json_encode($info),true);
        if($info['order_status'] == 5){
            foreach($info['orders_goods'] as $k=>$v){
                $info['orders_goods'][$k]['comment_status'] = 0;//未评价
                $comment = db('goods_comments')->where('good_id', $v['good_id'])->where('order_id', $info['id'])->where('user_id', session('mini.uid'))->count();
                if($comment){
                    $info['orders_goods'][$k]['comment_status'] = 1;//已评价
                }
            }
        }
//        echo '<pre>';print_r($info);exit;
        $this->success('成功', '', $info);
    }
    
}
