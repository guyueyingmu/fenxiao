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
 * @param string $type 获取类型 1 大图
 * @return string
 */
function getThumbUrl($img, $type = '') {

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
    
    if($type == 1){
        if('thumb_' != substr($s2, 0, 6)){
            return $img;
        }
        $s2 = str_replace('thumb_', '', $s2);
        
        $thumb = $s1. $s2;
    }else{
        //如果已经是缩略图则返回
        if('thumb_' == substr($s2, 0, 6)){
            return $img;
        }

        //否则添加缩略图前缀
        $thumb = $s1. 'thumb_' . $s2;
    }

    return $thumb;
}

/**
 * 计算分销商获佣金额
 * @param float $amount 商品总额
 * @param int $level 分销等级
 */
function get_earn_amount($amount, $level = 1){
    if($amount <= 0){
        return 0;
    }
    if($level == 1){
        $earn1 = $amount * (config('distribution_first_percent')/100);
        $earn2 = $earn1 * ((config('platform_service_percent') + config('platform_maintain_percent') + config('tax_percent'))/100);
        $earn = $earn1 - $earn2;
    }else{
        $earn1 = $amount * (config('distribution_first_percent')/100) * (config('distribution_second_percent')/100);
        $earn2 = $earn1 * ((config('platform_service_percent') + config('tax_percent'))/100);
        $earn = $earn1 - $earn2;
    }
    
    return $earn;
}

/**
 * 生成gatewayworker的group_id
 */
function get_group_id($user_id){
    return "user_$user_id";
}