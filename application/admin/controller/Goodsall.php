<?php
namespace app\admin\controller;

use app\admin\model\Goods;
use app\admin\model\GoodsCategory;
use app\admin\model\GoodsBanner;
use think\Db;

/**
 * 商品列表
 */
class Goodsall extends Base
{
    //定义当前菜单id
    public $menu_id = 3;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
    
    /**
     * 获取列表
     * @param string $keyword 商品编号/商品名
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
        $cat_id = input("param.cat_id", "", 'intval');
        $good_type = input("param.good_type", "", 'intval');
        $status = input("param.status", "", 'intval');
                
        $where = "1=1";
        $where .= $keyword ? " AND (g.good_num LIKE '%$keyword%' OR g.good_title LIKE '%$keyword%')" : "";
        $where .= $cat_id ? " AND g.cat_id = $cat_id" : "";
        $where .= $good_type ? " AND g.good_type = $good_type" : "";
        $where .= $status ? " AND g.status = $status" : "";
        
        $goods = new Goods();
        $list = $goods->alias("g")
                ->join("__GOODS_CATEGORY__ gc", "gc.id=g.cat_id", "LEFT")
                ->where($where)
                ->field("g.id, g.good_title, g.specification, g.brand, g.price, g.credits, g.presenter_credits, g.good_type, g.distribution, g.status, g.sort, g.add_time, gc.cat_name")
                ->page($page,$limit)
                ->order('g.id DESC')
                ->select();
        $total = $goods->alias("g")
                ->join("__GOODS_CATEGORY__ gc", "gc.id=g.cat_id", "LEFT")
                ->where($where)
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
        
        $this->success("成功", "", $result);
    }
    
    /**
     * 添加
     * @return string
     */
    public function add(){
        $data = input('post.', '', 'trim');
        
        if(in_array($data['good_type'],[4, 5])){
            $data['distribution'] = 2;
        }
        
        $validate_res = $this->validate($data, "Goods");
        if(true !== $validate_res){
            // 验证失败 输出错误信息
            $this->error($validate_res);
        }
        
        $banner_img = $data['banner_img'];
        unset($data['banner_img']);
        
        Db::startTrans();
        try{
            //保存商品表
            $data['admin_user_id'] = session("admin.uid");
            $data['add_time'] = date("Y-m-d H:i:s");
            $good = new Goods($data);
            $good->save();
            
            //保存轮播图
            $good_banner = new GoodsBanner;
            $have_banner_img = false;
            foreach($banner_img as $v){
                if($v['is_show'] == 1){
                    $have_banner_img = true;
                }
                if($v['img_url'] && $v['is_show'] == 1){
                    $list[] = ['good_id' => $good->id, 'img_url' => $v['img_url'], 'add_time' => date("Y-m-d H:i:s")];
                }
            }
            if($have_banner_img == false){
                exception("请上传商品轮播图");
            }
            
            $banner_res = $good_banner->saveAll($list);
            
            //写日志
            $this->add_log($this->menu_id,['title' => '添加商品', 'data' => ['good' =>$good, 'banner_img' => $banner_res]]);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("添加失败");
            $this->error($e->getMessage());
        }
        $this->success("添加成功");
    }
        
    /**
     * 获取单个商品信息
     * @param int $good_id 商品id
     */
    public function get_good_info(){
        $good_id = input("param.good_id", "", "intval");
        if(!$good_id){
            $this->error('参数错误');
        }
        
        $good_info = Goods::get($good_id);
        
        if(!$good_info){
            $this->error("数据不存在");
        }
        
        $banner_list = GoodsBanner::all(['good_id' => $good_id]);
        
        if($banner_list){
            foreach($banner_list as $k=>$v){
                $banner_list[$k]['is_show'] = 1;
            }
        }
        
        $good_info['banner_img'] = $banner_list;
        
        $this->success('成功', '', $good_info);
    }
    
    /**
     * 编辑
     * @param int $good_id 商品id
     * ....
     */
    public function edit(){
        $data = input('post.', '', 'trim');
        
        if(!isset($data['id']) || !$data['id']){
            $this->error("参数错误");
        }
        
        if(in_array($data['good_type'],[4, 5])){
            $data['distribution'] = 2;
        }
        
        $validate_res = $this->validate($data, "Goods");
        if(true !== $validate_res){
            // 验证失败 输出错误信息
            $this->error($validate_res);
        }
        
        $banner_img = $data['banner_img'];
        unset($data['banner_img']);
        
        Db::startTrans();
        try{
            //保存商品表
            $data['edit_admin_user'] = session("admin.uid");
            $data['edit_time'] = date("Y-m-d H:i:s");
            Goods::update($data);
            
            //保存轮播图
            $good_banner = new GoodsBanner;
            $list = $delete_banners = $delete_list = array();
            $have_banner_img = false;
            foreach($banner_img as $v){
                if($v['is_show'] == 1){
                    $have_banner_img = true;
                }
                if($v['id'] && $v['is_show'] == 2){
                    $delete_banners[] = $v['id'];
                    $delete_list[] = $v;
                }elseif(!$v['id'] && $v['img_url'] && $v['is_show'] == 1){
                    $list[] = ['good_id' => $data['id'], 'img_url' => $v['img_url'], 'add_time' => date("Y-m-d H:i:s")];
                }
            }
            if($have_banner_img == false){
                exception("请上传商品轮播图");
            }
            $banner_res = [];
            if($list){
                $banner_res = $good_banner->saveAll($list);
            }
            if($delete_banners){
                $good_banner->destroy($delete_banners);
            }
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error($e->getMessage());
        }
            
        //写日志
        $this->add_log($this->menu_id,['title' => '编辑商品', 'data' => ['good' => $data, 'new_banner_img' => $banner_res, 'delete_banner_img' => $delete_list]]);
        
        $this->success("编辑成功");
    }
    
    /**
     * 删除
     * @param int $good_id 商品id
     */
    public function del(){
        $good_id = input("param.good_id", "", "intval");
        if(!$good_id){
            $this->error("参数错误");
        }
        
        $good = Goods::get($good_id);
        if(!$good){
            $this->error("数据不存在");
        }
        $res = $good->delete();
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '删除商品', 'data' => $good]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
