<?php
namespace app\admin\controller;

class Logrecord extends Base
{
	 //定义当前菜单id
    private static $menu_id = 24;
	
    public function getlist(){
		$page = input("page",1,"intval");
		$limit = config('paginate.list_rows');
		$map = [];
		
		
		$keyword = input("keyword","","trim");
		$starttime = input("starttime","","trim");
		$endtime = input("endtime","","trim");	
		
		
		if($keyword){
           $map['admin_user_id']  =   $keyword;	
		}
		
        if($starttime && strtotime($starttime)){
            $map['log_time'] = array("EGT", strtotime($starttime." 00:00:00"));
        }
       
        if($endtime && strtotime($endtime)){
            $map['log_time'] = array("ELT", strtotime($endtime." 23:59:59"));
        }
		
		
		
		$count =db('admin_log')->where($map)->count();
		$list=db('admin_log')					
			->where($map)
			->order('id desc')
			->page($page,$limit)
			->select();
		
		$admin_user = db('admin_user')->where('is_show=1')->select();
	
		
		$result['list'] = $list;
		$result['admin_user'] = $admin_user;
        $result['total'] = $count;
        $result['limit'] = $limit;
        
        $this->success("成功","",$result);
    }

}
