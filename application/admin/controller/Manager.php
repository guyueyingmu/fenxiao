<?php
namespace app\admin\controller;

use think\Controller;
use think\Paginator;

use app\admin\model\AdminUser;
use app\admin\model\AdminUserLogin;
use app\admin\model\AdminUserRole;
use app\admin\model\AdminMenu;

class Manager extends Controller
{
    public function index(){
		$page = input("page",1,"intval");
		$limit = config('paginate.list_rows');
		$map = [];
		$keyword = input("keyword","","trim");
		$map['is_show'] = 1;
		if($keyword){
           $map['nickname']  =   array('like', '%'.(string)$keyword.'%');	
		}
		
		$count =db('admin_user')->where($map)->count();
		$list=db('admin_user')						
			->where($map)
			->order('id desc')
			->paginate($limit,$count);
		$data = $list->all();
		foreach($data as $key=>$row){
				$row['role_name']=db('admin_user_role')->where("id=".$row['role_id'])->value('role_name');	
				$list[$key]=$row;
		}	
	
		$show = $list->render();// 分页显示输出
		
		print_r($list);exit;
		
		
		$this->assign('Page',$show);	
        $this->assign('list',$list);
        return view();
    }
	
	
	//  修改/添加角色页
	public function add_user(){
		
		$user_id = input("user_id","","trim");	
		$user = db('admin_user')->where('id ='.$user_id)->find();
		
		$role = db('admin_user_role')->where("role_name !='超级管理员'")->order('sort,id desc')->select();		

		$this->assign('user',$user);
		$this->assign('role',$role);
		return view();
	

	}
	
	// 保存/新增角色
	public function save_role(){ 
		$user_id = input("user_id","","trim");	
		
		$data['user_name'] = input("user_name","","trim");
		$data['nickname'] = input("nickname","","trim");
		$password = input("password","","trim");
		$data['role_id'] = input("role_id","","trim");
		$data['status'] = input("status","","trim");
		
		if(!$data['user_name']){ 
			$this->error("后台登录账号不能为空！");
		}
		if(!$data['nickname']){ 
			$this->error("管理员姓名不能为空！");
		}
		
		if($password){ 
			$data['password'] = md5($password);
		}
		
		if(!$data['role_id']){ 
			$this->error("所属角色不能为空！");
		}

		if($user_id){
			$data['edit_time'] = time();
			$new_user = db('admin_user')->where('id='.$user_id)->update($data);
			//写入日志表
		
		
			//=============================
			
			
			$this->success("修改成功");
			/*
			$return['code'] = 40000;
			$return['msg']="修改成功";
			exit(json_encode($return));
			*/
			
			
			
		}else{
			$data['add_time'] = time();			
			$new_user =  db('admin_user')->insert($data);	
			//写入日志表
		
		
			//=============================	
			$this->success("新增成功");
			/*
			$return['code'] = 40000;
			$return['msg']="新增成功";
			exit(json_encode($return));
			*/
		}	

	}
	
	//   启动/禁用管理员
	public function modify_user(){ 
		$user_id = input("user_id","","trim");
		$status = input("status","","trim");	
		if(!$user_id){ 
			$this->error("用户不存在！");
		}
		if($status == 1){ 
			$data['is_show'] = 2;
		}elseif($status == 2){ 
			$data['is_show'] = 1;
		}else{ 
			$this->error("数据错误！！");
		}
		$data['edit_time'] = time();
		$del = db('admin_user')->where('id='.$user_id)->update($data);
		//写入日志表
		
		
		//=============================
		
		$return['code'] = 40000;
		$return['msg']="修改成功";
		exit(json_encode($return));
	}

	
	//删除管理员
	public function del_user(){ 
		$user_id = input("user_id","","trim");	
		if(!$user_id){ 
			$this->error("用户不存在！");
		}
		$data['is_show'] = 2;
		$data['edit_time'] = time();
		$del = db('admin_user')->where('id='.$user_id)->update($data);
		//写入日志表
		
		
		//=============================
		
		$return['code'] = 40000;
		$return['msg']="删除成功";
		exit(json_encode($return));
	}
    
   
}
