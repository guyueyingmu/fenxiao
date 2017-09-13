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
    private static $menu_id = 3;
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
        $where .= $keyword ? " AND (g.good_num LIKE '%$keyword%' OR g.good_name LIKE '%$keyword%')" : "";
        $where .= $cat_id ? " AND g.cat_id = $cat_id" : "";
        $where .= $good_type ? " AND g.good_type = $good_type" : "";
        $where .= $status ? " AND g.status = $status" : "";
        
        $goods = new Goods();
        $list = $goods->alias("g")
                ->join("__GOODS_CATEGORY__ gc", "gc.id=g.cat_id", "LEFT")
                ->where($where)
                ->field("g.id, g.good_name, g.specification, g.brand, g.price, g.credits, g.presenter_credits, g.good_type, g.distribution, g.status, g.sort, g.add_time, gc.cat_name")
                ->page($page,$limit)
                ->order('g.id DESC')
                ->select();
        if($list){
            foreach ($list as $k => $v){
                $list[$k]['add_time'] = date("Y-m-d H:i:s",$v['add_time']);
            }
        }
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
            $data['add_time'] = time();
            $good = new Goods($data);
            $good->save();
            
            //保存轮播图
            $good_banner = new GoodsBanner;
            foreach($banner_img as $v){
                if($v['img_url'] && $v['is_show'] == 1){
                    $list[] = ['good_id' => $good->id, 'img_url' => $v['img_url'], 'add_time' => time()];
                }
            }
            $banner_res = $good_banner->saveAll($list);
            
            //写日志
            $this->add_log(self::$menu_id,['title' => '添加商品', 'good' => $good, 'banner_img' => $banner_res]);
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error("添加失败");
        }
        $this->success("添加成功");
    }
    
    /**
     * 上传文件
     */
    public function upload(){         
        
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');

        //good_img:商品小图,banner_img:商品轮播图
        $img_type = input("param.img_type","",'trim');
        if(!$img_type){
            $img_type = 'default';
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        //图片大小不能超过2mb
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $save_name = $info->getSaveName();
            
            $res_url = DS . "uploads" . DS . $save_name;
            
            //图片处理
            $image = \think\Image::open(ROOT_PATH . 'public' . DS . 'uploads' . DS . $save_name);
            $width = $image->width();
            if($img_type == 'good_img'){
                $max_w = 320;
                $max_h = 320;
            }elseif($img_type == 'banner_img'){
                $max_w = 640;
                $max_h = 320;
            }else{
                $max_w = $max_h = 640;
            }
            if($width > $max_w){
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $thumb_save_name = getThumbUrl(str_replace("\\","/",$save_name));// echo $thumb_save_name."<br />";echo ROOT_PATH . 'public' . DS . 'uploads' . DS . $thumb_save_name;exit('xxx');
                $image->thumb($max_w, $max_h)->save(ROOT_PATH . 'public' . DS . 'uploads' . DS . $thumb_save_name);
                
                $res_url = DS . "uploads" . DS . $thumb_save_name;
            }
            
            $this->success("上传成功","",["img_path" => $res_url]);
            
        }else{
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
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
            $data['edit_time'] = time();
            Goods::update($data);
            
            //保存轮播图
            $good_banner = new GoodsBanner;
            $list = $delete_banners = $delete_list = array();
            foreach($banner_img as $v){
                if($v['id'] && $v['is_show'] == 2){
                    $delete_banners[] = $v['id'];
                    $delete_list[] = $v;
                }elseif(!$v['id'] && $v['img_url'] && $v['is_show'] == 1){
                    $list[] = ['good_id' => $data['id'], 'img_url' => $v['img_url'], 'add_time' => time()];
                }
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
            $this->error("编辑失败");
        }
            
        //写日志
        $this->add_log(self::$menu_id,['title' => '编辑商品', 'good' => $data, 'new_banner_img' => $banner_res, 'delete_banner_img' => $delete_list]);
        
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
            $this->add_log(self::$menu_id,['title' => '删除商品', 'data' => $good]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
