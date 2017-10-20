<?php
namespace app\mini\controller;
use think\Controller;
//支付类，目前是微信支付
class Payment extends Controller
{
    
    public function index()
    {
		
		require APP_PATH . "/../extend/Wxpay/lib/WxPay.Api.php";
		require APP_PATH . "/../extend/Wxpay/example/WxPay.JsApiPay.php";
		//②、统一下单
		$order_id = input("order_id",0,'intval');
        
		if(!$order_id)
		{
			$this->error("参数错误");
		}
		
		$data = db("orders")->where("id=".$order_id)->find();

		if(!$data)
		{
			$this->error("订单不存在");
		}
        if($data['pay_status'] == 2){
            $this->error('订单已支付');
        }elseif($data['pay_status'] == 3){
            $this->error('订单已退款');
        }
		
		$goods_info = db("orders_goods")->alias("og")
                ->where("og.order_id=$order_id")->order("og.id ASC")->field("og.good_title,og.good_type")->find();
        
        if(!in_array($goods_info['good_type'],[1, 2])){
            $this->error('非微信支付类商品，不能进行微信支付');
        }
		
		$tools = new \JsApiPay();
		
		$input = new \WxPayUnifiedOrder();
		
		$WxPayApi = new \WxPayApi();
		
		
		$title = $goods_info['good_title'];
		$trade_no = $data['order_number'];
		$total_fee = $data['total_amount']*100;
		$openId = session("mini.openid");
		
		
		$input->SetBody($title);//标题
		$input->SetAttach("test");
		$input->SetOut_trade_no($trade_no);//订单号
		$input->SetTotal_fee($total_fee);//支付金额 分为单位
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/".request()->instance()->module()."/".request()->instance()->controller()."/notify");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = $WxPayApi->unifiedOrder($input);
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/token/jsapi_ticket.json", json_encode($order), FILE_APPEND);
		if($order['result_code']=='FAIL'){
            $this->error($order['err_code_des']);
		}
		
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		$jsApiParameters = $tools->GetJsApiParameters($order);
                
        $result['jsApiParameters'] = $jsApiParameters;
        $result['order_id'] = $order_id;
        
        return view('pay');
		
    }
	
	public function notify(){
		require APP_PATH . "/../extend/Wxpay/lib/WxPay.Api.php";
		require APP_PATH . "/../extend/Wxpay/lib/WxPay.Notify.php";
		
		$notify = new \WxPayNotify();
		
		//验证数据
		$notify->Handle(false);
		//获取状态
		$res = $notify->GetValues();
		if($res['return_code'] ==="SUCCESS" && $res['return_msg'] ==="OK"){
			//获取返回的数据
            $data = file_get_contents("php://input");
		    $result = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			$order = db("orders");
			$order_data = $order->where("order_number=".(int)$result['out_trade_no'])->find();
			if($order_data['order_amount'] * 100 !=$result['total_fee'])
			{
				exit("error");
			}
            //更新订单表
			$order->where("id", $order_data['id'])->update(array("pay_status"=>"2","pay_method"=> 1,"pay_time"=>date('Y-m-d H:i:s'), "pay_trade_num"=>$result['transaction_id']));
        }else{
            //更新订单表
			$order->where("id", $order_data['id'])->update(array("pay_status"=>"4","pay_method"=> 1,"pay_time"=>date('Y-m-d H:i:s'), "pay_note"=> json_encode($result)));
        }
	}
	
	public function refund($order_id,$refund_fee=""){
		require APP_PATH . "/../extend/Wxpay/lib/WxPay.Api.php";
		
		if(!$order_id)
		{
			$res['code'] = 0;
            $res['msg'] = "参数错误";
			return $res;
		}
		
        $order_info = db('orders')->find($order_id);
        if(!$order_info){
            $this->error("订单不存在");
        }
        if($order_info['pay_method'] != 1){
            $this->error('非微信支付订单，不能退费');
        }
        if($order_info['pay_status'] == 3){
            $this->error('已退款');
        }
        		
		$transaction_id = $order_info["pay_trade_num"];
		$total_fee = $order_info['total_amount']*100;
		$refund_fee = $refund_fee?$refund_fee*100:$total_fee;
        
        if($refund_fee > $total_fee){
            $res['code'] = 0;
            $res['msg'] = "退款金额高于支付金额，无法退款";
			return $res;
        }
        
        $refund_id = $order_id;
        
        $input = new \WxPayRefund();
        $WxPayApi = new \WxPayApi();
        $WxPayConfig = new \WxPayConfig();
        $input->SetTransaction_id($transaction_id);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($refund_id);
        $input->SetOp_user_id($WxPayConfig::MCHID);
        $result = $WxPayApi->refund($input);

        if($result['result_code']=="SUCCESS" && $result['return_code']=='SUCCESS'){

            db("orders")->where("id",$order_id)->update(["pay_status"=>3,"order_status"=>4,"refund_time"=>date('Y-m-d H:i:s'),"refund_trade_num"=>$result['refund_id']]);

            $res['code'] = 1;
            $res['msg'] = "退费成功";
            return $res;
        }else{
            db("orders")->where("id",$order_id)->update(["pay_status"=>5,"refund_time"=>date('Y-m-d H:i:s'),"refund_note"=>json_encode($result)]);
            $res['code'] = 0;
            $res['msg'] = $result['err_code_des'];
            return $res;
        }
	}
	
	
	

}
