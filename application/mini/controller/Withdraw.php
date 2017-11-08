<?php
namespace app\mini\controller;

/**
 * 佣金提现
 */
class Withdraw extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $list = db('users_withdraw')
                ->where('user_id', session('mini.uid'))
                ->order('id DESC')
                ->page($page, $limit)
                ->field('add_time,amount,status')
                ->select();
        
        $total = db('users_withdraw')
                ->where('user_id', session('mini.uid'))
                ->count();
        
        if($list){
            $status = ['', '待处理', '已同意', '拒绝'];
            foreach($list as $k=>$v){
                $list[$k]['user_name'] = session('mini.nickname');
                $list[$k]['status_txt'] = $status[$v['status']];
            }
        }
        
        $total_page = ceil($total/$limit);
        
        $result = [
            'user_account' => db('users')->where('id', session('mini.uid'))->value('account_balance'),
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
     * 申请提现
     */
    public function add_apply(){
        $withdraw_amount = input('param.withdraw_amount', '', 'float');
        if($withdraw_amount <= 0){
            $this->error('请输入提现金额');
        }
        $user_account = db('users')->where('id', session('mini.uid'))->value('account_balance');
        if($user_account < $withdraw_amount){
            $this->error('提现金额高于账户余额，无法提现');
        }
        $add_data = [
            'user_id' => session('mini.uid'),
            'amount' => $withdraw_amount,
            'status' => 1,
            'add_time' => date('Y-m-d H:i:s'),
        ];
        $res = db('users_withdraw')->insert($add_data);
        if($res){
            $add_data['status_txt'] = '待处理';
            $add_data['user_name'] = session('mini.nickname');
            $this->success('申请提现成功', '', $add_data);
        }else{
            $this->error('申请提现失败');
        }
    }
}
