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
    private static $menu_id = 8;
    
    /**
     * 获取列表
     * @param string $keyword 用户id
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
        $where .= $keyword ? " AND user_id=$keyword" : "";
        $where .= $start_time ? " AND add_time >= '$start_time 00:00:00'" : "";
        $where .= $end_time ? " AND add_time <= '$end_time 23:59:59'" : "";
        
        $goods = new GoodsComments();
        $list = $goods
                ->where($where)
                ->page($page,$limit)
                ->order('id DESC')
                ->select();
        if($list){
            foreach ($list as $k => $v){
                $list[$k]['good_link'] = url('/mini/Good/info',['id' => $v['good_id']]); //前台商品链接
            }
        }
        $total = $goods->where($where)->count();
        
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
        exit(json_encode($result));
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
            $this->add_log(self::$menu_id,['title' => '删除商品评论', 'data' => $comment]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
