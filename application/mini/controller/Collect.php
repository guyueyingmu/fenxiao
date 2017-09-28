<?php
namespace app\mini\controller;

/**
 * 收藏
 */
class Collect extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $list = db('goods_collect')->alias('gc')
                ->join('__GOODS__ g', 'g.id = gc.good_id', 'LEFT')
                ->where('g.user_id', session('mini.uid'))
                ->order('gc.id DESC')
                ->page($page, $limit)
                ->field('gc.good_id,g.good_title,g.good_img,g.price,g.credits,g.good_type')
                ->select();
        
        $total = db('goods_collect')->alias('gc')
                ->join('__GOODS__ g', 'g.id = gc.good_id', 'LEFT')
                ->where('g.user_id', session('mini.uid'))
                ->count();
        
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
     * 添加收藏
     */
    public function add(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        $info = db('goods')->where('id', $good_id)->where('status', 1)->field('id,good_title')->find();
        if(!$info){
            $this->error('商品数据不存在');
        }
        //是否已收藏
        $collect = db('goods_collect')->where('user_id', session('mini.uid'))->where('good_id', $good_id)->count();
        if($collect){
            $this->success('已收藏');
        }
        //加入收藏
        $add_data['user_id'] = session('mini.uid');
        $add_data['good_id'] = $good_id;
        $add_data['add_time'] = date('Y-m-d H:i:s');
        $res = db('goods_collect')->insert($add_data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
    /**
     * 删除收藏
     */
    public function del(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        $res = db('goods_collect')->where('good_id', $good_id)->where('user_id', session('mini.uid'))->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
