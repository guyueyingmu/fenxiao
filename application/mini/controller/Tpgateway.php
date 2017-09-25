<?php
namespace app\mini\controller;

use GatewayClient\Gateway;

class Tpgateway
{
    public $register_address = '127.0.0.1:1238';
    
    public function __construct() {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        Gateway::$registerAddress = $this->register_address;
    }
    
    public function bind(){
        $client_id = input("param.client_id", "", "trim");

        // 假设用户已经登录，用户uid和群组id在session中
        $uid      = session("mini.user_id");
        $group_id = get_group_id($uid);
        // client_id与uid绑定
        Gateway::bindUid($client_id, $uid);
        // 加入某个群组（可调用多次加入多个群组）
        Gateway::joinGroup($client_id, $group_id);
    }
    
    public function send_msg(){
        $uid = session("mini.user_id");
        $message = input("param.message", "", "trim");
        $group = get_group_id($uid);
        
        $exclude_user = Gateway::getClientIdByUid($uid);
        // 向任意群组的网站页面发送数据
        Gateway::sendToGroup($group, $message, [$exclude_user]);
    }
}
