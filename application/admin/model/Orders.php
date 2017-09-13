<?php
namespace app\admin\model;

use think\Model;

class Orders extends Model
{
    public function ordersGoods()
    {
        return $this->hasMany('OrdersGoods', 'order_id');
    }
}