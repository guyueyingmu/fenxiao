<?php
namespace app\admin\controller;

/**
 * 首页轮播图
 */
class Banner extends Base
{
    //定义当前菜单id
    public $menu_id = 27;
    
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
                
        $list = db('banner')
                ->page($page,$limit)
                ->order('id DESC')
                ->select();
        $total = db('banner')->count();
        
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
//        exit(json_encode($result));
        $this->success("成功", "", $result);
    }
    
    /**
     * 添加
     * @return string
     */
    public function add(){
        $data = [
            'status'  => input("status","","trim"),
            'img_url'  => input("img_url","","trim"),
            'sort'  => input("sort","","trim"),
        ];
        
        $validate_res = $this->validate($data,[
            'img_url'  => 'require',
            'status'  => 'require|in:1,2',
            'sort'  => 'between:0,999',
        ],[
            'img_url.require' => '请上传轮播图',
            'status.require' => '请选择启用状态',
            'status.in' => '启用状态数据不正确',
            'sort.between' => '排序只能在0-999之间',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        //保存商品分类
        $data['admin_user_id'] = session("admin.uid");
        $data['add_time'] = date("Y-m-d H:i:s");
        $banner = db('banner');
        $id = $data['id'] = $banner->insertGetId($data);
        
        if($id){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '添加首页轮播图', 'data' => $data]);
            
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
        
    /**
     * 编辑
     * @param int $id id
     * @param string $status 是否启用 1启用 2不启用
     */
    public function edit(){
        $data = [
            'id'  => input("id"),
            'status'  => input("status","","trim"),
        ];
        
        $validate_res = $this->validate($data,[
            'id'  => 'require|number',
            'status'  => 'require|in:1,2',
        ],[
            'id.require' => '参数错误',
            'id.number' => '参数格式错误',
            'status.require' => '请选择商品启用状态',
            'status.in' => '启用状态数据不正确',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $info = db('banner')->find($data['id']);
        if(!$info){
            $this->error("轮播图不存在");
        }
        
        //保存商品分类
        $res = db('banner')->update($data);        
        
        //写日志
        $this->add_log($this->menu_id,['title' => '编辑首页轮播图', 'data' => $data]);
        
        $this->success("编辑成功");
    }
    
    /**
     * 删除
     * @param int $id id
     */
    public function del(){
        $id = input("param.id", "", "intval");
        if(!$id){
            $this->error("参数错误");
        }
        
        $info = db('banner')->find($id);
        if(!$info){
            $this->error("数据不存在");
        }
        $res = db('banner')->delete($id);
        
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '删除首页轮播图', 'data' => $info]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
