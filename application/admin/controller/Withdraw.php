<?php
namespace app\admin\controller;

use think\Db;

/**
 * 提现申请
 */
class Withdraw extends Base
{
    //定义当前菜单id
    public $menu_id = 11;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户id，手机号码
     * @param int $status 提现审核状态（1待处理 2已同意 3拒绝）
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $status = input("param.status", "", 'intval');
                
        $where = "1=1";
        $where .= $keyword ? " AND (u.id LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $status ? " AND w.status = $status" : "";
        
        $withdraw = db('users_withdraw');
        $list = $withdraw->alias('w')
                ->join("__USERS__ u", "u.id=w.user_id", "LEFT")
                ->join("__ADMIN_USER__ au", "au.id=w.admin_user_id", "LEFT")
                ->where($where)
                ->field("w.id,w.user_id,u.nickname,u.phone_number,u.account_balance,w.amount,w.add_time,w.status,w.handle_time,w.admin_user_id,au.nickname admin_user_name")
                ->page($page,$limit)
                ->order('w.id DESC')
                ->select();
        $total = $withdraw->alias('w')
                ->join("__USERS__ u", "u.id=w.user_id", "LEFT")
                ->join("__ADMIN_USER__ au", "au.id=w.admin_user_id", "LEFT")
                ->where($where)->count();
        
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
//        exit(json_encode($result));
        $this->success("成功", "", $result);
    }
    
    /**
     * 提现处理
     * @param int $id 提现申请id
     * @param int $status 提现审核状态（2同意 3拒绝）
     */
    public function handle(){
        $data = input("param.", "", "trim");
        
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'status' => 'require|in:2,3',
        ],[
            'id.require' => '参数错误',
            'status.require' => '请选择处理状态',
            'status.in' => '处理状态数据不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $info = db("users_withdraw")->find($data['id']);
        if(!$info){
            $this->error("提现申请数据不存在");
        }
        if($info['status'] != 1){
            $this->error("已处理，不能重复处理");
        }
        
        Db::startTrans();
        try{
            
            if($data['status'] == 2){
        
                $user_account = db('users')->where('id', $info['user_id'])->value('account_balance');

                if($info['amount'] > $user_account){
                    exception('提现金额高于账户余额，无法提现');
                }
                
                //扣减用户账户余额
                db("users")->where('id', $info['user_id'])->setDec('account_balance', $info['amount']);
            }
            $data['handle_time'] = date("Y-m-d H:i:s");
            $data['admin_user_id'] = session("admin.uid");
            db('users_withdraw')->update($data);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("操作失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log($this->menu_id,['title' => '后台提现处理', 'data' => $data]);
        
        $this->success("操作成功");        
    }
}
