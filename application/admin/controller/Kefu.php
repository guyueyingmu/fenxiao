<?php
namespace app\admin\controller;

use think\Db;

/**
 * 在线客服
 */
class Kefu extends Base
{
    //定义当前菜单id
    public $menu_id = 28;
    
    public function __construct(\think\Request $request = null) {
        parent::__construct($request);
                
        $this->check_auth();
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
        
        $subQuery = db('message')->group("user_id")->order("read_status ASC,add_time DESC")->buildSql();
        
        $list = Db::table($subQuery." m")
                ->join("__USERS__ u", "m.user_id=u.id", "LEFT")
                ->where($where)
                ->field("m.user_id,u.nickname,m.content,m.read_status,m.add_time,m.type")
                ->page($page,$limit)
                ->order('m.read_status ASC,m.id ASC')
                ->select();
        $total = Db::table($subQuery." m")
                ->where($where)
                ->count();
        if($list){
            foreach($list as $k=>$v){
                if($v['type'] == 3){
                    $list[$k]['content'] = json_decode($v['content'], true);
                }elseif($v['type'] == 2){
                    $list[$k]['content'] = ['img_url' => getThumbUrl($v['content'], 1), 'thumb_img_url' => $v['content']];                            
                }
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
     * @param int $user_id 用户id
     */
    public function detail(){
        
        $page = input("param.page", 1, 'intval');
        $limit = config('admin_page_limit');
        
        $user_id = input("param.user_id", "", "intval");
        if(!$user_id){
            $this->error("参数错误");
        }
        $where = 'm.user_id='. $user_id;
        $list = db('message')->alias('m')
                ->join("__USERS__ u", "m.user_id=u.id", "LEFT")
                ->join("__ADMIN_USER__ au", "m.admin_user_id=au.id", "LEFT")
                ->where($where)
                ->field("m.user_id,u.nickname user_name,m.content,m.read_status,m.add_time,m.admin_user_id,au.nickname admin_user_name,m.type")
                ->page($page,$limit)
                ->order('m.id ASC')
                ->select();
        $total = db('message')->alias('m')
                ->where($where)
                ->count();
        if($list){
            foreach($list as $k=>$v){
                if($v['type'] == 3){
                    $list[$k]['content'] = json_decode($v['content'], true);
                }elseif($v['type'] == 2){
                    $list[$k]['content'] = ['img_url' => getThumbUrl($v['content'], 1), 'thumb_img_url' => $v['content']];                            
                }
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
     * 添加
     * @param string $content 消息内容
     * @param int $user_id 用户id
     * @param int $type 消息类型 1文本 2图片 3商品
     * @return string 
     */
    public function add(){
        $data = [
            'content'  => input("content","","trim"),
            'user_id'  => input("user_id","","trim"),
            'type'  => input("type","","trim"),
        ];
        
        $validate_res = $this->validate($data,[
            'user_id'  => 'require|number',
            'content'  => 'require',
            'type'  => 'require|in:1,2,3',
        ],[
            'user_id.require' => '参数错误',
            'user_id.number' => '参数格式不正确',
            'content.require' => '请输入消息内容',
            'type.require' => '请传入消息类型',
            'type.in' => '消息类型格式错误',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $data['admin_user_id'] = session("admin.uid");
        $data['add_time'] = date("Y-m-d H:i:s");
        $data['send_user'] = 2;
        $data['read_status'] = 1;
        $data['id'] = db('message')->insertGetId($data);
        
        if($data['id']){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '添加客服消息', 'data' => $data]);
            
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
}
