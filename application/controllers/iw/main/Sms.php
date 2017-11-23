<?php

class Sms extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("main/m_aiw_files");
        $this->load->model("user/m_aiw_sys_role");
        $this->load->model("user/m_aiw_sys_user_role");
        $this->load->model("main/m_aiw_sms");

    }

    function add()
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


    function add_ajax()
    {
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "createdate";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "asc";

        $key = isset($get["searchText"]) ? $get["searchText"] : "";
        $where = " (t1.isdel='0' ) ";
        if ($key != "") {
            $where .= " and (t3.name like '%" . $key . "%' or t1.username like '%" . $key . "%' or t1.email  like '%" . $key . "%' or t1.tel  like '%" . $key . "%') ";
        }

        $model = $this->m_aiw_sys_user->get_list_pager($pageindex, $pagesize, $where, $sortName . " " . $sortOrder);
        $check_status = array(
            "0" => "<span style='color:red'>未审</span>",
            "5" => "<span style='color:#0000cc'>待验证</span>",
            "10" => "通过",
            "99" => "<span style='color:#ce8735'>不通过</span>",
        );
        foreach ($model["list"] as $k => $v) {
            //因为编辑按钮的取值 是title
            $model["list"][$k]["title"] = $model["list"][$k]["username"];
            if (isset($check_status[$v["check_status"]])) {
                $model["list"][$k]["check_status_name"] = $check_status[$v["check_status"]];
            } else {
                $model["list"][$k]["check_status_name"] = "-";
            }
            if ($v["check_content"] != "") {
                $model["list"][$k]["check_status_name"] . "<br/>" . $v["check_content"];
            }
        }
        $list["rows"] = $model["list"];
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }


    function save()
    {

        $post = $this->input->post();
        $msg["flag"] = "0";
        $msg["msg"] = "";
        if (is_array($post)) {
            $tel = $post["tel"];
            $content = $post["content"];
            $tel = trim($tel);
            $content = trim($content);
            if($tel=="" || $content == ""){
                $msg["msg"] = "无参数";
                exit(json_encode($msg));
            }
            $tel_arr = explode("\n",$tel);
            $tel_arr = array_unique($tel_arr);
            foreach($tel_arr as $v){
                $model["guid"] = create_guid();
                $model["tel"] = $v;
                $model["content"] = $content;
                $model["create_time"] = time();
                $model["create_user"] = $this->admin_guid();
                $model["sms_type"] = $this->m_aiw_sms->get_zx_guid();//自写短信的GUID
                $model["user_guid"] = "";
                $usermodel = $this->m_aiw_sys_user->get_list("tel='".$v."' and isdel='0'");
                if(isset($usermodel[0]["tel"])){
                    $model["user_guid"] = $usermodel[0]["guid"];
                }
                $this->m_aiw_sms->add($model);
            }
            $msg["flag"] = "1";
            exit(json_encode($msg));
        } else {
            $msg["msg"] = "无数据";
            exit(json_encode($msg));
        }
    }


}

?>