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
            
            // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.png
            $thumb_save_name = getThumbUrl(str_replace("\\","/",$save_name));
            
//            if($width > $get_info['max_w']){
                $image->thumb($get_info['max_w'], $get_info['max_h'])->save($file_path . DS . $thumb_save_name);
//            }
                
            $res_url = $url_path . DS . $thumb_save_name;
            
            $this->success("上传成功","",["img_path" => $res_url, "big_img_path" => $url_path . DS . $save_name]);
            
        }else{
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }
    
    /**
     * 上传图片类型处理
     * @param string $img_type 图片类型 
     * @return int
     */
    public function upload_type($img_type = ''){
        if($img_type == 'good_img'){ //商品图片
            $res['max_w'] = 270;
            $res['max_h'] = 270;
        }elseif($img_type == 'good_banner_img'){ //商品轮播图
            $res['max_w'] = 540;
            $res['max_h'] = 540;
        }elseif($img_type == 'good_cat_img'){ //商品分类图
            $res['max_w'] = 120;
            $res['max_h'] = 120;
        }elseif($img_type == 'banner_img'){ //首页轮播图
            $res['max_w'] = 750;
            $res['max_h'] = 320;
        }elseif($img_type == 'message_img'){ //客服消息图片
            $res['max_w'] = 200;
            $res['max_h'] = 200;
        }else{ //其他图片
            $res['max_w'] = $res['max_h'] = 300;
        }
        return $res;
    }
}
