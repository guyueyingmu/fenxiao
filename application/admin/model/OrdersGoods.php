<?php
namespace app\admin\model;

use think\Model;

class OrdersGoods extends Model
{
    public function orders()
    {
        return $this->belongsTo('Orders', 'order_id');
    }
}