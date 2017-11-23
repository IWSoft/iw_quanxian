<?php

/**
 * 管理员菜单及模块
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 10:39
 */
class Sys_module extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_module");
        $this->load->model("user/m_aiw_dd");
        //读取语言文件
        $this->lang->load(array('btn','iw_main_sys_module'), $this->config->item("language"));
    }

    function index()
    {
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
        $data["parent_guid"] =  $parent_guid;
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax()
    {
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";
        $parent_guid = isset($get["guid"]) ? $get["guid"] : "";
        $module_type = isset($get["mt"]) ? $get["mt"] : "";
        $sortName = isset($get["sortName"]) ? $get["sortName"] : "";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " isdel='0' ";
        if ($key != "") {
            $where = " and title like '%" . $key . "%'";
        }
        if ($searchText != "") {
            $where = " and title like '%" . $searchText . "%'";
        }
        if ($module_type != "") {
            $where .= " and module_type='" . $module_type . "'";
        } else {
            //$where .= " and module_type='-1'";
        }
        if($parent_guid==""){
            $where .= " and (isnull(parent_guid) or parent_guid='')";
        }
        else {
            $where .= " and (parent_guid='" . $parent_guid . "')";
        }

        $model = $this->m_aiw_sys_module->get_list_pager($pageindex, $pagesize, $where, "sort asc");
        $list["rows"] = $model["list"];
        //读取图标
        $icon_btn = $this->m_aiw_dd->get_btn_icon_list();
        foreach ($list["rows"] as $k => $v) {
            $list["rows"][$k]["dd_icon_style"] = "";
            $list["rows"][$k]["dd_icon_color"] = "";
            foreach ($icon_btn as $k2 => $v2) {
                if ($v["method"] == $v2["val"]) {
                    $list["rows"][$k]["dd_icon_style"] = $v2["val2"];
                    $list["rows"][$k]["dd_icon_color"] = $v2["val3"];
                    break;
                }
            }
            if($v["dd_icon"]!=""){
                $list["rows"][$k]["dd_icon_style"] = $v["dd_icon"];
                $list["rows"][$k]["dd_icon_color"] = "";
            }
            //处理链接,将控制器方法参数，合并到URL
            if($list["rows"][$k]["controller"]!=""){
                $list["rows"][$k]["url"] = $list["rows"][$k]["controller"]."/".$list["rows"][$k]["method"].($list["rows"][$k]["param"]!=""?("?".$list["rows"][$k]["param"]):"");
            }
            if($list["rows"][$k]["url"]==""){
                $list["rows"][$k]["url"] = "-";
            }

            //父名称
            if($list["rows"][$k]["parent_guid"]=="" || strtolower($list["rows"][$k]["parent_guid"])=="n/a"){
                $list["rows"][$k]["parent_title"] = "顶级";
            }
            else{
                $parent_model = $this->m_aiw_sys_module->get($list["rows"][$k]["parent_guid"]);
                if(isset($parent_model["title"])){
                    $list["rows"][$k]["parent_title"] = $parent_model["title"];
                }
                else{
                    $list["rows"][$k]["parent_title"] = "-";
                }
            }
        }
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }



    /**
     *新增顶级菜单
     */
    function add(){
        $get = $this->input->get();
        $data = array();
        $data["header_include_js"] = array(
            "static/js/plugins/validate/jquery.validate.min.js"
            ,
            "static/js/plugins/validate/messages_zh.min.js"
        );


        $parent_guid = isset($get["parent_guid"])?$get["parent_guid"]:"";
        if($parent_guid!=""){
            $data["parent_model"] = $this->m_aiw_sys_module->get($parent_guid);
        }
        helper_include_css($data,
            array(
                "switchery/switchery.css",
               "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
            ));
        helper_include_js($data,
            array(
                "switchery/switchery.js",
                "jsKnob/jquery.knob.js"
            ));
        $data["parent_guid"] = $parent_guid;
        //讲出所有图标
        $data["icon_list"] = $this->m_aiw_dd->get_list_pid($this->config->item("def_dd_icon_guid"),true);
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    /**
     * 修改顶级菜单
     */
    function edit(){

        $get = $this->input->get();
        $data = array();
        $data["header_include_js"] = array(
            "static/js/plugins/validate/jquery.validate.min.js", "static/js/plugins/validate/messages_zh.min.js"
        );
        $parent_guid = isset($get["parent_guid"])?$get["parent_guid"]:"";
        $guid = isset($get["guid"])?$get["guid"]:"";
        if($parent_guid!=""){
            $data["parent_model"] = $this->m_aiw_sys_module->get($parent_guid);
        }
        helper_include_css($data,
            array(
                "switchery/switchery.css",
                "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
            ));
        helper_include_js($data,
            array(
                "switchery/switchery.js",
                "jsKnob/jquery.knob.js"
            ));
        //讲出所有图标
        $data["icon_list"] = $this->m_aiw_dd->get_list_pid($this->config->item("def_dd_icon_guid"),true);
        //读出实体
        if($guid!=""){
            $data["model"] =  $this->m_aiw_sys_module->get($guid);
        }
        else{
            $data["model"] = "";
        }
        if(!is_array($data["model"])){
            helper_err(lang("err_no_data"),"");
        }
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }



    /**
     * 保存顶级菜单
     */
    function save(){
        $model = $this->admin_get_model();
        $parent_path  = "";
        $curr_level = 0;
        $guid = isset($model["guid"])?$model["guid"]:"";
        //处理父路径
        if($model["parent_guid"]!=""){
            $parent_model = $this->m_aiw_sys_module->get($model["parent_guid"]);
            if(count($parent_model)>0) {
                $parent_path = ($parent_model["parent_path"]==""?"":($parent_model["parent_path"].",")).$model["parent_guid"];
                $parent_path_arr = explode(",",$parent_path);
                $curr_level = count($parent_path_arr);
            }
        }
        if($guid=="") {
            $model["id"] = $this->m_aiw_sys_module->get_id(create_id());
            $model["guid"] = create_guid();
            $model["createdate"] = time();
            $model["createuser"] = $this->admin_guid();
            $model["parent_path"] = $parent_path;
            $model["curr_level"] = $curr_level;
            $model["updateuser"] = "";
            if(isset($model["collapsed"])) {
                $model["collapsed"] = $model["collapsed"] == "yes" ? "0" : "1";
            }
            else{
                $model["collapsed"] = '1';
            }
            $guid = $this->m_aiw_sys_module->add($model);
        }
        else{
            //修改
            $model["parent_path"] = $parent_path;
            $model["updatedate"] = time();
            $model["updateuser"] = $this->admin_guid();
            $model["curr_level"] = $curr_level;
            if(isset($model["collapsed"])) {
                $model["collapsed"] = $model["collapsed"] == "yes" ? "0" : "1";
            }
            else{
                $model["collapsed"] = '1';
            }
            $this->m_aiw_sys_module->update($model);
        }
        helper_get_json_header();
        if($guid!=""){
            echo helper_return_json(lang("ok_msg_save"),"",true);

        }
        else{
            echo helper_return_json(lang("err_msg"));
        }
    }

    /**
     * 读出下级菜单
     */
    function get_sub_menu(){
        $get = $this->input->get();

        $parent_guid = isset($get["parent_guid"])?$get["parent_guid"]:"";


        if($parent_guid==""){
            helper_err(lang("iw_main_sys_module_err_menu"));
        }

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
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }


}