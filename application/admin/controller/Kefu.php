<?php
namespace app\admin\controller;

use think\Db;
use GatewayClient\Gateway;

/**
 * 在线客服
 */
class Kefu extends Base
{
    //定义当前菜单id
    public $menu_id = 28;
    public $register_address = '127.0.0.1:1238';
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
                
        $this->check_auth();
        
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        Gateway::$registerAddress = $this->register_address;
    }
    
    /**
     * 绑定客户端和uid
     * @param int $user_id 用户id
     */
    public function bind(){
        $client_id = input("param.client_id", "", "trim");
		session("admin.kefu_client_id", $client_id);
		$admin_uid = session("admin.uid");
        // 绑定客服client和uid
        $uid      = 'admin'. $admin_uid;
		// client_id与uid绑定
		$res = Gateway::bindUid($client_id, $uid);
		// 加入客服群组
		$res2 = Gateway::joinGroup($client_id, 'kefu');
		//$data = Gateway::getClientSessionsByGroup('kefu');
		if($res && $res2){
			$this->success('连接成功');
		}else{
			$this->error('连接失败');
		}
		
        
    }
	
	//获取客服列表
    public function get_kefu_group(){
        //查询客服组
		$data = Gateway::getClientSessionsByGroup('kefu');
		if(count($data)>0){
			foreach($data as $key=>$list){
				$list['kefu_client_id'] = $key;	
			}
			$this->success("成功", "", $list);
		}else{
			$this->error('失败');
		}
        
    }
	
	//转移用户给其他客服
    public function move_group(){
        $user_id = input("param.user_id", "", "intval");
		$kefu_client_id = session("admin.kefu_client_id");
		//获取用户分组
        $group_id = get_group_id($user_id);
		$res = Gateway::joinGroup($kefu_client_id, $group_id);
		if($res){
			$this->success('转移成功');
		}else{
			$this->error('转移失败');
		}
        
    }
	
	//客服加入到用户组
    public function join_group(){
        $user_id = input("param.user_id", "", "intval");
		$kefu_client_id = session("admin.kefu_client_id");
		//获取用户分组
        $group_id = get_group_id($user_id);
		$res = Gateway::joinGroup($kefu_client_id, $group_id);
		if($res){
			$this->success('加入成功');
		}else{
			$this->error('加入失败');
		}
        
    }
	
    //关闭聊天窗口，客服离开用户分组
    public function leave_group(){
        $user_id = input("param.user_id", "", "intval");
        $kefu_client_id = session("admin.kefu_client_id");
		//获取用户分组
        $group_id = get_group_id($user_id);
		if($kefu_client_id && $group_id){
			Gateway::leaveGroup($kefu_client_id, $group_id);
			$this->success('退出成功');
		}else{
			$this->error('退出失败');
		}
        
    }
    
    /**
     * 获取列表
     * @return string
     */
    public function get_list(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $keyword = input("param.keyword", "", 'trim');
                
        $where = "1=1";
        $where .= $keyword ? " AND m.user_id = $keyword" : "";
        
        $subQuery = db('message')->alias('m')
                ->join('__MESSAGE_GROUP__ mg', 'mg.id=m.message_group_id', 'LEFT')
                ->group("mg.id")
                ->order("m.read_status ASC,m.add_time DESC")
                ->field('m.*,mg.user_id')
                ->buildSql();
        
        $list = Db::table($subQuery." m")
                ->join("__USERS__ u", "m.user_id=u.id", "LEFT")
                ->where($where)
                ->field("m.user_id,u.nickname,u.img_url,m.content,m.read_status,m.add_time,m.type,m.message_group_id")
                ->page($page,$limit)
                ->order('m.read_status ASC,m.add_time DESC')
                ->select();
        $total = Db::table($subQuery." m")
                ->where($where)
                ->count();
        if($list){
            foreach($list as $k=>$v){
                if($v['type'] == 3){
                    $list[$k]['content'] = '商品信息';//json_decode($v['content'], true);
                }
//                elseif($v['type'] == 2){
//                    $list[$k]['content'] = ['img_url' => getThumbUrl($v['content'], 1), 'thumb_img_url' => $v['content']];
//                }
            }
        }
        
        $total_page = ceil($total/$limit);
        
        $result = [
            "list" => $list,
            "pages" => [
                "total" => $total,
                "total_page" => $total_page,
                "limit" => $limit,
                "current_page" => $page,
            ]            
        ];
//        echo "<pre>";print_r($result);exit;
        $this->success("成功", "", $result);
    }
    
    /**
     * 会话详细
     * @param int $message_group_id 会话id
     */
    public function detail(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $message_group_id = input("param.message_group_id", "", "intval");
        if(!$message_group_id){
            $this->error("参数错误");
        }
        
        $group = db('message_group')->find($message_group_id);
        $user_info = db('users')->field('nickname user_name,img_url')->find($group['user_id']);
        
        $where = 'message_group_id='. $message_group_id;
        $list = db('message')
                ->where($where)
                ->field("id,content,read_status,add_time,send_user_id,type,send_user")
                ->page($page,$limit)
                ->order('id DESC')
                ->select();
        $total = db('message')
                ->where($where)
                ->count();
        if($list){
            $admin_user_info = [];
            $id_arr = [];
            foreach($list as $k=>$v){
                if($v['type'] == 3){
                    $list[$k]['content'] = json_decode($v['content'], true);
                }elseif($v['type'] == 2){
                    $list[$k]['content'] = ['img_url' => getThumbUrl($v['content'], 1), 'thumb_img_url' => $v['content']];                            
                }
                if($v['send_user'] == 1){
                    $list[$k]['user_name'] = $user_info['user_name'];
                    $list[$k]['img_url'] = $user_info['img_url'];
                }else{
                    if(!isset($admin_user_info[$v['send_user_id']])){
                        $admin_user_info[$v['send_user_id']] = db('admin_user')->field('id,nickname')->find();
                    }
                    $list[$k]['user_name'] = $admin_user_info[$v['send_user_id']]['nickname'];
                    $list[$k]['img_url'] = '/static/img/avt2.jpg';
                }
                $id_arr[] = $v['id'];
            }
            array_multisort($id_arr, SORT_ASC, $list);
        }
        
        $total_page = ceil($total/$limit);
        
        $result = [
            "list" => $list,
            "pages" => [
                "total" => $total,
                "total_page" => $total_page,
                "limit" => $limit,
                "current_page" => $page,
            ]            
        ];
//        echo "<pre>";print_r($result);exit;
        $this->success("成功", "", $result);
    }
    
    /**
     * 添加
     * @param string $content 消息内容
     * @param int $message_group_id 会话id
     * @param int $type 消息类型 1文本 2图片 3商品
     * @return string 
     */
    public function add(){
        $data = [
            'content'  => input("content","","trim"),
            'message_group_id'  => input("message_group_id","","trim"),
            'type'  => input("type","","trim"),
        ];
        $data['type'] = intval($data['type']);
        
        $validate_res = $this->validate($data,[
            'message_group_id'  => 'require|number',
            'content'  => 'require',
            'type'  => 'require|in:1,2,3',
        ],[
            'message_group_id.require' => '参数错误',
            'message_group_id.number' => '参数格式不正确',
            'content.require' => '请输入消息内容',
            'type.require' => '请传入消息类型',
            'type.in' => '消息类型格式错误',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $data['send_user_id'] = session("admin.uid");
        $data['add_time'] = date("Y-m-d H:i:s");
        $data['send_user'] = 2;
        $data['read_status'] = 1;
        $data['id'] = db('message')->insertGetId($data);
        
        if($data['id']){
            //推送消息
            $uid = db('message_group')->where('id', $data['message_group_id'])->value('user_id');
            $group = get_group_id($uid);
            
            if($data['type'] == 2){
                $data['content'] = ['img_url' => getThumbUrl($data['content'], 1), 'thumb_img_url' => $data['content']];                            
            }
            $data['user_name'] = session('admin.nickname');
            $data['img_url'] = '/static/img/avt2.jpg';
            
			$kefu_client_id = session("admin.kefu_client_id");
			Gateway::joinGroup($kefu_client_id, $group);
			//$data = Gateway::getClientSessionsByGroup($group);
			//print_r($data);
            // 向任意群组的网站页面发送数据
            Gateway::sendToGroup($group, json_encode(array(
                'type'      => 'msg',
                'content' => $data
            )));//, [$exclude_user]
            
            //写日志
            $this->add_log($this->menu_id,['title' => '添加客服消息', 'data' => $data]);
            
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
}
