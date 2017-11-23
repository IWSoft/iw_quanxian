<?php

class Bangding extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_sys_user_role");
        $this->load->model("main/m_aiw_client_licensing");
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
        $data["form_btn"] = $this->admin_get_form_btn();
        $data["form_list_btn"] = $this->admin_get_list_btn();
        //当前有无绑定
        $data["isbind"] = $this->m_aiw_client_licensing->get_count("create_user='".$this->admin_guid()."'")>0;
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }


    function save()
    {
        //判断是否在微信端
        $post = $this->input->post();
        $msg["isok"] = false;
        $msg["msg"] = "";
        $msg["url"] = site_url2("index");

        if(!helper_is_weixin()){
            //$msg["msg"] = "请在微信端操作";
            //exit(json_encode($msg));
        }

        $opt = isset($post["opt"])?$post["opt"]:"";

        if($opt=="bind"){
            $msg["msg"] = "绑定失败，请在微信端重新登录即可绑定";
            exit(json_encode($msg));
        }
        else{
            $list = $this->m_aiw_client_licensing->get_list("create_user='".$this->admin_guid()."'");
            if(count($list)>0){
                foreach($list as $v) {
                    $this->m_aiw_client_licensing->del($v["guid"]);
                }
            }
            $msg["isok"] = true;
            $msg["msg"]  = "操作成功";
            exit(json_encode($msg));
        }
    }




}

?>