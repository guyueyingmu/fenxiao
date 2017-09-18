<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\Orders;

/**
 * 换货申请
 */
class Exchange extends Base
{
    //定义当前菜单id
    private static $menu_id = 7;
    
    /**
     * 获取列表
     * @param string $keyword 关键词搜索
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND (o.order_number LIKE '%$keyword%' OR u.phone_number LIKE '%$keyword%')" : "";
        $where .= $start_time ? " AND e.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND e.add_time <= '$end_time 23:59:59'" : "";
        
        $list = db('orders_exchange_apply')->alias("e")
                ->join("__ORDERS__ o", "o.id=e.order_id", "LEFT")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where($where)
                ->field("e.*,u.phone_number,u.nickname,o.order_number,o.add_time order_add_time")
                ->page($page,$limit)
                ->order('e.id DESC')
                ->select();
        $total = db('orders_exchange_apply')->alias("e")
                ->join("__ORDERS__ o", "o.id=e.order_id", "LEFT")
                ->join("__USERS__ u", "u.id=o.user_id", "LEFT")
                ->where($where)
                ->count();
        
        $total_page = ceil($total/$limit);
        
        $result = [
            "list" => $list,
            "pages" => [
                "total" => $total,
                "total_page" => $total_page,
                "limit" => $limit,
                "current_page" => $page,
                "manage_user" => session("admin.nickname")                
            ]
        ];
       // exit(json_encode($result));
        $this->success("成功", "", $result);
    }
    
    /**
     * 换货操作
     * @param int $id 换货申请id
     * @param string $handle_user 处理员名字
     * @param datetime $handle_time 处理时间
     * @param int $status 处理状态（0未处理，1同意，2拒绝）
     * @param string $handle_note 处理备注
     */
    public function handle(){
        $data = input("param.", "", "trim");
                
        $validate_res = $this->validate($data,[
            'id'  => 'require',
            'handle_user' => 'require|max:50',
            'handle_time' => 'require|dateFormat:Y-m-d H:i:s',
            'status' => 'require|in:1,2',
            'handle_note' => 'max:1000',
        ],[
            'id.require' => '参数错误',
            'handle_user.require' => '请输入处理员',
            'handle_user.max' => '处理员长度不能超过50',
            'handle_time.require' => '请选择处理时间',
            'handle_time.dateFormat' => '处理时间格式不正确',
            'status.require' => '请选择处理状态',
            'status.in' => '处理状态数据不正确',
            'handle_note.max' => '处理备注长度不能超过1000',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $exchange = db("orders_exchange_apply")->find($data['id']);
        if(!$exchange){
            $this->error("换货申请不存在");
        }
        
        Db::startTrans();
        try{
            //保存发货记录
            $data['admin_user_id'] = session("admin.uid");
            db('orders_exchange_apply')->update($data);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("操作失败");
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log(self::$menu_id,['title' => '后台换货操作', 'data' => $data]);
        
        $this->success("操作成功");
    }
           
}
