<?php

class Home extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_module");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("main/m_aiw_files");
    }

    function index(){
        $data = $this->admin_data;

        $data["session"] = $this->admin_get_session();
        $module_list = $this->m_aiw_sys_module->get_module_list($data["session"]["model"]["guid"],"10");
        $data["module_list"] = $module_list;
        //print_r($data["session"]);
        //系统名称
        $model = $this->m_aiw_dd->get("b72b7935-c089-88c7-c9b6-07a5b97652d7");
        if(isset($model)){
            $system_name = $model["val"];
        }
        else{
            $system_name = "管理后台";
        }
        $data["system_name"] = $system_name;
        //页脚名称
        $model_footer = $this->m_aiw_dd->get("cbb37883-411a-885d-59b1-bf920e56f729");
        if(isset($model_footer)){
            $pager_footer = $model_footer["val"];
        }
        else{
            $pager_footer = "管理后台";
        }
        //取头像
        $userlogo = $this->m_aiw_sys_user->get($this->admin_guid());
        if(isset($userlogo["logo"])){
            if($userlogo["logo"]!="") {
                $userlogo = $this->m_aiw_files->get($userlogo["logo"]);
                $userlogo = $userlogo["filepath"];
            }
            else{
                $userlogo = "static/img/profile_small.jpg";
            }
        }
        else{
            $userlogo = "static/img/profile_small.jpg";
        }
        $data["userlogo"] = $userlogo;
        $data["pager_footer"] = $pager_footer;
        $this->load->view(__ADMIN_TEMPLATE__."/main/home",$data);
    }

    function page_404(){
        $data["heading"] = "找不到页面";
        $data["message"] = "检查类名是否正确";
        $this->load->view(__ERROR_TEMPLATE__."/html/error_404",$data);
    }

    function test(){
        echo "yes";
    }
}