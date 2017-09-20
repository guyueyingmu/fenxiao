<?php
namespace app\admin\controller;

use think\Db;
use app\admin\model\GoodsComments;

/**
 * 商品评论
 */
class Goodcomment extends Base
{
    //定义当前菜单id
    public $menu_id = 8;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 用户id
     * @param string $start_time 开始时间
     * @param string $end_time 结束时间
     * @param int $delete 是否已删除 1已删除
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $start_time = input("param.start_time", "", 'trim');
        $end_time = input("param.end_time", "", 'trim');
        $delete = input("param.delete", "", 'trim');
                
        $where = "1=1";
        $where .= $delete == 1 ? " AND c.delete_time IS NOT NULL" : "";
        $where .= $keyword ? " AND c.user_id=$keyword" : "";
        $where .= $start_time ? " AND c.add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND c.add_time <= '$end_time 23:59:59'" : "";
        
        $comments = new GoodsComments();
        $list = $comments->alias('c')
                ->join("__ADMIN_USER__ au", "au.id=c.admin_user_id", "LEFT")
                ->where($where)
                ->field("c.*,au.nickname")
                ->page($page,$limit)
                ->order('c.id DESC')
                ->select();
        if($list){
            foreach ($list as $k => $v){
                $list[$k]['good_link'] = url('/mini/Good/info',['id' => $v['good_id']]); //前台商品链接
            }
        }
        $total = $comments->alias('c')->where($where)->count();
        
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
     * 删除
     * @param int $id 评论id
     */
    public function del(){
        $id = input("param.id", "", "intval");
        if(!$id){
            $this->error("参数错误");
        }
        
        $comment = GoodsComments::get($id);
        if(!$comment){
            $this->error("数据不存在");
        }
        $res = $comment->update(['id' => $id, 'admin_user_id' => session("admin.uid"), 'delete_time' => date("Y-m-d H:i:s")]);
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '删除商品评论', 'data' => $comment]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
