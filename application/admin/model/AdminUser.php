<?php
namespace app\admin\model;

use think\Model;

class AdminUser extends Model
{
    public function admin_user_role()
    {
        return $this->belongsTo('AdminUserRole','role_id','','','LEFT');
    }
}