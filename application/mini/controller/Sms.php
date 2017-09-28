<?php
namespace app\mini\controller;

use think\Controller;
/*
 * 短信
 */
class Sms extends Controller
{
    protected $url = 'http://106.ihuyi.cn/webservice/sms.php';//http://106.ihuyi.cn/webservice/sms.php?method=Submit
    protected $account = 'cf_koude';
    protected $password = '978be5cf27106e82d07cbf0696670ec0';

    function send_sms($phone, $msg) {
        $method = 'get';
        if ($method == 'get') {//get
            $url = $this->url . "?method=Submit&account=" . $this->account . "&password=" . $this->password . "&mobile=" . $phone . "&content=" . $msg;
            $result = xml_to_array(curl_url_get($url));
        } else {//post
            $url = $this->url . "?method=Submit";
            $data = array(
                'account' => $this->account,
                'password' => $this->password,
                'mobile' => $phone,
                'content' => $msg
            );
            $result = xml_to_array(curl_url_post($url, $data));
        }
        //生成发送日志
        if ($result['SubmitResult']['code'] == 2) {
            $return_data['code'] = 40000;
            $return_data['msg'] = "发送成功";
        } else {
            $return_data['code'] = $result['SubmitResult']['code'];
            $return_data['msg'] = $result['SubmitResult']['msg'];
        }


        return $return_data;
    }

    public function get_code($phone) {
        if(!$phone){
            return false;
        }
        $verify = mt_rand(1000, 9999); //获取随机验证码		
//        $msg = "您的本次操作的验证码为：" . $verify . "。请在页面中完成验证，三十分钟内有效。";
        $msg = "您的验证码是：" . $verify . "。请不要把验证码泄露给其他人。";

        $return_data = $this->send($phone, $verify, $msg);
        return $return_data;
    }

    public function send($phone, $verify, $msg) {
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
        $count1 = db("sms_cache")->where("phone_number='$phone' and create_time+60>=$time")->count();
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
        $count2 = db("sms_cache")->where("phone_number='$phone' and ip='$ip' and (create_time>= unix_timestamp('$start') AND create_time<= unix_timestamp('$end'))")->count();
        if ($count2 >= 5) {
            $return_data = array(
                'code' => 40003,
                'msg' => "一天内只能发送5条"
            );
            return $return_data;
        }


//        $res = ["code"=>40000];
        $res = $this->send_sms($phone, $msg);
            //添加发送记录
            $sms_data['phone_number'] = $phone;
            $sms_data['verify_code'] = $verify;   //验证码
            $sms_data['content'] = $msg;
            $sms_data['create_time'] = time();
            $sms_data['ip'] = $ip;
        if ($res['code'] == 40000) {
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
                'msg' => '发送失败,原因:' . $res['msg'],
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
        $info = db('sms_cache')->where('phone_number', $array['phone'])->where('status_send', 1)->where('status_use', 0)->order('id DESC')->select();
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
