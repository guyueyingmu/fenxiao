<?php
namespace app\admin\controller;


use think\Paginator;


class Role extends Base
{
	public function index(){ 
		return view();
	}
	
    public function getlist(){
		$page = input("page",1,"intval");
		$limit = config('paginate.list_rows');
		$map = [];
		$keyword = input("keyword","","trim");
		if($keyword){
           $map['role_name']  =   array('like', '%'.(string)$keyword.'%');	
		}
		
		$count =db('admin_user_role')->where($map)->count();
		$list=db('admin_user_role')						
			->where($map)
			->order('id desc')
			->page($page,$limit)
			->select();
		
		foreach($list as $key=>$row){
				$row['type']=db('admin_user')->where("role_id=".$row['id'])->count();	//type=1表示该角色已被使用	
				$list[$key]=$row;
		}	
		
		print_r($count);
		print_r($list);exit;
		
		$result['list'] = $list;
        $result['total'] = $count;
        $result['limit'] = $limit;
        
        $this->success("成功","",$result);
    }
	
	
	//  修改/添加角色页
	public function add_role(){
		
		$role_id = input("role_id","","trim");	
		$role = db('admin_user_role')->where('id ='.$role_id)->find();
		$role['menu_auth'] = explode(',',$role['menu_auth']);
		
		
		$menu = db('admin_menu')->where('pid=0 and status = 1')->order('sort,id desc')->select();		
		foreach($menu as &$row){ 
			$row['child_list']=db('admin_menu')->where('status = 1 and pid='.$row['id'])->order('sort,id desc')->select();	
		}
		print_r($menu);
		print_r($role);exit;
		
		$result['role'] = $role;
        $result['menu'] = $menu;
    
        $this->success("成功","",$result);
		
		
	}
	
	// 保存/新增角色
	public function save_role(){ 
		$role_id = input("role_id","","trim");	
		$data['role_name'] = input("role_name","","trim");	
		$data['menu_auth'] = input("menu_auth","","trim");//逗号隔开的菜单id
		
		if(!$data['role_name']){ 
			$this->error("角色名不能为空！");
		}
		
		$check =db('admin_user_role')->where("role_name ='".$data['role_name']."'")->count();
		if(!$role_id && $check){ 
			$this->error("该角色已存在！");
		}
		
		if($role_id){
			$data['edit_time'] = time();
			$new_role = db('admin_user_role')->where('id='.$role_id)->update($data);
			//写入日志表
			$this->add_log(['更新角色', 'admin_user_role.id' => $role_id]);
		
			//=============================
			$this->success("修改成功");
			/*
			$return['code'] = 40000;
			$return['msg']="修改成功";
			exit(json_encode($return));
			*/
			
			
			
		}else{
			$data['add_time'] = time();			
			$new_role =  db('admin_user_role')->insertGetId($data);	
			//写入日志表
			$this->add_log(['新增角色', 'admin_user_role.id' => $new_role]);
		
			//=============================
			$this->success("新增成功");
			/*
			$return['code'] = 40000;
			$return['msg']="新增成功";
			exit(json_encode($return));
			*/
		}	

	}
	
	//删除角色
	public function del_role(){ 
		$role_id = input("role_id","","trim");	
		if(!$role_id){ 
			$this->error("角色不存在！");
		}
		$del = db('admin_user_role')->where('id='.$role_id)->delete();
		//写入日志表
		$this->add_log(['删除角色', 'admin_user_role.id' => $role_id]);
		
		//=============================
		
		$return['code'] = 40000;
		$return['msg']="删除成功";
		exit(json_encode($return));
		
		
		
		
	}
    
   
}
