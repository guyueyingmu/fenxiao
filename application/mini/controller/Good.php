<?php
namespace app\mini\controller;

/**
 * 商品
 */
class Good extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        $page = I('param.page', 1, 'intval');
        $limit = config('mini_page_limit');
        
        $where = 'status=1';
        $sort = 'sort DESC';
        $keyword = I('param.keyword', '', 'trim');
        if($keyword){
            $where .= ' AND good_name LIKE "%'.$keyword.'%" ';
        }
        $cat_id = I('param.cat_id', '', 'intval');
        if($cat_id){
            $where .= ' AND cat_id = '. $cat_id;
        }
        $price_order = I('param.price_order', '', 'trim');
        if(in_array($price_order, ['asc', 'desc'])){
            $sort .= ',price '.$price_order;
        }
        
        $list = db('goods')->where($where)->order($sort)->page($page, $limit)
                ->field('id,good_name,good_img,price,credits,good_type')->select();
        
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
        
    }
}
