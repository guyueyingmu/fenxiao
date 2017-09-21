<?php
namespace app\admin\controller;

/**
 * 文件处理
 */
class Asset extends Base
{    
    /**
     * 上传文件
     */
    public function upload(){         
        
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');

        //good_img:商品小图,banner_img:商品轮播图
        $img_type = input("param.img_type","default",'trim');
        $file_path = ROOT_PATH . 'public' . DS . 'uploads' . DS . $img_type;
        $url_path = DS . 'uploads' . DS . $img_type;
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        //图片大小不能超过2mb
        $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png,gif'])->move($file_path);
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $save_name = $info->getSaveName();
            
            $res_url = $url_path . DS . $save_name;
            
            //图片处理
            $image = \think\Image::open($file_path . DS . $save_name);
            $width = $image->width();
            $get_info = $this->upload_type($img_type);
            
            if($width > $get_info['max_w']){
                // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
                $thumb_save_name = getThumbUrl(str_replace("\\","/",$save_name));// echo $thumb_save_name."<br />";echo ROOT_PATH . 'public' . DS . 'uploads' . DS . $thumb_save_name;exit('xxx');
                $image->thumb($get_info['max_w'], $get_info['max_h'])->save($file_path . DS . $thumb_save_name);
                
                $res_url = $url_path . DS . $thumb_save_name;
            }
            
            $this->success("上传成功","",["img_path" => $res_url]);
            
        }else{
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }
    
    public function upload_type($img_type = ''){
        if($img_type == 'good_img'){ //商品图片
            $res['max_w'] = 320;
            $res['max_h'] = 320;
        }elseif($img_type == 'good_banner_img'){ //商品轮播图
            $res['max_w'] = 640;
            $res['max_h'] = 320;
        }elseif($img_type == 'good_cat_img'){ //商品分类图
            $res['max_w'] = 100;
            $res['max_h'] = 100;
        }else{ //其他图片
            $res['max_w'] = $res['max_h'] = 640;
        }
        return $res;
    }
}
