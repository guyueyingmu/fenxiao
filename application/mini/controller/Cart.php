<?php
namespace app\mini\controller;

/**
 * 购物车
 */
class Cart extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        
        $list = db('goods_cart')->alias('gc')
                ->join('__GOODS__ g', 'g.id=gc.good_id', 'LEFT')
                ->where('gc.user_id', session('mini.uid'))
                ->order('gc.id DESC')
                ->field('gc.good_id,gc.total,g.good_title,g.good_img,g.price')
                ->select();
        
        $this->success('成功', '', $list);
    }
    
    /**
     * 加入购物车
     */
    public function add(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        $good_info = db('goods')->where('id', $good_id)->where('status', 1)->field('id,good_title,good_type')->find();
        if(!$good_info){
            $this->error('商品不存在');
        }
        if($good_info['good_type'] != 1){
            $this->error('只有实物类商品可以加入购物车');
        }
        $cart = db('goods_cart')->where('user_id', session('mini.uid'))->where('good_id', $good_id)->find();
        if($cart){
            $this->success('已加入购物车');
        }
        $add_data['user_id'] = session('mini.uid');
        $add_data['good_id'] = $good_id;
        $add_data['total'] = 1;
        $add_data['add_time'] = date('Y-m-d H:i:s');
        $res = db('goods_cart')->insert($add_data);
        if($res){
            $this->success('加入购物车成功');
        }else{
            $this->error('加入购物车失败');
        }
    }
    /**
     * 删除购物车商品
     */
    public function del(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        $res = db('goods_cart')->where('good_id', $good_id)->where('user_id', session('mini.uid'))->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
