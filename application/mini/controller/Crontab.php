<?php
namespace app\mini\controller;
use think\Controller;

/*
 * 脚本
 */
class Crontab extends Controller
{
    /*
     * 超时处理订单
     * 1分钟跑一次
     */
    public function handle_timeout_orders(){        
        //下单7天后未支付取消订单
        $cancel_list = db("orders")->where("pay_status",'IN',[1,4])->where("order_status",1)->where("add_time",'lt',date('Y-m-d H:i:s', time()-7*24*3600))->field("id")->select();
        if($cancel_list){
            foreach($cancel_list as $k=>$v){
                $data = [
                    'id' => $v['id'],
                    'order_status' => 4,
                    'cancel_reason' => 1,
                    'cancel_time' => date('Y-m-d H:i:s')
                ];
                db("orders")->update($data);
            }
        }
        
        //发货7天后标记已完成
        $finish_list1 = db("orders")->alias('o')
                ->join('__ORDER_CONSIGNMENT__ oc', 'o.id=oc.order_id', 'LEFT')
                ->where("pay_status", 2)->where("order_status", 2)
                ->where("oc.add_time",'lt',date('Y-m-d H:i:s', time()-7*24*3600))
                ->field("o.id")
                ->select();
        $finish_list2 = db("orders")->alias('o')
                ->join('__ORDER_SERVICE__ os', 'o.id=os.order_id', 'LEFT')
                ->where("pay_status", 2)->where("order_status", 3)
                ->where("os.add_time",'lt',date('Y-m-d H:i:s', time()-7*24*3600))
                ->field("o.id")
                ->select();
        $finish_list1 = $finish_list1 ? $finish_list1 : [];
        $finish_list2 = $finish_list2 ? $finish_list2 : [];
        $finish_list = array_merge($finish_list1, $finish_list2);
        if($finish_list){
            foreach($finish_list as $k=>$v){
                $data = [
                    'id' => $v['id'],
                    'order_status' => 5,
                    'finish_time' => date('Y-m-d H:i:s')
                ];
                db("orders")->update($data);
            }
        }
    }
    
}
