<?php
namespace app\mini\controller;

use think\Db;
use app\admin\model\Orders;
use app\admin\model\OrdersGoods;
use app\admin\model\Users;
/**
 * 订单
 */
class Order extends Base
{
    
    private static $order_status = ['', '待处理', '已发货', '已服务', '已取消', '已完成'];//订单状态
    private static $pay_status = ['', '未支付', '已支付', '已退款', '支付失败', '退款失败'];//支付状态
    
    /**
     * 确认订单
     * [
     *      {
     *          'good_id':1,
     *          'good_count':1,
     *      },
     *      {
     *          'good_id':2,
     *          'good_count':3,
     *      },
     * ]
     */
    public function check_order(){
        $data = input('param.', '', 'trim'); //print_r($data);exit;
//        $data = [
//            [
//                'good_id'=>4,
//                'good_count'=> 1,
//            ],
//            [
//                'good_id'=>3,
//                'good_count' => 2,
//            ]
//        ];
        if(!$data){
            $this->error('请选择要购买的商品');
        }
        foreach($data as $k=>$v){
            if(!$v['good_id']){
                $this->error('商品标识有误');
            }
            if(!$v['good_count']){
                $this->error('至少购买一个商品');
            }
            $good_id[] = $v['good_id'];
            $good_count[$v['good_id']] = $v['good_count'];
        }
        $good_list = db('goods')->where('id', 'IN', $good_id)->where('status', 1)->order('FIELD(id, '.  implode(',', $good_id).')')->field('id,good_img,good_title,price,good_type,credits')->select();
        if(!$good_list){
            $this->error('商品不存在');
        }
        $total_amount = 0;
        $total_credits = 0;
        
        foreach($good_list as $k=>$v){
            $good_list[$k]['good_count'] = $good_count[$v['id']];
            $total_amount += $v['price']*$good_list[$k]['good_count'];
            $total_credits += $v['credits']*$good_list[$k]['good_count'];
        }
        
        $address = []; $show_address = 0;
        if(in_array($good_list[0]['good_type'],[1, 4])){
            //默认收货地址
            $address = db('consignee_info')->where('user_id', session('mini.uid'))->where('is_default', 1)->find();
            $show_address = 1;
        }
        
        $result['total_amount'] = sprintf('%.2f',$total_amount);
        $result['total_credits'] = $total_credits;
        $result['good_list'] = $good_list;
        $result['address'] = $address;
        $result['show_address'] = $show_address;
//        exit(json_encode($result));
        $this->success('成功', '', $result);
    }
    
