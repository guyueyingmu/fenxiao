<?php
namespace app\mini\controller;

use think\Db;

class Home extends Base
{
    /**
     * 获取banner图
     * @return type
     */
    public function banner(){
        $list = db('banner')->where('status=1')->limit(3)->select();
        
        $this->success('成功', '', $list);
    }
    
    /**
     * 获取分类列表
     * @return type
     */
    public function get_cat_list(){
        $page = I('param.page', 1, 'intval');
        $limit = I('param.limit', 0, 'intval');
        
        if($limit){
            $list = db('goods_category')->order('sort DESC')->limit($limit)->field('id,cat_name,cat_img')->select();            
        }else{            
            $list = db('goods_category')->order('sort DESC')->field('id,cat_name,cat_img')->select();    
        }
        
        $this->success('成功', '', $list);
    }
    
    /**
     * 个人中心信息
     */
    public function center_info(){
        $user_info = db('users')->field('id,credits,earn_total,nickname,img_url,distribution_level,account_balance,sex,phone_number')->find(session('mini.uid'));
        
        //营业总额，统计所有订单的总额
        if($user_info['distribution_level'] == 2){
            $user_info['total_amount'] = db('orders')->where('order_status', 5)->where('pay_method', 'IN', [1, 2])->sum('total_amount');
            //分销二维码
            $user_info['dis_qrcode'] = db('qrcode')->where('user_id', $user_info['id'])->value('qrcode_url');
        }
        
        //签到天数
        $user_info['sign_total'] = db('signin')->where('user_id', session('mini.uid'))->count();
        
        $this->success('成功', '', $user_info);
    }
    
    /**
     * 佣金记录
     */
    public function distribution_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'earn_user_id='. session('mini.uid'). ' AND status=2';
        $list = db('order_distribution_log')->where($where)->order('earn_time DESC')->page($page, $limit)
                ->field('id,earn_amount_input,level,earn_time')->select();
        
        $total = db('order_distribution_log')->where($where)->count();
        
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
        
        $this->success('成功', '', $result);
    }
    
    /**
     * 积分记录
     */
    public function credits_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'user_id='. session('mini.uid'). ' AND status=2';
        $list = db('users_credits_records')->where($where)->order('earn_time DESC')->page($page, $limit)
                ->field('id,credits_in,credits_out,credits_from,add_time')->select();
        
        $total = db('users_credits_records')->where($where)->count();
        
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
        
        $this->success('成功', '', $result);
    }
    
    /**
     * 签到
     */
    public function signin(){
        Db::startTrans();
        try{
            //签到记录
            $singin_id = db('signin')->insertGetId([
                'user_id' => session('mini.uid'),
                'signin_date' => date('Y-m-d'),
                'add_time' => date('Y-m-d H:i:s'),
            ]);
            //积分记录
            db('users_credits_records')->insert([
                'user_id' => session('mini.uid'),
                'credits_in' => config('signin_credits'),
                'credits_from' => 1,
                'track_id' => $singin_id,
                'add_time' => date('Y-m-d H:i:s'),
            ]);
            //修改用户积分
            db('users')->where('id', session('mini.uid'))->setInc('credits', config('signin_credits'));
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("发货失败");
            $this->error($e->getMessage());
        }
        $this->success('今天已签到  +'. config('signin_credits'). '积分');
    }
    
    /**
     * 获取验证码
     */
    public function get_verify(){
        $captcha = new \think\captcha\Captcha;
        $captcha->fontSize = 30;
        $captcha->length   = 4;
        $captcha->useCurve = false;
        return $captcha->entry();
    }
    
    /**
     * 发送短信验证码
     */
    public function send_phone_code(){
        $data = [
            'phone' => input("phone","","trim"),
            'verify' => input("verify","","trim"),
        ];
        $validate_res = $this->validate($data,[
            'phone'  => 'require|regex:1[3-9]{1}\d{9}$',
            'verify' => 'require|captcha',
        ],[
            'phone.require' => '请输入手机号码',
            'phone.regex' => '手机号码格式不正确',
            'verify.require' => '请输入验证码',
            'verify.captcha' => '验证码不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $phone = db('users')->where('id', session('mini.uid'))->value('phone_number');
        if($phone == $data['phone']){
            $this->error('请不要绑定同一个手机号');
        }
        
        //发送短信验证码
        $res = Sms::get_code($data['phone']);
        if($res['code'] == 40000){
            $this->success('验证码发送成功');
        }else{
            $this->error($res['msg']);
        }
    }
    
    /**
     * 绑定手机号
     */
    public function bind(){
        $data = [
            'phone' => input("phone","","trim"),
            'phone_code' => input("phone_code","","trim"),
        ];
        $validate_res = $this->validate($data,[
            'phone'  => 'require|regex:1[3-9]{1}\d{9}$',
            'phone_code' => 'require',
        ],[
            'phone.require' => '请输入手机号码',
            'phone.regex' => '手机号码格式不正确',
            'phone_code.require' => '请输入短信验证码',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $res = Sms::check_code($data);
        if($res['code'] != 40000){
            $this->error($data['msg']);
        }
        
        $update_res = db('users')->update([
            'id' => session('mini.uid'),
            'phone_number' => $data['phone']
        ]);
        if($update_res){
            $this->success('绑定成功');
        }else{
            $this->error('绑定失败');
        }
    }
    
    /**
     * 申请代理
     */
    public function dis_apply(){
        $user_info = db('users')->field('phone_number')->find(session('mini.uid'));
        if(!$user_info['phone_number']){
            $this->error('未绑定手机号');
        }
        $res = db('distribution_apply')->insert([
            'user_id' => session('mini.uid'),
            'status' => 1,
            'add_time' => date('Y-m-d H:i:s'),
        ]);
        if($res){
            $this->error('申请成功');
        }else{
            $this->error('申请失败');
        }
    }
}
