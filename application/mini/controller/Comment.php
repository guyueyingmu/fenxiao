<?php
namespace app\mini\controller;

/**
 * 评论
 */
class Comment extends Base
{
    /**
     * 获取列表
     * @param int $keyword 1好评 2中评 3差评
     * @return type
     */
    public function get_list(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        
        $page = input('param.page', 1, 'intval');
        $limit = 2;//config('mini_page_limit');
        
        $where = 'gc.good_id = '.$good_id.' AND gc.delete_time IS NULL';
        $keyword = input('param.keyword', '', 'trim');
        if($keyword == 1){
            $where .= ' AND gc.stars=5';
        }elseif($keyword == 2){
            $where .= ' AND gc.stars IN (3,4)';
        }elseif($keyword == 3){
            $where .= ' AND gc.stars IN (1,2)';
        }
        
        $list = db('goods_comments')->alias('gc')
                ->join('__USERS__ u', 'u.id=gc.user_id', 'LEFT')
                ->where($where)->order('gc.id DESC')->page($page, $limit)
                ->field('gc.id,gc.content,gc.stars,gc.imgs,gc.add_time,u.nickname,u.img_url')->select();
        
        $total = db('goods_comments')->alias('gc')
                ->join('__USERS__ u', 'u.id=gc.user_id', 'LEFT')
                ->where($where)->count();
        
        if($list){
            foreach($list as $k=>$v){
                if($v['imgs']){
                    $list[$k]['imgs'] = json_decode($v['imgs'],true);
                }
            }
        }
        
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
     * 添加评论
     */
    public function add(){
        $data = [
            'good_id' => input('param.good_id', '', 'trim'),
            'order_id' => input('param.order_id', '', 'trim'),
            'content' => input('param.content', '', 'trim'),
            'stars' => input('param.stars', '', 'trim'),
            'imgs' => input('param.imgs', '', 'trim'),
        ];
                
        $validate_res = $this->validate($data,[
            'good_id'  => 'require',
            'order_id' => 'require',
            'content' => 'require|max:1000',
            'stars' => 'require|in:1,2,3,4,5',
            'imgs' => 'array',
        ],[
            'good_id.require' => '参数错误',
            'order_id.require' => '参数错误',
            'content.require' => '请输入评论内容',
            'content.max' => '评论内容长度不能超过1000',
            'stars.require' => '请选择评价星级',
            'stars.in' => '评价星级数据不正确',
            'imgs.array' => '图片格式不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $info = db('orders_goods')->alias('og')
                ->join('__ORDERS__ o', 'o.id=og.order_id', 'LEFT')
                ->where('og.order_id', $data['order_id'])
                ->where('og.good_id', $data['good_id'])
                ->where('o.user_id', session('mini.uid'))
                ->where('o.order_status', 5)
                ->field('og.good_id,og.order_id')
                ->find();
        if(!$info){
            $this->error('只能评价自己购买完成的商品');
        }
        
        //是否已评价
        $exist = db('goods_comments')->where('good_id', $data['good_id'])->where('order_id', $data['order_id'])->where('user_id', session('mini.uid'))->find();
        if($exist){
            $this->error('不能重复评论');
        }
        
        $data['user_id'] = session('mini.uid');
        $data['add_time'] = date('Y-m-d H:i:s');
        $data['imgs'] = $data['imgs'] ? json_encode($data['imgs']) : '';
        $res = db('goods_comments')->insert($data);
        if($res){
            $this->success('评价成功');
        }else{
            $this->error('评价失败');
        }
    }
}
