<?php
namespace app\admin\validate;

use think\Validate;

/*
 * 订单发货/服务验证规则
 */
class OrdersConsignment extends Validate
{
    protected $rule = [
            'consignment_user'  => 'require|max:50',
            'consignment_time'  => 'require|dateFormat:Y-m-d H:i:s',
            'deliver_method'  => 'require|in:1,2,3',
            'tracking_number'  => 'require|max:50',
            'service_user' => 'require|max:50',
            'service_time' => 'require|dateFormat:Y-m-d H:i:s',
            'note' => 'max:1000',
            'total_amount' => 'require|float',
        ];
    
    protected $message = [
            'consignment_user.require' => '请输入发货员名字',
            'consignment_user.max' => '发货员名字长度不能超过50',
            'consignment_time.require' => '请选择发货时间',
            'consignment_time.dateFormat' => '发货时间格式不正确',
            'deliver_method.require' => '请选择配送方式',
            'deliver_method.in' => '配送方式数据不正确',
            'tracking_number.require' => '请输入快递编号',
            'tracking_number.max' => '快递编号长度不能超过50',
            'service_user.require' => '请输入服务员名字',
            'service_user.max' => '服务员名字长度不能超过50',
            'service_time.require' => '请选择服务时间',
            'service_time.dateFormat' => '服务时间格式不正确',
            'note.max' => '备注长度不能超过1000',
            'total_amount.require' => '请输入收款总额',
            'total_amount.float' => '收款总额数据不正确',
        ];
    
    protected $scene = [
            'consignment'  =>  ['consignment_user', 'consignment_time', 'deliver_method', 'tracking_number'], //发货验证
            'service'  =>  ['servie_user', 'service_time', 'note'], //服务验证
            'service_amount'  =>  ['servie_user', 'service_time', 'note', 'total_amount'], //线下支付服务验证
        ];
}