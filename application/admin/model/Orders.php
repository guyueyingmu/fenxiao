<?php
namespace app\admin\model;

use think\Model;

class Orders extends Model
{
    public function ordersGoods()
    {
        return $this->hasMany('OrdersGoods', 'order_id');
    }
    
    public function orderService()
    {
        return $this->hasOne('OrderService', 'order_id');
    }
    
    public function orderConsignment()
    {
        return $this->hasOne('OrderConsignment', 'order_id');
    }
}