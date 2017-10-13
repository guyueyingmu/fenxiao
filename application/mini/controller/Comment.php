<?php
namespace app\mini\controller;

use think\Db;
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
    
    //验证数据
    
    /**
     * 添加评论
     * @param int $order_id 订单id
     * @param string $good_info 商品评论信息 json字符串
     * good_info包含
     * [
     *      {
     *          good_id:1,//必填
     *          content:'',//非必填
     *          stars:5,//必填
     *          imgs:['www.baidu.com','www.google.com'],//非必填
     *      }
     * ]
     */
    public function add(){
//        $data = [
//            ['good_id'=>4,'content'=>'bbb','stars'=>4,'imgs'=>['baidu1','google1']],
//            ['good_id'=>3,'content'=>'aaa','stars'=>5,'imgs'=>['baidu','google']],
//        ];
//                exit(json_encode($data));
        $order_id = input('param.order_id', '', 'trim');
        if(!$order_id){
            $this->error('参数错误');
        }
        $good_info = input('param.good_info', '', 'trim');
        $good_info = json_decode($good_info, true);
        if(!is_array($good_info)){
            $this->error('数据格式不正确');
        }
        
        $order_info = db('orders')->where('id', $order_id)->where('user_id', session('mini.uid'))->field('id,order_status,pay_status')->find();
        if(!$order_info){
            $this->error('订单不存在');
        }
        if($order_info['order_status'] != 5){
            $this->error('订单未完成，不能评价');
        }
        
        $comment_good_id = [];
        $add_data_all = [];
        foreach($good_info as $k=>$v){
            $data = [
                'good_id' => $v['good_id'],
                'content' => $v['content'] ? $v['content'] : '好评',
                'stars' => $v['stars'],
                'imgs' => $v['imgs'],
            ];
            $validate_res = $this->validate($data,[
                'good_id'  => 'require',
                'content' => 'require|max:1000',
                'stars' => 'require|in:1,2,3,4,5',
                'imgs' => 'array',
            ],[
                'good_id.require' => '缺少商品id',
                'content.require' => '请输入评论内容',
                'content.max' => '评论内容长度不能超过1000',
                'stars.require' => '请选择评价星级',
                'stars.in' => '评价星级数据不正确',
                'imgs.array' => '图片格式不正确',
            ]); 
            if ($validate_res !== true) {
                $this->error($validate_res);
            }
            $comment_good_id[] = $v['good_id'];
            
            $data['order_id'] = $order_id;
            $data['user_id'] = session('mini.uid');
            $data['add_time'] = date('Y-m-d H:i:s');
            $data['imgs'] = $data['imgs'] ? json_encode($data['imgs']) : '';
            
            $add_data_all[] = $data;
        }
        
        $order_goods = db('orders_goods')->alias('og')
                ->join('__ORDERS__ o', 'o.id=og.order_id', 'LEFT')
                ->where('og.order_id', $order_id)
                ->where('o.user_id', session('mini.uid'))
                ->where('o.order_status', 5)
                ->column('og.good_id');
        
        if(array_diff($order_goods, $comment_good_id)){
            $this->error('商品数据和订单内的商品信息不吻合');
        }
        
        //是否已评价
        $exist = db('goods_comments')->where('order_id', $order_id)->where('user_id', session('mini.uid'))->count();
        if($exist){
            $this->error('不能重复评论');
        }
        
        Db::startTrans();
        try{
            
            db('goods_comments')->insertAll($add_data_all);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
        
        $this->success('评价成功');
        
    }
    
    /**
     * 获取订单的商品信息
     */
    public function get_order_goods(){
        $order_id = input('param.order_id', '', 'intval');
        if(!$order_id){
            $this->error('参数错误');
        }
        $order_info = db('orders')->where('id', $order_id)->where('user_id', session('mini.uid'))->field('id,order_status')->find();
        if(!$order_info){
            $this->error('订单不存在');
        }
        if($order_info['order_status'] != 5){
            $this->error('订单未完成，不能评价');
        }
        $good_list = db('orders_goods')->where('order_id', $order_id)
                ->field('good_id,good_img,good_title')->select();
        if(!$good_list){
            $this->error('订单有误');
        }
        exit(json_encode($good_list));
        $this->success('成功', '', $good_list);
    }
}