    /**
     * 提交订单
     * @param array $good_info 商品信息
     * [
     *      {
     *          'good_id':1,
     *          'good_count':1,
     *      },
     *      {
     *          'good_id':2,
     *          'good_count':3,
     *      },
     * ]
     * @param int $address_id 收货地址id
     */
    public function add_order(){
        $goods = input('param.good_info', '', 'trim');
        $goods = json_decode($goods,true);
//        $goods = [
//            [
//                'good_id'=>4,
//                'good_count'=> 1,
//            ],
//            [
//                'good_id'=>3,
//                'good_count' => 2,
//            ]
//        ];
        $address_id = input('param.address_id', '', 'intval');
        
        if(!$goods){
            $this->error('缺少要购买的商品信息');
        }
        foreach($goods as $k=>$v){
            if(!$v['good_id']){
                $this->error('商品标识有误');
            }
            if(!$v['good_count']){
                $this->error('至少购买一个商品');
            }
            $good_id[] = $v['good_id'];
            $good_count[$v['good_id']] = $v['good_count'];
        }
        $good_where = 'g.id IN ('.implode(',', $good_id).') AND g.status = 1';
        if(count($goods) > 1){
            $good_where .= ' AND g.good_type = 1';
        }
        $good_list = db('goods')->alias('g')
                ->join('__GOODS_CATEGORY__ gc', 'gc.id=g.cat_id', 'LEFT')
                ->where($good_where)->order('FIELD(g.id, '.  implode(',', $good_id).')')
                ->field('g.id good_id,g.good_num,g.good_img,g.good_title,gc.cat_name,g.credits,g.price,g.distribution,g.good_type,g.presenter_credits')->select();
        if(!$good_list){
            $this->error('商品不存在');
        }
        
        $total_amount = 0;
        $total_credits = 0;
        foreach($good_list as $k=>$v){
            $good_list[$k]['buy_num'] = $good_count[$v['good_id']];
            $total_amount += $v['price']*$good_list[$k]['buy_num'];
            $total_credits += $v['credits']*$good_list[$k]['buy_num'];
        }
        if(in_array($good_list[0]['good_type'], [1, 4])){
            if(!$address_id){
                $this->error('请选择收货地址');
            }
            $address_info = db('consignee_info')->where('id', $address_id)->where('user_id', session('mini.uid'))->find();
            if($address_info['province'] == $address_info['city']){
                $address_detail = $address_info['city']."市 ".$address_info['area']." ".$address_info['address'];
            }else{
                $address_detail = $address_info['province']."省 ".$address_info['city']."市 ".$address_info['area']." ".$address_info['address'];
            }
        }
        
        if(in_array($good_list[0]['good_type'], [1, 2])){
            $pay_method = 1;
        }elseif($good_list[0]['good_type'] == 3){
            $pay_method = 2;
        }elseif(in_array($good_list[0]['good_type'], [4, 5])){
            $pay_method = 3;
        }
        
        Db::startTrans();
        try{
            $order_number = db('orders_number')->insertGetId(['add_time' => time()]);
            
            $order = Orders::create([
                'order_number' => $order_number,
                'total_amount' => $total_amount,
                'minus_credits' => $total_credits,
                'order_from' => 1,
                'user_id' => session('mini.uid'),
                'add_time' => date('Y-m-d H:i:s'),
                'pay_method' => $pay_method,
                'consignee_name' => isset($address_info['user_name']) ? $address_info['user_name'] : '',
                'consignee_address' => isset($address_detail) ? $address_detail : '',
                'consignee_phone' => isset($address_info['phone']) ? $address_info['phone'] : '',
            ]);
            foreach($good_list as $k=>$v){
                $good_list[$k]['order_id'] = $order->id;
                $good_list[$k]['add_time'] = date('Y-m-d H:i:s');
            }
            $order->ordersGoods()->saveAll($good_list);
            
            //如果是购物车结算，要删除购物车数据
            db('goods_cart')->where('user_id', session('mini.uid'))->where('good_id', 'IN', $good_id)->delete();
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
        $result['order_id'] = $order->id;
        $result['pay_method'] = $pay_method;
//        exit(json_encode($result));
        $this->success('提交订单成功', '', $result);
    }
    
    /**
     * 积分兑换
     * @param int $order_id 订单id
     */
    public function credits_exchange(){
        $order_id = input('param.order_id', '', 'intval');
        if(!$order_id){
            $this->error('参数错误');
        }
        $order_info = Orders::with('ordersGoods')->where(['id' => $order_id, 'user_id' => session('mini.uid')])->find();
        if(!$order_info){
            $this->error('订单不存在');
        }
        if($order_info['order_status'] == 4){
            $this->error('订单已取消');
        }
        if($order_info['pay_status'] == 2){
            $this->error('订单已支付，请不要重复支付');
        }
        if(!in_array($order_info['orders_goods'][0]['good_type'],[4, 5])){
            $this->error('非积分类商品，不能用积分支付');
        }
        $user_info = Users::field('id,credits')->find(session('mini.uid'));
        if($user_info['credits'] < $order_info['minus_credits']){
            $this->error('积分不足');
        }
        
        Db::startTrans();
        try{
            //添加积分记录
            $add_data['user_id'] = session('mini.uid');
            $add_data['credits_out'] = $order_info['minus_credits'];
            $add_data['credits_from'] = 4;
            $add_data['track_id'] = $order_id;
            $add_data['add_time'] = date('Y-m-d H:i:s');
            db('users_credits_records')->insert($add_data);
            //修改用户表积分字段
            db('Users')->where('id', session('mini.uid'))->setDec('credits', $order_info['minus_credits']);
            //修改订单表支付状态
            Orders::update(['id' => $order_id, 'pay_status' => 2, 'pay_method' => 3, 'pay_time' => date('Y-m-d H:i:s')]);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
        $this->success('积分兑换成功');
    }
    
    /**
     * 获取列表
     * @param status $int 过滤状态 1待付款 2待收货 4已取消 5已完成
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'user_id='.session('mini.uid');
        
        $status = input('param.status', '', 'intval');
        if($status){
            if($status == 1){
                $where .= ' AND pay_status = 1 AND order_status = 1';
            }elseif(in_array($status, [2, 4, 5])){
                $where .= ' AND order_status ='. $status;
            }
        }
        
        $list = Orders::with("ordersGoods")
                ->where($where)
                ->field("id,order_number,order_status,pay_status,total_amount,pay_method,minus_credits")
                ->page($page,$limit)
                ->order('id DESC')
                ->select();
        
        if($list){
            $list = json_decode(json_encode($list),true);
            foreach($list as $k=>$v){
                $list[$k]['order_status_txt'] = self::$order_status[$v['order_status']];
                $list[$k]['comment_status'] = 0;//不可以评价
                if($v['order_status'] == 5){
                    $list[$k]['comment_status'] = 1;//可以评价
                    $comment = db('goods_comments')->where('good_id', $v['orders_goods'][0]['good_id'])->where('order_id', $v['id'])->where('user_id', session('mini.uid'))->count();
                    if($comment){
                        $list[$k]['comment_status'] = 0;//不可以评价
                    }
                }
                $list[$k]['refund'] = 0;//不可以申请退款
                if($v['pay_method'] == 1 && $v['pay_status'] == 2 && in_array($v['order_status'], [1, 2, 3])){ //可申请退款，查看是否已有申请
                    $list[$k]['refund'] = 1;//可以申请退款
                    $refund = db('orders_refund_apply')->where('order_id', $v['id'])->count();
                    if($refund){
                        $list[$k]['refund'] = 0;//不可以申请退款
                    }
                }
                $list[$k]['exchange'] = 0;//不可以申请换货
                if($v['order_status'] == 2){
                    $list[$k]['exchange'] = 1;//可以申请换货
                    $exchange = db('orders_exchange_apply')->where('order_id', $v['id'])->count();
                    if($exchange){
                        $list[$k]['exchange'] = 0;//不可以申请换货
                    }
                }
                if($list[$k]['refund'] == 0 || $list[$k]['exchange'] == 0){
                    $list[$k]['refund'] = 0;
                    $list[$k]['exchange'] = 0;
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
                ->field('id,order_status,order_number,pay_status,pay_method,total_amount,minus_credits,add_time,consignee_name,consignee_address,consignee_phone')->find();
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
        $info['order_status_txt'] = self::$order_status[$info['order_status']];
        $info['pay_status_txt'] = self::$pay_status[$info['pay_status']];
        $info['pay_url'] = url('/mini/Payment/index', ['order_id' => $order_id]);
//        echo '<pre>';print_r($info);exit;
        $this->success('成功', '', $info);
    }
    
}
