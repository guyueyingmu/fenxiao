<?php
namespace app\mini\controller;

/**
 * 用户足迹
 */
class Footmark extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $list = db('users_footmark')
                ->where('user_id', session('mini.uid'))
                ->order('edit_time DESC')
                ->page($page, $limit)
                ->select();
        
        $total = db('users_footmark')
                ->where('user_id', session('mini.uid'))
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
//        exit(json_encode($result));
        $this->success('成功', '', $result);
    }
    
    /**
     * 添加
     * @param array $info 商品信息
     */
    public function add($info){
        $exist = db('users_footmark')->where('user_id', session('mini.uid'))->where('good_id', $info['id'])->find();
        if($exist){
            $res = db('users_footmark')->update([
                'id' => $exist['id'],
                'edit_time' => date('Y-m-d H:i:s')
            ]);
        }else{
            $res = db('users_footmark')->insert([
                'user_id' => session('mini.uid'),
                'good_id' => $info['id'],
                'good_title' => $info['good_title'],
                'good_img' => $info['good_img'],
                'price' => $info['price'],
                'good_type' => $info['good_type'],
                'credits' => $info['credits'],
                'add_time' => date('Y-m-d H:i:s'),
                'edit_time' => date('Y-m-d H:i:s'),
            ]);
        }
        return $res;
    }
    
    /**
     * 删除
     */
    public function del(){
        $id = input('param.id', '', 'intval');
        if(!$id){
            $this->error('参数错误');
        }
        $res = db('users_footmark')->where('id', $id)->where('user_id', session('mini.uid'))->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
