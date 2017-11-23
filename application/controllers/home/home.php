<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:48
 */
class Home extends MY_HomeController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user_role");
    }

    function index()
    {

        ("location:" . site_url2("home/login/index"));
    }



    //生成验证码
    function code()
    {
        $this->load->library("code", array(
            'width' => 80,
            'height' => 35,
            'fontSize' => 20,
            'font' => "system/fonts/font.ttf"
        ));
        $this->code->show();
        //echo $this->code->getCode();
    }







    private function set_default_role($guid){
        $role_arr = array("b35d6023-e6d3-f31c-6c06-4d57c330f7cf");//默认员工角色
        $where = "";
        //删除无选中的角色
        foreach($role_arr as $v){
            if($where==""){
                $where = "role_guid<>'".$v."'";
            }
            else{
                $where.= " and role_guid<>'".$v."'";
            }
        }
        if($where!=""){
            $where = " user_guid='".$guid."' and (".$where.")";
            $this->m_aiw_sys_user_role->del($where);
        }
        //取前最大排排序值
        $more_role_model = $this->m_aiw_sys_user_role->get_list("user_guid='".$guid."'","main_sort desc",1);
        if(count($more_role_model)>0){
            $i = $more_role_model[0]["main_sort"];
        }
        else{
            $i=0;
        }
        foreach($role_arr as $v){
            if($this->m_aiw_sys_user_role->get_count("user_guid='".$guid."' and role_guid='".$v."'")==0){
                $role_model["guid"] = create_guid();
                $role_model["user_guid"] = $guid;
                $role_model["role_guid"] = $v;
                $role_model["createdate"] = time();
                $role_model["createuser"] = "";//$this->admin_guid();
                $role_model["main_sort"] = ($i+=10);//第一个排序值为主角色
                $this->m_aiw_sys_user_role->add($role_model);
            }
        }
    }

}