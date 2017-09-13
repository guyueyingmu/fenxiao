<?php
namespace app\admin\validate;

use think\Validate;

/*
 * 商品验证规则
 */
class Goods extends Validate
{
    protected $rule = [
            'good_img'  => 'require|max:200',//商品小图
            'banner_img' => 'require|array',//商品轮播图
            'brand' => 'max:50',//品牌
            'good_num' => 'max:50',//商品编号
            'good_name' => 'require|max:50',//商品名
            'cat_id' => 'require|number',//商品分类
            'supplier' => 'max:50',//供应商
            'color' => 'max:50',//商品颜色
            'specification' => 'max:50',//商品规格
            'good_type' => 'require|in:1,2,3,4,5',//商品类型
            'price' => 'require|float',//销售价格
            'distribution' => 'require|in:1,2',//参与分销
            'credits' => 'number',//积分兑换
            'status' => 'require|in:1,2',//是否上架
            'inventory' => 'number',//可用库存
            'presenter_credits' => 'number',//赠送积分
            'sort' => 'between:0,999',//排序
            'good_title' => 'require|max:200',//商品标题
            'detail' => 'require',//商品详情
        ];
    
    protected $message = [
            'good_img.require' => '请上传商品小图',
            'good_img.max' => '商品小图长度不能超过200',
            'banner_img.require' => '请上传商品轮播图',
            'banner_img.array' => '商品轮播图数据格式不正确',
            'brand.max' => '品牌长度不能超过50',
            'good_num.max' => '商品编号长度不能超过50',
            'good_name.require' => '请输入商品名',
            'good_name.max' => '商品名长度不能超过50',
            'cat_id.require' => '请选择商品分类',
            'cat_id.number' => '商品分类数据不正确',
            'supplier.max' => '供应商长度不能超过50',
            'color.max' => '商品颜色长度不能超过50',
            'specification.max' => '商品规格长度不能超过50',
            'good_type.require' => '请选择商品类型',
            'good_type.in' => '商品类型数据不正确',
            'price.require' => '请输入销售价格',
            'price.float' => '销售价格数据不正确',
            'distribution.require' => '请选择是否参与分销',
            'distribution.in' => '是否参与分销数据不正确',
            'credits.number' => '积分兑换数据不正确',
            'status.require' => '请选择是否上架',
            'status.in' => '是否上架数据不正确',
            'inventory.number' => '可用库存数据不正确',
            'presenter_credits.number' => '赠送积分数据不正确',
            'sort.between' => '排序数据不正确',
            'good_title.require' => '请输入商品标题',
            'good_title.max' => '商品标题长度不能超过200',
            'detail.require' => '请输入商品详情',
        ];
    

}