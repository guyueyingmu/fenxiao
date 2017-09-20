<?php
namespace app\admin\controller;

use app\admin\model\Goods;
use app\admin\model\GoodsCategory;
use think\Db;

/**
 * 商品分类
 */
class Goodscat extends Base
{
    //定义当前菜单id
    public $menu_id = 4;
    
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
        $where .= $keyword ? " AND gc.cat_name LIKE '%$keyword%'" : "";
        
        $goodscat = new GoodsCategory();
        $list = $goodscat->alias("gc")
                ->join("__ADMIN_USER__ u", "gc.admin_user_id=u.id", "LEFT")
                ->where($where)
                ->field("gc.*,u.nickname")
                ->page($page,$limit)
                ->order('gc.id DESC')
                ->select();
        $total = $goodscat->alias("gc")
                ->where($where)
                ->count();
        
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
        
        $this->success("成功", "", $result);
    }
    
    /**
     * 添加
     * @return string
     */
    public function add(){
        $data = [
            'cat_name'  => input("cat_name","","trim"),
            'cat_img'  => input("cat_img","","trim"),
            'sort'  => input("sort","","trim"),
        ];
        
        $validate_res = $this->validate($data,[
            'cat_name'  => 'require|unique:goods_category|max:50',
            'cat_img'  => 'require|max:200',
            'sort'  => 'between:0,999',
        ],[
            'cat_name.require' => '请输入商品分类',
            'cat_name.unique' => '商品分类名已存在',
            'cat_name.max' => '商品分类长度不能超过50',
            'cat_img.require' => '请上传分类图片',
            'cat_img.max' => '图片地址长度不能超过200',
            'sort.between' => '排序只能在0-999之间',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        //保存商品分类
        $data['admin_user_id'] = session("admin.uid");
        $data['add_time'] = date("Y-m-d H:i:s");
        $goodcat = new GoodsCategory($data);
        $goodcat->save();
        
        if($goodcat->id){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '添加商品分类', 'data' => $goodcat]);
            
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
        
    /**
     * 编辑
     * @param int $id 商品分类id
     * @param string $cat_name 商品分类名称
     */
    public function edit(){
        $data = [
            'id'  => input("id"),
            'cat_name'  => input("cat_name","","trim"),
            'cat_img'  => input("cat_img","","trim"),
            'sort'  => input("sort","","trim"),
        ];
        
        $validate_res = $this->validate($data,[
            'id'  => 'require|number',
            'cat_name'  => 'require|unique:goods_category|max:50',
            'cat_img'  => 'require|max:200',
            'sort'  => 'between:0,999',
        ],[
            'id.require' => '参数错误',
            'id.number' => '参数格式错误',
            'cat_name.require' => '请输入商品分类',
            'cat_name.unique' => '商品分类名已存在',
            'cat_name.max' => '商品分类长度不能超过50',
            'cat_img.require' => '请上传分类图片',
            'cat_img.max' => '图片地址长度不能超过200',
            'sort.between' => '排序只能在0-999之间',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        //保存商品分类
        $data['edit_admin_user'] = session("admin.uid");
        $data['edit_time'] = date("Y-m-d H:i:s");
        GoodsCategory::update($data);        
        
        //写日志
        $this->add_log($this->menu_id,['title' => '编辑商品分类', 'data' => $data]);
        
        $this->success("编辑成功");
    }
    
    /**
     * 删除
     * @param int $id 商品分类id
     */
    public function del(){
        $id = input("param.id", "", "intval");
        if(!$id){
            $this->error("参数错误");
        }
        
        //查看分类是否被使用，已经被使用的不能删除
        $good_id = Goods::where('cat_id',$id)->value('id');
        if($good_id){
            $this->error('该分类已经被使用，不能删除！');
        }
        
        $cat = GoodsCategory::get($id);
        if(!$cat){
            $this->error("数据不存在");
        }
        $res = $cat->delete();
        
        if($res){
            
            //写日志
            $this->add_log($this->menu_id,['title' => '删除商品分类', 'data' => $cat]);
            
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
