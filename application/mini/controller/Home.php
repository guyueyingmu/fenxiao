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
        $user_info = db('users')->field('id,credits,earn_total,nickname,img_url,distribution_level,dis_qrcode,account_balance')->find(session('mini.uid'));
        
        //营业总额，统计所有订单的总额
        if($user_info['distribution_level'] == 2){
            $user_info['total_amount'] = db('orders')->where('order_status', 5)->where('pay_method', 'IN', [1, 2])->sum('total_amount');
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
}
