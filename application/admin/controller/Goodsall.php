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
    /**
     * 获取列表
     * @param string $keyword 商品编号/商品名
     * @return string
     */
    public function get_list(){
        
//        $list = 
    }
    
    /**
     * 添加
     * @param type $name Description
     * @return string
     */
    public function add(){
        $data = input('post.');
//        $banner_img = [
//                ['id'=>'1','img_url'=>'baidu.com'],
//                ['id'=>'2','img_url'=>'baidu.com'],
//            ];
//        exit(json_encode($banner_img));
        $validate_res = $this->validate($data, "Goods");
        if(true !== $validate_res){
            // 验证失败 输出错误信息
            $this->error($validate_res);
        }
        
        $banner_img = $data['banner_img'];
        unset($data['banner_img']);
        
        $banner_img = json_decode($banner_img,true);
        
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
                if($v['img_url']){
                    $list[] = ['good_id'=>$good->id,'img_url'=>$v['img_url']];
                }
            }
            $good_banner->saveAll($list);
            
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
//        $img = '20160820/42a79759f284b767dfcb2a0197904287.jpg';
//        $a = getThumbUrl($img);
//        echo $a;exit;
        
        
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
}
