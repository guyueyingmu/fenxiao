<?php
namespace app\mini\controller;

/**
 * 计划任务 Hour
 *  分销团队奖，分销总额奖
 * 每个月1号跑一次
 * 测试脚本
 */
class Prize extends \think\Controller{
	
	 
    /*
     * 分销团队奖计划任务
     */
    public function handle_team(){  
        $day = date("d");
        if($day != '01'){
            exit('只有1号才可以运行脚本');
        }
        $last_month = date('Y-m-d', strtotime('-1 month'));
        $this_month = date('Y-m-d');
        $exist = db('team_prize')->where("add_time", ">=", $this_month)->count();
        if($exist){
            exit('一天只能运行一次脚本');
        }
        $fxs = db('users')->where('distribution_level', 2)->where('status', 1)->field('id')->select();
        if($fxs){
            $config = db('config')->where('c_type', 1)->where('c_name', 'team_first_people')->whereOr('c_name', 'team_second_people')->whereOr('c_name', 'distribution_first_percent')
                    ->whereOr('c_name', 'distribution_second_percent')->column('c_name,c_value');
            
            foreach($fxs as $k=>$v){
                //一级分销会员数
                $first_count = db('users')->where('pid', $v['id'])->where("dis_scan_time", ">=", $last_month)->where("dis_scan_time", "<", $this_month)->where("status", 1)->count();
                
                if($first_count >= $config['team_first_people']){
                    $total_amount = db('orders')->alias('o')
                            ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                            ->where('u.pid', $v['id'])
                            ->where('o.order_status', 5)
                            ->where('o.pay_method', 'IN', [1, 2])
                            ->where('o.add_time>=u.dis_scan_time')
                            ->where('o.finish_time', '>=', $last_month)
                            ->where('o.finish_time', '<', $this_month)
                            ->sum('o.total_amount');
                    if($total_amount){
                        $log_data['earn_amount'] = $total_amount*($config['distribution_first_percent']/100)*0.1;
                        $log_data['earn_user_id'] = $v['id'];
                        $log_data['level'] = 1;
                        $log_data['status'] = 1;
                        $log_data['add_time'] = date('Y-m-d H:i:s');
                        $log_data['member_num'] = $first_count;
                        db('team_prize')->insert($log_data);
                    }
                }
                //二级分销会员数
                $second_count = db('users')->where('status', 1)->where('pid IN (SELECT id FROM mb_users WHERE pid='. $v['id']. ')')
                        ->where("dis_scan_time", ">=", $last_month)->where("dis_scan_time", "<", $this_month)->count();
                if($second_count >= $config['team_second_people']){
                    $total_amount = db('orders')->alias('o')
                            ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                            ->where('u.pid IN (SELECT id FROM mb_users WHERE pid='. $v['id']. ')')
                            ->where('o.order_status', 5)
                            ->where('o.pay_method', 'IN', [1, 2])
                            ->where('o.add_time>=u.dis_scan_time')
                            ->where('o.finish_time', '>=', $last_month)
                            ->where('o.finish_time', '<', $this_month)
                            ->sum('o.total_amount');
                    if($total_amount){
                        $log_data['earn_amount'] = $total_amount*($config['distribution_first_percent']/100)*($config['distribution_second_percent']/100)*0.1;
                        $log_data['earn_user_id'] = $v['id'];
                        $log_data['level'] = 2;
                        $log_data['status'] = 1;
                        $log_data['add_time'] = date('Y-m-d H:i:s');
                        $log_data['member_num'] = $second_count;
                        db('team_prize')->insert($log_data);
                    }
                }
            }
        }
    }
    
    /*
     * 分销总额奖计划任务
     */
    public function handle_total(){      
        $day = date("d");
        if($day != '01'){
            exit('只有1号才可以运行脚本');
        }
        $last_month = date('Y-m-d', strtotime('-1 month'));
        $this_month = date('Y-m-d');
        $exist = db('total_amount_prize')->where("add_time", ">=", $this_month)->count();
        if($exist){
            exit('一天只能运行一次脚本');
        }
        
        //特殊分销商
        $fxs = db('users')->where('distribution_level', 2)->where('status', 1)->where('sepcial_dis', 1)->field('id')->select();
        if($fxs){
            $config = db('config')->where('c_type', 1)->where('c_name', 'total_price_percent')->column('c_name,c_value');
            
            foreach($fxs as $k=>$v){
                $all_childs = $this->all_child([$v['id']]);
                if($all_childs){
                    $where = 'u.id IN ('. implode(',', $all_childs) .')';
                }else{
                    $where = '1=1';
                }
                $total_amount = db('orders')->alias('o')
                        ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                        ->where('o.order_status', 5)
                        ->where('o.pay_method', 'IN', [1, 2])
                        ->where('o.add_time>=u.dis_scan_time')
                        ->where('o.finish_time', '>=', $last_month)
                        ->where('o.finish_time', '<', $this_month)
                        ->where($where)
                        ->sum('o.total_amount');
                if($total_amount){
                    $log_data['earn_amount'] = $total_amount*($config['total_price_percent']/100);
                    $log_data['earn_user_id'] = $v['id'];
                    $log_data['status'] = 1;
                    $log_data['add_time'] = date('Y-m-d H:i:s');
                    db('total_amount_prize')->insert($log_data);
                }
            }
        }
        
    }
    
    public function all_child($pid, $ids = []){
        $childs = db('users')->where('pid', 'IN', $pid)->column('id');
        if($childs){
            $ids = array_merge($ids, $childs);
            return $this->all_child($childs, $ids);
        }else{
            return $ids;
        }
    }
    
}
