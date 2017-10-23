<?php
namespace app\mini\controller;
use think\Controller;
use app\mini\controller\Weixinapi;
/*
 * 微信入口处理
 */
class Index extends Controller
{
    public function index(){
        return view();
    }
    
    public function gateway(){
        return view();
    }
    
    //跳转判断处理
    public function goweixin(){
        $param = input(); 
        
        $go_url = $param['gourl'];
        $url = $this->getMiniUrl($go_url);
        if(!$url){
            $this->error("参数错误");
        }
        
        if($param){//传递其他参数的处理
            $other_param = "";
            foreach($param as $k=>$v){
                if($k != "gourl"){
                    $other_param .= "/$k/$v";
                }
            }
        }
        
        if($other_param){
            $url .= $other_param;
        }
        
        $state = urlencode($url);
        
        $redirect_url = "http://".$_SERVER['HTTP_HOST']."/".request()->instance()->module()."/".request()->instance()->controller()."/getOpenId";
        
        $weixin = new Weixinapi();
        $weixin->getAuthCode('snsapi_base',$redirect_url,$state,true);
    }
    
    /**微信
     * 获取用户openid并跳转
     * @return bool|string
     */
    public function getOpenId(){
        $code = input("code");
        $go_url   = urldecode(input('state'));

        $weixin = new Weixinapi();
        
        $return = $weixin->getAuthAccessToken($code);
        
        if(isset($return['errcode'])){
//            $cookData = cookie("weixin_open_arr");
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/log/weixin_openid.json", json_encode($return), FILE_APPEND);
//            $return = $weixin->getAuthRefreshAccessToken($cookData['refresh_token']);
            header("Content-type:text/html;charset=utf-8");
            exit("获取openid失败，请重试");
        }
		
        if(isset($return['openid'])){
			//生成cookies
//            cookie("weixin_open_arr",$return,3600*24);
			
            $user_data = db("users")->where("openid='".$return['openid']."'")->field("id,openid,phone_number,img_url,nickname,sex,status,distribution_level,pid")->find();
            
            if(!$user_data){
                $this->error('用户不存在，请重新关注');
            }
            if($user_data['status'] == 2){
                $this->error("用户被禁用，请联系管理员");
            }
            
            //更新用户登录信息 
            db("users")->where("id=".$user_data['id'])->update(array_merge($user_data,array("last_login_time"=>date('Y-m-d H:i:s'))));
            
            session("mini",$user_data);
            session("mini.uid",$user_data['id']);
            
			ob_start();
            header("Location:".$go_url);
            ob_end_flush();
            exit();
        }else{
            header("Content-type:text/html;charset=utf-8");
            exit("openid错误");
        }
    }
    
    /**
     * 链接映射
     * @param $status
     */
    public function getMiniUrl($status){
        $host = "http://".$_SERVER['HTTP_HOST'];
        switch($status){
            case 1://购物商城
                $url =  $host."/mini/Index/index/#";
                break;
            case 2://个人中心
                $url =  $host."/mini/Index/index/#/userCenter";
                break;
            case 3://积分兑换
                $url =  $host."/mini/Index/index/#/search/list_type/1";
                break;
        }
        return $url;
    }
    
    /*
     * 生成菜单
     */
    public function add_menu(){
        $menu_data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/weixin_menu.json"),true);
//        echo "<pre>";print_r($menu_data);exit;
        if($menu_data){
            $weixin = new Weixinapi();
            $res = $weixin->menuManage('save',$menu_data);
            echo "<pre>";print_r($res);exit;
        }else{
            echo "菜单数据不存在";exit;
        }  
    }
}
