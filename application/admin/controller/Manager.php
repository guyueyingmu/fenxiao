<?php
namespace app\admin\controller;

use app\admin\model\AdminUser;


//管理员管理
class Manager extends Base
{
     //定义当前菜单id
    private static $menu_id = 23;
	
    public function get_list(){
		$page = input("page",1,"intval");
		$limit = config('paginate.list_rows');
		$map = [];
		$keyword = input("keyword","","trim");
		$map['is_show'] = 1;
		if($keyword){
           $map['nickname']  =   array('like', '%'.(string)$keyword.'%');	
		}
		
		$total =db('admin_user')->where($map)->count();
		$list=db('admin_user')						
			->where($map)
			->order('id desc')
			->page($page,$limit)
			->select();
		if($list){ 
      
			foreach($list as $key=>$row){
					$list[$key]['add_time'] = date("Y-m-d H:i:s",$row['add_time']);
					$list[$key]['edit_time'] = date("Y-m-d H:i:s",$row['edit_time']);
					$list[$key]['login_time'] = date("Y-m-d H:i:s",$row['login_time']);
					$list[$key]['role_name']=db('admin_user_role')->where("id=".$row['role_id'])->value('role_name');	
				
			}
		}
		$total_page = ceil($total/$limit);	
	
		$result['list'] = $list;
        //分页
        $result['pages']['total'] = $total;
        $result['pages']['limit'] = $limit;
	    $result['pages']['total_page'] = $total_page;
		$result['pages']['current_page'] = $page;
        
        $this->success("成功","",$result);
    }
	
	
	//  修改/添加角色页
	public function add_user(){
		
		$user_id = input("user_id","","trim");
		if($user_id){ 
			$user = db('admin_user')->where('id ='.$user_id)->find();
			$result['user'] = $user;
		}	
		
		$role = db('admin_user_role')->where("role_name !='超级管理员'")->order('id desc')->select();		
        $result['role'] = $role;
    
        $this->success("成功","",$result);
	
	}
	
	// 保存/新增管理员
	public function save_user(){ 
		$user_id = input("user_id","","trim");	
		$password = input("password","","trim");
		$repassword = input("repassword","","trim");
		if($password && ($password != $repassword)){ 
            $this->error('两次密码不相同，请重新输入');
        }
     

	
		if($user_id){
			$data = [
				'id'  => $user_id,
				'user_name'  => input("user_name","","trim"),
				'nickname' => input("nickname","","trim"),
				'role_id'  => input("role_id","","trim"),
				'status' => input("status","","trim"),
            ];
            if($password){ 
                $data['password'] = md5($password);
            }
			$validate_res = $this->validate($data,[
				'id'  => 'require|number',
                'user_name'  => 'require|unique:admin_user',
                'nickname'  => 'require',
                'role_id'  => 'require',
			],[
				'id.require' => '参数错误',
				'id.number' => '参数格式错误',
				'user_name.require' => '后台登录账号不能为空！',
				'user_name.unique' => '该后台登录账号已存在！',
				'nickname.require' => '管理员姓名不能为空！',
				'role_id.require' => '所属角色不能为空！',           
			]); 
			
			if ($validate_res !== true) {
				$this->error($validate_res);
			}
			
			$data['edit_time'] = time();
			$new_user = db('admin_user')->where('id='.$user_id)->update($data);		
			//写入日志表
			$this->add_log(self::$menu_id,['title' => '更新管理员', 'data' => $data]);           
			//=============================

			$this->success("修改成功");		
		}else{
          
			$data = [				
				'user_name'  => input("user_name","","trim"),
				'nickname' => input("nickname","","trim"),
				'role_id'  => input("role_id","","trim"),
				'status' => input("status","","trim"),
            ];
            if($password){ 
                $data['password'] = md5($password);
            }

			$validate_res = $this->validate($data,[
	
				'user_name'  => 'require|unique:admin_user',
                'password'  => 'require',
                'nickname'  => 'require',
                'role_id'  => 'require',
                
			],[
			
                'password.require' => '密码不能为空',
				'user_name.require' => '后台登录账号不能为空！',
				'user_name.unique' => '该后台登录账号已存在！',
				'nickname.require' => '管理员姓名不能为空！',
				'role_id.require' => '所属角色不能为空！',           
			]); 
			
			if ($validate_res !== true) {
				$this->error($validate_res);
			}
			$data['add_time'] = time();			
			$new_user =  db('admin_user')->insertGetId($data);
			$data['id'] = $new_user;	
			//写入日志表
			$this->add_log(self::$menu_id,['title' => '新增管理员', 'data' => $data]);
			//=============================	
			$this->success("新增成功");
		}	

	}
	
	//   启用/禁用管理员
	public function modify_user(){ 
		$user_id = input("user_id","","trim");
		$data['status'] = input("status","","trim");	
		if(!$user_id){ 
			$this->error("用户不存在！");
        }
        $val = $data['status'] == 1?"启用管理员":'禁用管理员';
		$data['edit_time'] = time();
		$update = db('admin_user')->where('id='.$user_id)->update($data);
		$data['id'] = $user_id;
		//写入日志表		
		$this->add_log(self::$menu_id,['title' => $val, 'data' => $data]);
		//=============================
		$this->success("修改成功");
		
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
		
		$data['id'] = $user_id;
		//写入日志表
		$this->add_log(self::$menu_id,['title' => '删除管理员', 'data' => $data]);		
		//=============================	
		$this->success("删除成功");	
	
    }
    
	//查看权限
	public function view_ctrl(){ 
		$role_id = input("role_id","","trim");	
	
		if(!$role_id){ 
			$this->error("参数错误");
        }
        $user = db('admin_user_role')->where('id='.$role_id)->value('menu_auth');
        if($user=='all'){ 
            $auth = db('admin_menu')->where('pid != 0 and status =1')->column('id');
            //$auth = implode(",", $menu_auth);
        }else{
            if($user != ''){	
                $auth = explode(',',$user);
                
            }
        }

        $menu = db('admin_menu')->where('pid=0 and status = 1')->order('sort,id desc')->select();		
		foreach($menu as &$row){
			$row['label'] = $row['menu_name'];	
			$row['children']=db('admin_menu')->where('status = 1 and pid='.$row['id'])->order('sort,id desc')->select();
			foreach($row['children'] as &$v){ 
				$v['label'] = $v['menu_name'];
			}	
        }
        $data['menu'] =$menu;
        $data['auth'] =$auth;

        
		$this->success("删除成功",'',$data);	
	
    }
    
    
   
}
