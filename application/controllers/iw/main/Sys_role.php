<?php
/**
 * Created by PhpStorm.
 * User: kawaycheng
 * Date: 2017-3-8
 * Time: 21:55
 */
class Sys_role extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_role");
        $this->load->model("user/m_aiw_dd");
        //读取语言文件
        //$this->lang->load(array('btn', 'iw_main_sys_module'), $this->config->item("language"));
    }

    function index(){
        $data = array();
        helper_include_css($data, array(
            "bootstrap-table/bootstrap-table.min.css",
            "bootstrap-table/bootstrap-table.my.css",
            "iCheck/custom.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js"
        ));

        $data["pagesize"] = $this->config->item("def_pagesize");
        $data["form_btn"] = $this->admin_get_form_btn();
        $data["form_list_btn"] = $this->admin_get_list_btn();
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }
    function checkid(){
        $get = $this->input->get();
        $id = isset($get["form_id"])?$get["form_id"]:"";
        if($this->m_aiw_sys_role->chk_id_exist($id)){//存在返回TRUE
            exit("false");
        }
        else{
            exit("true");
        }
    }
    function add(){
        $data["newid"] = mt_rand(100000,999999);
        $data["header_include_js"] = array(
            "static/js/plugins/validate/jquery.validate.min.js", "static/js/plugins/validate/messages_zh.min.js"
        );
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
        ));
        //读出所有模块
        $data["modules"] = $this->m_aiw_sys_module->get_all();
        //读出模块类型
        $data["modules_type"] = $this->m_aiw_sys_module->get_module_type();
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }
    function edit()
    {
        $this->load->model("user/m_aiw_sys_module");
        $data = array();
        $data["header_include_js"] = array(
            "static/js/plugins/validate/jquery.validate.min.js", "static/js/plugins/validate/messages_zh.min.js"
        );
        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";
        if($guid==""){
            helper_err(lang("err_no_data"));
        }
        $data["model"] = $this->m_aiw_sys_role->get($guid);
        if(!is_array($data["model"])){
            helper_err(lang("err_no_data"));
        }
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
        ));
        //读出所有模块
        $data["modules"] = $this->m_aiw_sys_module->get_all();
        //读出模块类型
        $data["modules_type"] = $this->m_aiw_sys_module->get_module_type();
        //角色模块
        $data["role_modules"] = explode(",",$data["model"]["modules"]);
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax()
    {
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "createdate";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "asc";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " isdel='0' ";
        if ($key != "") {
            $where = " and title like '%" . $key . "%'";
        }

        $model = $this->m_aiw_sys_role->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        $list["rows"] = $model["list"];
        //读取图标
        $icon_btn = $this->m_aiw_dd->get_btn_icon_list();
        foreach ($list["rows"] as $k => $v) {
            $list["rows"][$k]["dd_icon_style"] = "";
            $list["rows"][$k]["dd_icon_color"] = "";
        }
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }


    function save(){
        $post = $this->input->post();
        $model = $this->admin_get_model();
        $modules = isset($post["modules"])?$post["modules"]:"";
        $model["modules"] = implode(",",$modules);
        if($model["guid"]==""){
            $model["guid"] = create_guid();
            $model["isdel"] = '0';
            $model["createuser"] = $this->admin_guid();
            $model["createdate"] = time();
            $this->m_aiw_sys_role->add($model);
        }
        else{
            $model["updateuser"] = $this->admin_guid();
            $model["updatedate"] = time();
            $this->m_aiw_sys_role->update($model);
        }
        echo helper_return_json(lang("ok_msg_submit"),site_url2("edit")."?guid=".$model["guid"],true,0);

    }

    function del(){
        $post = $this->input->post();
        $json["ok"] = 0;
        $json["msg"] = "";
        //file_put_contents("./aa.txt",print_r($post,true));
        if(!isset($post["guid"])){
            $json["msg"] = "没有值";
            exit(json_encode($json));
        }
        $guid = $post["guid"];
        $arr = explode(",",$guid);
        foreach($arr as $v) {
            if($v!="" && strtolower($v)!="f0761c0f98c1") {
                $model = $this->m_aiw_sys_role->get($v);
                $model["isdel"]='1';
                $model["deldate"]=time();
                $model["deluser"]=$this->admin_guid();
                $this->m_aiw_sys_role->update($model);
            }
        }
        $json["ok"] = "1";
        $json["msg"] = "删除成功";
        exit(json_encode($json));
    }



}