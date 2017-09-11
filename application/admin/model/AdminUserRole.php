<?php
namespace app\admin\model;

use think\Model;

class AdminUserRole extends Model
{
    public function admin_user()
    {
        return $this->hasMany('AdminUser','role_id','','','LEFT');
    }
}