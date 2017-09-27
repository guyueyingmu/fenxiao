<?php
namespace app\mini\controller;

/**
 * 收货地址
 */
class Address extends Base
{
    /**
     * 获取列表
     * @return type
     */
    public function get_list(){
        
        $list = db('consignee_info')
                ->where('user_id', session('mini.uid'))
                ->order('is_default DESC,id DESC')
                ->select();
//        exit(json_encode($list));
        $this->success('成功', '', $list);
    }
    
    /**
     * 添加
     */
    public function add(){
        $data = [
            'user_name' => input('param.user_name', '', 'trim'),
            'phone' => input('param.phone', '', 'trim'),
            'province' => input('param.province', '', 'trim'),
            'city' => input('param.city', '', 'trim'),
            'area' => input('param.area', '', 'trim'),
            'address' => input('param.address', '', 'trim'),
        ];
                
        $validate_res = $this->validate($data,[
            'user_name'  => 'require|max:50',
            'phone' => 'require|regex:1[3-9]{1}\d{9}$',
            'province' => 'require|max:20',
            'city' => 'require|max:20',
            'area' => 'require|max:20',
            'address' => 'require|max:50',
        ],[
            'user_name.require' => '请输入收货人',
            'user_name.max' => '收货人的长度不能超过50',
            'phone.require' => '请输入手机号',
            'phone.regex' => '手机号格式不正确',
            'province.require' => '请选择省份',
            'province.max' => '省份长度不能超过20',
            'city.require' => '请选择城市',
            'city.max' => '城市长度不能超过20',
            'area.require' => '请选择地区',
            'area.max' => '地区长度不能超过20',
            'address.require' => '请输入详细地址',
            'address.max' => '详细地址长度不能超过50',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $default = db('consignee_info')->where('user_id', session('mini.uid'))->where('is_default', 1)->find();
        if(!$default){
            $data['is_default'] = 1;
        }
        $data['user_id'] = session('mini.uid');
        $data['add_time'] = date('Y-m-d H:i:s');
        $res = db('consignee_info')->insert($data);
        if($res){
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }
    /**
     * 编辑
     */
    public function edit(){
        $data = [
            'id' => input('param.id', '', 'trim'),
            'user_name' => input('param.user_name', '', 'trim'),
            'phone' => input('param.phone', '', 'trim'),
            'province' => input('param.province', '', 'trim'),
            'city' => input('param.city', '', 'trim'),
            'area' => input('param.area', '', 'trim'),
            'address' => input('param.address', '', 'trim'),
        ];
                
        $validate_res = $this->validate($data,[
            'id'  => 'require|number',
            'user_name'  => 'require|max:50',
            'phone' => 'require|regex:1[3-9]{1}\d{9}$',
            'province' => 'require|max:20',
            'city' => 'require|max:20',
            'area' => 'require|max:20',
            'address' => 'require|max:50',
        ],[
            'id.require' => '参数错误',
            'id.number' => 'id格式不正确',
            'user_name.require' => '请输入收货人',
            'user_name.max' => '收货人的长度不能超过50',
            'phone.require' => '请输入手机号',
            'phone.regex' => '手机号格式不正确',
            'province.require' => '请选择省份',
            'province.max' => '省份长度不能超过20',
            'city.require' => '请选择城市',
            'city.max' => '城市长度不能超过20',
            'area.require' => '请选择地区',
            'area.max' => '地区长度不能超过20',
            'address.require' => '请输入详细地址',
            'address.max' => '详细地址长度不能超过50',
        ]); 
        if ($validate_res !== true) {
            $this->error($validate_res);
        }
        
        $exist = db('consignee_info')->where('user_id', session('mini.uid'))->where('id', $data['id'])->find();
        if(!$exist){
            $this->error('收货地址信息不存在');
        }
        $data['edit_time'] = date('Y-m-d H:i:s');
        $res = db('consignee_info')->update($data);
        if($res){
            $this->success('编辑成功');
        }else{
            $this->error('编辑失败');
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('param.id', '', 'intval');
        if(!$id){
            $this->error('参数错误');
        }
        $res = db('consignee_info')->where('id', $id)->where('user_id', session('mini.uid'))->delete();
        if($res){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }
}
