<?php
namespace app\mini\controller;

use think\Controller;
/*
 * 短信
 */
class Sms extends Controller
{
    
    protected $AccessKeyId = 'LTAIgujKFdFpKVlk';
    protected $AccessKeySecret = 'HUMcYYStftdUnHPEebvzBHFufufAUP';
    protected $sign = '爱悦妍';    
    
    //发送验证码
    public function send_sms($phone) {
        if(!$phone){
            return false;
        }
        $verify = mt_rand(1000, 9999); //获取随机验证码		
        $msg = "您的验证码为" . $verify . "，本次验证码1分钟内有效，如非本人，请忽略！";
        
        if( !is_numeric($phone) || !preg_match("/1[3-9]{1}\d{9}$/",$phone) ) {
            $return_data = array(
                'code' => 40001,
                'msg' => "手机号格式错误"
            );
            return $return_data;
        }
        $ip = $_SERVER["REMOTE_ADDR"];
        $time = time();
        //限制60秒只能发送一次
        $count1 = db("sms_cache")->where("phone_number='$phone' and add_time+60>=$time")->count();
        if ($count1) {
            $return_data = array(
                'code' => 40003,
                'msg' => "太频繁发送了，请60秒后再发送" . $count1
            );
            return $return_data;
        }
        //一天内只能发送5条
        $start = date('Y-m-d 00:00:00', time());
        $end = date('Y-m-d 23:59:59', time());
        $count2 = db("sms_cache")->where("phone_number='$phone' and ip='$ip' and (add_time>= unix_timestamp('$start') AND add_time<= unix_timestamp('$end'))")->count();
        if ($count2 >= 5) {
            $return_data = array(
                'code' => 40003,
                'msg' => "一天内只能发送5条"
            );
            return $return_data;
        }
        
        $sms = new \Sms\Smsapi($this->AccessKeyId, $this->AccessKeySecret);
        $res = $sms->sendSms(
            $this->sign, // 短信签名
            "SMS_99140065", // 短信模板编号
            $phone, // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>$verify
            )
        );
        
        //添加发送记录
        $sms_data['phone_number'] = $phone;
        $sms_data['verify_code'] = $verify;   //验证码
        $sms_data['content'] = $msg;
        $sms_data['add_time'] = time();
        $sms_data['ip'] = $ip;
        if ($res->Code == 'OK') {
            $sms_data['status_send'] = 1;
            $return_data = array(
                'code' => 40000,
//                'verify'=>$verify,
                'msg' => "发送成功"
            );
        } else {
            $sms_data['status_send'] = 0;
            $return_data = array(
                'code' => 40010,
                'msg' => '发送失败,原因:' . $res->Message,
            );
        }
        $save_res = db("sms_cache")->insert($sms_data);
        if(!$save_res){
            $return_data = array(
                'code' => 40011,
                'msg' => $return_data['msg'].'。发送失败,原因:保存数据库失败',
            );
        }
        return $return_data;
    }
    
    /**
     * 验证短信验证码
     */
    public function check_code($array){
        if(!$array['phone'] || !$array['phone_code']){
            return [
                'code' => 40004,
                'msg' => '数据不完整'
            ];
        }
        $info = db('sms_cache')->where('phone_number = "'. $array['phone']. '" AND status_send = 1 AND status_use = 0 AND add_time+60 <= '. time())->order('id DESC')->select();
        if(!$info){
            return [
                'code' => 40003,
                'msg' => '验证码不正确'
            ];
        }
        $check = '';
        foreach($info as $k=>$v){
            if($v['verify_code'] == $array['phone_code']){
                $check = $k;
                break;
            }
        }
        if($check !== ''){
            //标记已使用
            $info[$check]['status_use'] = 1;
            db('sms_cache')->update($info[$check]);
            return [
                'code' => 40000,
                'msg' => '验证通过'
            ];
        }else{
            return [
                'code' => 40002,
                'msg' => '验证码不正确'
            ];            
        }
    }
}
