<?php
namespace app\admin\controller;

use app\mini\controller\Weixinapi;
/**
 * 分销二维码
 */
class Qrcode extends Base
{
    /**
     * 获取二维码
     */
    public function get_qrcode($user_id){
//        $user_id = input('param.user_id', '', 'intval');
        if(!$user_id){
            return false;
        }
        
        $info = db("qrcode")->where("user_id", $user_id)->find();
        
        $qrcode= "";
        if($info && $info['qrcode_url']){
            $qrcode = $info['qrcode_url'];
        }else{
            $max_scene_id = db("qrcode")->order("scene_id DESC")->field("scene_id")->find();
            if($max_scene_id){
                $scene_id = $max_scene_id['scene_id'] + 1;
            }else{
                $scene_id = 1;
            }            
            
            $weixin = new Weixinapi;
            $qrcode = $weixin->getQrcode($scene_id);
        }
        if(!$qrcode){
            return false;
        }
        
        //保存二维码名片
        if(!isset($info['qrcode_url']) && !$info['qrcode_url']){
            db("qrcode")->insert([
                'scene_id'=>$scene_id,
                'user_id'=>$user_id,
                'qrcode_url'=>$qrcode,
                'add_time'=>date('Y-m-d H:i:s'),
                'scan_total'=>0
            ]);
        }
        
        return $qrcode;
    }
}
