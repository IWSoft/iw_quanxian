<?php

class Upload_setup extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("main/m_aiw_upload_setup");
        $this->load->model("user/m_aiw_dd");
    }

    function index()
    {
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

    function ajax()
    {
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "create_date";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "asc";

        $key = isset($get["key"]) ? $get["key"] : "";
        $key = $searchText;
        $where = " isdel='0' ";
        if ($key != "") {
            $where .= " and title like '%" . $key . "%'";
        }

        $model = $this->m_aiw_upload_setup->get_list_pager($pageindex, $pagesize, $where, $sortName . " " . $sortOrder);
        $list = $model["list"];
        foreach ($list as $k => $v) {
            $list[$k]["ismore"] = $v["ismore"] == "0" ? "否" : "<span style='color:red;'>是</span>";
            //$list[$k]["filesize"] = floor($v["filesize"] / 1024 / 1024);
        }
        $list["rows"] = $list;
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

    function add()
    {
        $data["newid"] = mt_rand(100000, 999999);

        helper_include_css($data,
            array(
                "iCheck/custom.css",
                "switchery/switchery.css",
                "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
            ));

        helper_include_js($data,
            array(
                "switchery/switchery.js",
                "validate/jquery.validate.min.js", "validate/messages_zh.min.js"
                //'iCheck/icheck.min.js'
            ));
        //读出类型
        $data["filetype"] = $this->m_aiw_dd->get_list_pid($this->config->item("def_dd_filetype"));
        //读出处理方案
        $data["dd_fangan"] = $this->m_aiw_dd->get_list_pid("50118ac8-2ee1-a604-7ef0-72f9e080a320",true);
        $data["filesize"] = $this->config->item("def_dd_upload_filesize");
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function edit()
    {

        $data = array();

        $get = $this->input->get();
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        if ($guid == "") {
            helper_err(lang("err_no_data"));
        }
        $data["model"] = $this->m_aiw_upload_setup->get($guid);
        if (!is_array($data["model"])) {
            helper_err(lang("err_no_data"));
        }
        helper_include_css($data,
            array(
                "switchery/switchery.css",
                "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
            ));

        helper_include_js($data,
            array(
                "switchery/switchery.js",
                "validate/jquery.validate.min.js", "validate/messages_zh.min.js"
            ));
        //读出类型
        $data["filetype"] = $this->m_aiw_dd->get_list_pid($this->config->item("def_dd_filetype"));
        //读出处理方案
        $data["dd_fangan"] = $this->m_aiw_dd->get_list_pid("50118ac8-2ee1-a604-7ef0-72f9e080a320",true);
        //已选类型
        $data["model_filetype"] = explode("|",$data["model"]["filetype"]);
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function checkid()
    {
        $get = $this->input->get();
        $id = isset($get["form_id"]) ? $get["form_id"] : "";
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        //file_put_contents("./aa.txt", print_r($get,true));
        if ($this->m_aiw_upload_setup->chk_id_exist($id, $guid)) {//存在返回TRUE
            exit("false");
        } else {
            exit("true");
        }
    }


    function save()
    {
        $post = $this->input->post();
        $model = $this->admin_get_model();
        if ($model["guid"] == "") {
            if(isset($model["can_del"])){
                $model["can_del"] = $model["can_del"]=="yes"?1:0;
            }
            else{
                $model["can_del"] = '0';
            }

            $model["guid"] = create_guid();
            $model["isdel"] = '0';
            $model["create_user"] = $this->admin_guid();
            $model["create_date"] = date("Y-m-d H:i:s");
            $this->m_aiw_upload_setup->add($model);
        } else {
            $model["update_user"] = $this->admin_guid();
            $model["update_date"] = date("Y-m-d H:i:s");
            $this->m_aiw_upload_setup->update($model);
        }
        echo helper_return_json(lang("ok_msg_submit"), site_url2("edit") . "?guid=" . $model["guid"], true, 0);
    }

    function del(){
        $post = $this->input->post();
        $json["ok"] = 0;
        $json["msg"] = "";
        if(!isset($post["guid"])){
            $json["msg"] = "没有值";
            exit(json_encode($json));
        }
        $guid = $post["guid"];
        $arr = explode(",",$guid);
        foreach($arr as $v) {
            if($v!="") {
                $model = $this->m_aiw_upload_setup->get($v);
                if($model["can_del"]=="1"){
                    $model["isdel"] = '1';
                    $model["del_date"] = date("Y-m-d H:i:s");
                    $model["del_user"] = $this->admin_guid();
                    $this->m_aiw_upload_setup->update($model);
                }
                else {
                    continue;
                }
            }
        }
        $json["ok"] = "1";
        $json["msg"] = "删除成功";
        exit(json_encode($json));
    }

    /**
     * 查看
     */
    function upload_box(){
        //0f1e3fbe-94d2-b55b-e292-74a5924f0770

    }

}