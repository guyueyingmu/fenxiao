<?php
namespace app\admin\controller;

//日志管理
class Logrecord extends Base
{
	 //定义当前菜单id
    public $menu_id = 24;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
        
        $this->check_auth();
    }
	
    public function get_list(){
		$page = input("page",1,"intval");
		$limit = config('paginate.list_rows');
		$map = [];
		
		
		$keyword = input("keyword","","trim");
		$starttime = input("starttime","","trim");
		$endtime = input("endtime","","trim");	
		        
        $map = "1=1";
        $map .= $keyword ? " AND admin_user_id=$keyword" : "";
        $map .= $starttime ? " AND log_time >= '".strtotime($starttime." 00:00:00")."'" : "";
        $map .= $endtime ? " AND log_time <= '".strtotime($endtime." 23:59:59")."'" : "";
		
		
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
			   $list[$k]['menu_name'] = db('admin_menu')->where('id = '.$v['menu_id'])->value('menu_name');
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
