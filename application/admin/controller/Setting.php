<?php
namespace app\admin\controller;

use think\Db;
/**
 * 设置
 */
class Setting extends Base
{
    public $menu_id = '';

    /**
     * 获取设置数据
     * @param int $c_type 配置类型 0系统设置 1分销配置 2积分设置
     * @return string 
     */
    public function get_set(){
        $c_type = input("param.c_type", "");
        
        $this->menu_id = $c_type == 1 ? 19 : ($c_type == 2 ? 13 : '');
        $this->check_auth();
        
        $where = "1=1";
        $where .= $c_type !== "" ? " AND c_type=$c_type" : "";
        
        $list = db("config")->where($where)->field("id,c_name,c_value,show_name,note")->select();
        
        if($c_type == 1){
            $top_user_id = db('users')->where("pid", '0')->value('id');
            $top_arr = ['id' => '', 'c_name' => 'distribution_top_user', 'c_value' => $top_user_id, 'show_name' => '顶级分销商用户ID', 'note' => '不能更改'];
            array_unshift($list, $top_arr);
        }
//        exit(json_encode($list));
        $this->success("成功", "", $list);
    }
    
    /**
     * 积分设置/分销设置
     * @param array $data 设置数据
     * $data数据格式：
     * [
     *      [
     *          'id' => '1',
     *          'c_value' => '10',
     *      ],
     *      [
     *          'id' => '2',
     *          'c_value' => '3',
     *      ],
     * ]
     * @return string
     */
    public function set(){
        $data = input('param.data', "", "trim");
        $data = json_decode($data,true);
        if(!$data){
            $this->error("请上传要设置的数据");
        }
        $c_type = "";
        
        Db::startTrans();
        
        try{
            foreach($data as $k=>$v){
                if($v['id']){
                    if($c_type == ""){
                        $c_type = db("config")->where("id", $v['id'])->value("c_type");
                    }
                    $updata = ['id' => $v['id'], 'c_value' => $v['c_value'], 'edit_time' => time()];
                    db("config")->update($updata);
                }
            }
            
            $this->menu_id = $c_type == 1 ? 19 : ($c_type == 2 ? 13 : '');
            $this->check_auth();           
  
            if(!in_array($c_type, [1, 2])){
                exception("非法操作");
            }
            
            // 提交事务
            Db::commit();  
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
//            $this->error("操作失败");
            $this->error($e->getMessage());
        }
        
        if($c_type == 1){
            $log_title = "分销设置";
        }elseif($c_type == 2){
            $log_title = "积分设置";
        }
        //写日志
        $this->add_log($this->menu_id,['title' => $log_title, 'data' => $data]);
        
        $this->success("操作成功");
    }
}
