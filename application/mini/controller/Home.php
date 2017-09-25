<?php
namespace app\mini\controller;

class Home extends Base
{
    /**
     * 获取banner图
     * @return type
     */
    public function banner(){
        $list = db('banner')->where('status=1')->limit(3)->select();
        
        $this->success('成功', '', $list);
    }
    
    /**
     * 获取分类列表
     * @return type
     */
    public function get_cat_list(){
        $page = I('param.page', 1, 'intval');
        $limit = I('param.limit', 0, 'intval');
        
        if($limit){
            $list = db('goods_category')->order('sort DESC')->limit($limit)->field('id,cat_name,cat_img')->select();            
        }else{            
            $list = db('goods_category')->order('sort DESC')->field('id,cat_name,cat_img')->select();    
        }
        
        $this->success('成功', '', $list);
    }
}
