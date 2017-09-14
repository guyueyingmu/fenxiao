<?php
namespace app\admin\model;

use think\Model;

class OrderService extends Model
{
    public function orders()
    {
        return $this->belongsTo('Orders', 'order_id');
    }
}