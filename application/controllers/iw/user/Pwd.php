<?php

class Pwd extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("main/m_aiw_files");
    }


    function index(){
        $data = array();
        $data["header_include_js"] = array(
            "static/js/plugins/validate/jquery.validate.min.js", "static/js/plugins/validate/messages_zh.min.js"
        );
        $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function save(){
        $post = $this->input->post();
        $pwd = isset($post["form_pwd"])?$post["form_pwd"]:"";
        $pwd2 = isset($post["form_pwd2"])?$post["form_pwd2"]:"";
        if(trim($pwd) == "" || trim($pwd2)==""){
            exit(helper_return_json("数据为空","",false,999));
        }
        else{
            if($pwd!=$pwd2){
                exit(helper_return_json("两次输入密码不相同","",false,999));
            }
            else{
                $model = $this->m_aiw_sys_user->get($this->admin_guid());
                $model["pwd"] = md5($pwd);
                $this->m_aiw_sys_user->update($model);
                exit(helper_return_json("密码修改成功",site_url2("index"),true,2));
            }
        }
    }
}
?>