<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 获取缩略图地址
 * @param string $img 原图地址
 * @return string
 */
function getThumbUrl($img) {

    $img = trim($img);
    if('' == $img){
        return $img;
    }
    
    $pos = strrpos($img, '/');
    if(false !== $pos){
        $s1 = substr($img, 0, $pos + 1);
        $s2 = substr($img, $pos + 1);
    }else{
        $s1 = '';
        $s2 = $img;
    }

    //如果已经是缩略图则返回
    if('thumb_' == substr($s2, 0, 6)){
        return $img;
    }

    //否则添加缩略图前缀
    $thumb = $s1. 'thumb_' . $s2;

    return $thumb;
}