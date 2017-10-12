<?php
namespace app\mini\controller;

use think\Controller;
/*
 * 短信
 */
class Smsdemo extends Controller
{

    public function send_msg(){
        // 调用示例：

        header('Content-Type: text/plain; charset=utf-8');
        $demo = new \Sms\Smsapi("LTAIgujKFdFpKVlk", "HUMcYYStftdUnHPEebvzBHFufufAUP");
//        $demo = new \Smsapi\Smsapi(
//            "LTAIgujKFdFpKVlk",
//            "HUMcYYStftdUnHPEebvzBHFufufAUP"
//        );

        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "爱悦妍", // 短信签名
            "SMS_99140065", // 短信模板编号
            "18520126749", // 短信接收者
            Array(  // 短信模板中字段的值
                "code"=>"12345"
            )
        );
        print_r($response);

        echo "SmsDemo::queryDetails\n";
        $response = $demo->queryDetails(
            "18520126749",  // phoneNumbers 电话号码
            "20171012", // sendDate 发送时间
            10, // pageSize 分页大小
            1 // currentPage 当前页码
            // "abcd" // bizId 短信发送流水号，选填
        );

        print_r($response);
    }

}