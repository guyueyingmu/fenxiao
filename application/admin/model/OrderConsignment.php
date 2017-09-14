<?php
namespace app\admin\model;

use think\Model;

class OrderConsignment extends Model
{
    public function orders()
    {
        return $this->belongsTo('Orders', 'order_id');
    }
}