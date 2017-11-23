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

    /**
     * 自动调用系统消息
     */
    function get_system_msg(){
        $this->load->model("main/m_aiw_sys_message");
        $list = $this->m_aiw_sys_message->get_msg($this->admin_guid(),true);
        //读出模块的打开方式
        exit(json_encode($list));
    }

    function view_system_msg(){
        $this->load->model("main/m_aiw_sys_message");
        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";
        if($guid==""){
            exit();
        }
        $model = $this->m_aiw_sys_message->get($guid);
        $model["isread"] = '1';
        $this->m_aiw_sys_message->update($model);
        $data["model"] = $model;
        $this->curr_path = $model["title"];
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function list_system_msg(){
        $this->load->model("main/m_aiw_sys_message");
        $data = array();
        helper_include_css($data, array(
            "bootstrap-table/bootstrap-table.min.css",
            "bootstrap-table/bootstrap-table.my.css",
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css",
            "iCheck/custom.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js"
        ));
        $get = $this->input->get();
        $parent_guid = isset($get["guid"])?$get["guid"]:"";
        if($parent_guid!="") {
            $data["model"] = $this->m_aiw_sys_module->get($parent_guid);
        }
        else{
            $data["model"] = "";
        }
        $data["pagesize"] = $this->config->item("def_pagesize");
        $data["form_btn"] = $this->admin_get_form_btn();
        $data["form_list_btn"] = $this->admin_get_list_btn();
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }


    function list_system_msg_ajax()
    {
        $this->load->model("main/m_aiw_sys_message");
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";
        $sortName = isset($get["sortName"]) ? $get["sortName"] : "createdate";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "desc";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " receive_user='".$this->admin_guid()."'";
        if ($key != "") {
            $where = " and title like '%" . $key . "%'";
        }
        if ($searchText != "") {
            $where = " and title like '%" . $searchText . "%'";
        }

        $model = $this->m_aiw_sys_message->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        foreach ($model["list"] as $k=>$v){
            //标识赋值
            $this->m_aiw_sys_message->msg_level_to_array($v["msg_level"],$model["list"][$k]);
            $model["list"][$k]["createdate"] = date("Y-m-d H:i",$model["list"][$k]["createdate"]);
        }
        $list["rows"] = $model["list"];
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }
}