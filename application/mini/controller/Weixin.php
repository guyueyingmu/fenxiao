<?php
namespace app\mini\controller;
use think\Controller;
use app\mini\controller\Weixinapi;

//微信相关操作类,如验证，推送，定位等
class Weixin extends Controller
{
	
	public function index(){
        
	}
	
	
	public function valid()
    {
		//获取原始post数据
		$data = file_get_contents("php://input");

		$postData = array();
		
		if($data){
			$postData = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
		}
		
        $echoStr = input("echostr");

        if ($this->checkSignature()) {
			
            echo $echoStr;
			
        } else {
            echo "no";
        }
		
		
		
		
		if($postData )
		{
			$weixin =new Weixinapi();
			
			switch ($postData['MsgType']) {
                // 接收事件推送
                case 'event':
                    switch($postData['Event']){
                        //关注
                        case 'subscribe': 
                            $content = '欢迎关注';
							echo  $weixin->replyTextTemplate($postData['FromUserName'], $postData['ToUserName'], $content);
							
                            $this->add_user($postData);
//							if(isset($postData['EventKey']) && $postData['EventKey']) {
//								//判断qrscene是否存在，从而判断是否为扫描带参数二维码事件
//                                $qrscene = preg_match ("/qrscene/i", $postData['EventKey']);
//                                if($qrscene){
//                                    $this->add_user($postData);
//                                }
//							}
							break;
                        //扫描带参数二维码事件 用户已关注时的事件推送
                        case 'SCAN':
                            echo '';
							$this->add_user($postData);
                            break;
                        // 点击自定菜单链接
                        case 'VIEW':
                            echo "";
                            break;							
                        case 'TEMPLATESENDJOBFINISH'://模板消息发送完成
                            echo "";						
                            break;
                        case 'CLICK':
                            echo "";
//                            if($postData['EventKey'] == 'kefu'){
//                                $kf_phone = db("config")->where("c_name","kf_phone")->where("status",1)->value("c_value");
//                                $content = '客服服务时间：工作日09:00-18:00，客服电话：<a href="tel:'.$kf_phone.'">'.$kf_phone.'</a>';
//                                $weixin->pushServerMsg($postData['FromUserName'], $content, 'text');
//                            }
							break;
					}
					break;
				case "text":
				case 'image'://图片
				case "voice"://语音
				case "video"://视频
				case "shortvideo"://小视频
				case "location"://地理位置
				case "link"://链接消息
                    echo "";
                    break;
				default://其他消息 地理位置消息 链接消息
					echo "";
                    
					break;
			}
		}
		
		
	}
	
	private function checkSignature() {

        $signature = input("signature");
        $timestamp = input("timestamp");
        $nonce = input("nonce");

		
        $token = 'weixin';
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
		
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
	/*
     * 添加用户数据
     */
    public function add_user($postData){
        //用户是否已注册
        $user_info = db('users')->where('openid', $postData['FromUserName'])->find();
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/token/test.json", '1'.json_encode($postData), FILE_APPEND);
        $parent_user_id = 0;
        if(isset($postData['EventKey']) && $postData['EventKey']) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/token/test.json", '2', FILE_APPEND);
            //判断qrscene是否存在，从而判断是否为扫描带参数二维码事件
            $qrscene = preg_match ("/qrscene/i", $postData['EventKey']);
            if($qrscene){
                $scene_id = str_replace('qrscene_', '', $postData['EventKey']);file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/token/test.json", '3/'.$scene_id, FILE_APPEND);                
            }else{
                $scene_id = $postData['EventKey'];
            }
            db("qrcode")->where("scene_id",$scene_id)->setInc("scan_total");//扫码数+1
            $parent_user_id = db("qrcode")->where("scene_id",$scene_id)->value("user_id");
        }
        
        $weixin = new Weixinapi();
        //获取用户信息
        $weixin_user_info = $weixin->getUserInfo($postData['FromUserName']);
        if(isset($weixin_user_info['errcode']) && $weixin_user_info['errcode']){
            //$weixin->addErrorLog('调用获取用户信息接口失败：' . session("weixin.openid") . PHP_EOL . print_r($user_info, true));
        }else{
            //保存用户信息
            if($weixin_user_info['subscribe'] != 0){
                $user_info['nickname'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $weixin_user_info['nickname']);                   
                $user_info['img_url'] = $weixin_user_info['headimgurl'];                    
                $user_info['sex'] = $weixin_user_info['sex'];
                $user_info['province'] = $weixin_user_info['province'];
                $user_info['city'] = $weixin_user_info['city'];
            }
        }
        
        if(isset($user_info['id']) && $user_info['id']){
            if($parent_user_id && $user_info['distribution_level'] == 0 && $user_info['pid'] == ''){
                $user_info['distribution_level'] = 1;
                $user_info['pid'] = $parent_user_id;
                $user_info['dis_scan_time'] = date('Y-m-d H:i:s');
            }
            $user_info['last_login_time'] = date('Y-m-d H:i:s');
            db('users')->update($user_info);
        }else{
            $user_info['openid'] = $postData['FromUserName'];
            if($parent_user_id){
                $user_info['distribution_level'] = 1;
                $user_info['pid'] = $parent_user_id;
                $user_info['dis_scan_time'] = date('Y-m-d H:i:s');
            }
            $user_info['register_time'] = date('Y-m-d H:i:s');
            $user_info['last_login_time'] = date('Y-m-d H:i:s');
            //注册用户账号
            $user_info['id'] = db('users')->insertGetId($user_info);            
        }
    }
    
}
