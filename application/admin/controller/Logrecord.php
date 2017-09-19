<?php
namespace app\admin\controller;

class Logrecord extends Base
{
	 //定义当前菜单id
    private static $menu_id = 24;
	
    public function get_list(){
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
		
		
		
		$total =db('admin_log')->where($map)->count();
		$list=db('admin_log')					
			->where($map)
			->order('id desc')
			->page($page,$limit)
			->select();
		if($list){
            foreach ($list as $k => $v){                
               $list[$k]['log_time'] = date("Y-m-d H:i:s",$v['log_time']);
               $list[$k]['admin_user_name'] = db('admin_user')->where('id = '.$v['admin_user_id'])->value('nickname');
            }
        }
		$admin_user = db('admin_user')->where('is_show=1')->select();
	
		$total_page = ceil($total/$limit);
		
		$result['list'] = $list;
		$result['admin_user'] = $admin_user;
		
		//分页
        $result['pages']['total'] = $total;
        $result['pages']['limit'] = $limit;
	    $result['pages']['total_page'] = $total_page;
		$result['pages']['current_page'] = $page;
		
      
        $this->success("成功","",$result);
    }

}
