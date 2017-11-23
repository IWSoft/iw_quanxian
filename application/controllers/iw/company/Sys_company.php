<?php
/**
 */
class Sys_company extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("company/m_aiw_company");
        $this->load->model("company/m_aiw_company_user_link");
        $this->load->model("dingcan/m_dingcan_company_attr");
        $this->load->model("user/m_aiw_dd");
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
        $this->load->view(__ADMIN_TEMPLATE__ . "/company/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax(){
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "create_date";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "asc";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " isdel='0' ";
        if ($key != "") {
            $where = " and ( t1.name '%" . $key . "%' ) ";
        }

        $model = $this->m_aiw_company->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        foreach($model["list"] as $k=>$v){
            //因为编辑按钮的取值 是title
            $model["list"][$k]["title"] = $model["list"][$k]["name"];
        }
        $list["rows"] = $model["list"];
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

    function check_name(){
        $get = $this->input->get();
        $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
        $name = isset($get["form_name"]) ? $get["form_name"] : "";
        if ($this->m_aiw_company->get_count("isdel='0' and name='".$name."'".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
            exit("false");
        } else {
            exit("true");
        }
    }

    function check_fullname(){
        $get = $this->input->get();
        $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
        $name = isset($get["form_fullname"]) ? $get["form_fullname"] : "";
        if ($this->m_aiw_company->get_count("isdel='0' and fullname='".$name."'".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
            exit("false");
        } else {
            exit("true");
        }
    }


    function add(){
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css",
            "datapicker/datepicker3.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js",
            "validate/jquery.validate.min.js",
            "validate/messages_zh.min.js",
            "datapicker/bootstrap-datepicker.js"
        ));
        $this->load->view(__ADMIN_TEMPLATE__ . "/company/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function edit(){
        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";
        if($guid==""){
            helper_err("无数据","",999);
        }
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css",
            "datapicker/datepicker3.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js",
            "validate/jquery.validate.min.js",
            "validate/messages_zh.min.js",
            "datapicker/bootstrap-datepicker.js"
        ));
        $data["model"] = $this->m_aiw_company->get($guid);
        $data["model_attr"] =  $this->m_dingcan_company_attr->get_by_guid($guid);
        $this->load->view(__ADMIN_TEMPLATE__ . "/company/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function save(){
        $post = $this->input->post();
        $guid = isset($post["form_guid"])?$post["form_guid"]:"";
        if($guid==""){
            $model = $this->admin_get_model();
            //判断是否重复
            if($this->m_aiw_company->get_count("name='".$model["name"]."' and isdel='0'")>0){
                exit(helper_return_json("单位简称重复",site_url2("add"),false));
            };
            if($model["fullname"]!="") {
                if ($this->m_aiw_company->get_count("fullname='" . $model["fullname"] . "' and isdel='0'") > 0) {
                    exit(helper_return_json("单位全称重复", site_url2("add"), false));
                };
            }
            $model["guid"] = create_guid();
            $model["isdel"] = '0';
            $model["create_date"] = date("Y-m-d H:i:s");
            $model["create_user"] = $this->admin_guid();
            $model["update_date"] = date("Y-m-d H:i:s");
            $model["update_user"] = $this->admin_guid();
            $this->m_aiw_company->add($model);

            exit(helper_return_json("保存成功",site_url2("add"),true));
        }
        else{

            $model = $this->m_aiw_company->get($guid);
            $post_model = $this->admin_get_model();
            foreach($post_model as $k=>$v){
                $model[$k] = $v;
            }
            $model["update_date"] = date("Y-m-d h:i:s");
            $model["update_user"] = $this->admin_guid();
            $this->m_aiw_company->update($model);
            exit(helper_return_json("保存成功",site_url2("edit")."?guid=".$guid,true));
        }
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
            $model = $this->m_aiw_company->get($v);
            $model["isdel"] = '1';
            $model["del_user"] = $this->admin_guid();
            $model["del_date"] = date("Y-m-d H:i:s");
            $this->m_aiw_company->update($model);
        }
        $json["ok"] = "1";
        $json["msg"] = "删除成功";
        exit(json_encode($json));
    }
}

?>