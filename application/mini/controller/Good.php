<?php
namespace app\mini\controller;

use think\Db;
/**
 * 商品
 */
class Good extends Base
{
    /**
     * 获取列表
     * @param int $list_type 商品类型 1积分类
     * @return type
     */
    public function get_list(){
        $page = input('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'status=1';
        $sort = 'sort DESC';
        $list_type = input('param.list_type', '', 'intval');
        if($list_type == 1){
            $where .= ' AND good_type IN (4,5)';
        }
        $keyword = input('param.keyword', '', 'trim');
        if($keyword){
            $where .= ' AND good_name LIKE "%'.$keyword.'%" ';
        }
        $cat_id = input('param.cat_id', '', 'intval');
        if($cat_id){
            $where .= ' AND cat_id = '. $cat_id;
        }
        $price_order = input('param.price_order', '', 'trim');
        if(in_array($price_order, ['asc', 'desc'])){
            $sort .= ',price '.$price_order;
        }
        
        $list = db('goods')->where($where)->order($sort)->page($page, $limit)
                ->field('id,good_title,good_img,price,credits,good_type')->select();
        
        $total = db('goods')->where($where)->count();
        
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
     * 商品详情
     */
    public function detail(){
        $id = input('param.id', '', 'intval');
        if(!$id){
            $this->error('参数错误');
        }
        $info = db('goods')->where('id', $id)->where('status', 1)->field('id,good_title,price,detail,good_type,credits,good_img')->find();
        if(!$info){
            $this->error('商品数据不存在');
        }
        
        //记录用户足迹
        Footmark::add($info);
        
        //轮播图
        $info['banner'] = db('goods_banner')->where('good_id', $id)->order('id DESC')->select();
        
        $info['is_collect'] = 0;
        //是否收藏
        $collect = db('goods_collect')->where('user_id', session('mini.uid'))->where('good_id', $id)->count();
        if($collect){
            $info['is_collect'] = 1;
        }
        //购物车数量
        if($info['good_type'] == 1){
            $info['cart_total'] = db('goods_cart')->where('user_id', session('mini.uid'))->sum('total');
        }
        
//        echo '<pre>';print_r($info);exit;
        $this->success('成功', '', $info);
    }
    
    /**
     * 分享商品
     */
    public function share(){
        $good_id = input('param.good_id', '', 'intval');
        if(!$good_id){
            $this->error('参数错误');
        }
        $add_data['user_id'] = session('mini.uid');
        $add_data['credits_in'] = config('share_good_credits');
        $add_data['credits_from'] = 3;
        $add_data['track_id'] = $good_id;
        $add_data['add_time'] = date('Y-m-d H:i:s');
        
        Db::startTrans();
        try{
            //添加积分记录
            db('users_credits_records')->insert($add_data);
            //修改用户表积分字段
            db('Users')->where('id', session('mini.uid'))->setInc('credits', config('share_good_credits'));
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
        
        $this->success('成功');
    }
}
