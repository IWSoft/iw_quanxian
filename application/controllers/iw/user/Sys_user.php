<?php
/**
 * Created by Hello,Web.
 * QQ: 4650566
 * 希望您能尊重劳动成果，把我留下^_^
 * Date: 2017-5-15
 * Time: 22:20
 */
class Sys_user extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("main/m_aiw_files");
        $this->load->model("user/m_aiw_sys_role");
        $this->load->model("user/m_aiw_sys_user_role");

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
        $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax(){
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
            $where = " and ( username like '%" . $key . "%' or email  like '%" . $key . "%' or tel  like '%" . $key . "%') ";
        }

        $model = $this->m_aiw_sys_user->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        foreach($model["list"] as $k=>$v){
            //因为编辑按钮的取值 是title
            $model["list"][$k]["title"] = $model["list"][$k]["username"];
        }
        $list["rows"] = $model["list"];
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

    function check_username(){
            $get = $this->input->get();
            $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
            $username = isset($get["form_username"]) ? $get["form_username"] : "";
            if ($this->m_aiw_sys_user->get_count("isdel='0' and username='".$username."'".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
                exit("false");
            } else {
                exit("true");
            }
    }

    function check_email(){
        $get = $this->input->get();
        $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
        $email = isset($get["form_email"]) ? $get["form_email"] : "";
        if ($this->m_aiw_sys_user->get_count("isdel='0' and email='".$email."'".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
            exit("false");
        } else {
            exit("true");
        }
    }

    function check_tel(){
        $get = $this->input->get();
        $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
        $tel = isset($get["form_tel"]) ? $get["form_tel"] : "";
        if ($this->m_aiw_sys_user->get_count("isdel='0' and tel='".$tel."' ".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
            exit("false");
        } else {
            exit("true");
        }
    }

    function add(){
        $data["role"] = $this->m_aiw_sys_role->get_list("isdel='0'","createdate asc");
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js",
            "validate/jquery.validate.min.js",
            "validate/messages_zh.min.js"
        ));

        $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function edit(){
        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";
        if($guid==""){
            helper_err("无数据","",999);
        }
        $data["role"] = $this->m_aiw_sys_role->get_list("isdel='0'","createdate asc");
        helper_include_css($data, array(
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js",
            "validate/jquery.validate.min.js",
            "validate/messages_zh.min.js"
        ));
        $data["model"] = $this->m_aiw_sys_user->get($guid);
        $tmp = $this->m_aiw_sys_user_role->get_list("user_guid='".$guid."'");
        $role_user = array();
        foreach($tmp as $v){
            $role_user[] = $v["role_guid"];
        }
        $data["role_user"] = $role_user;
        $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function edit_mine(){
        $post = $this->input->post();
        if(count($post)>0){
            $model = $this->admin_get_model();
            if($model["pwd"]=="") {
                unset($model["pwd"]);
            }
            else{
                $model["pwd"] = md5($model["pwd"]);
            }
            $old_model = $this->m_aiw_sys_user->get($this->admin_guid());
            foreach ($old_model as $k=>$v){
                if(!isset($model[$k])){
                    $model[$k] = $old_model[$k];
                }
            }
            $model["updatedate"] = time();
            $model["updateuser"] = $this->admin_guid();
            $this->m_aiw_sys_user->update($model);
            exit(helper_return_json("保存成功",site_url2("edit_mine"),true));
        }
        else {
            $guid = $this->admin_guid();
            if ($guid == "") {
                helper_err("无数据", "", 999);
            }
            helper_include_css($data, array(
                "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css"
            ));
            helper_include_js($data, array(
                "bootstrap-table/bootstrap-table.min.js",
                "bootstrap-table/bootstrap-table-mobile.min.js",
                "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
                "iCheck/icheck.min.js",
                "validate/jquery.validate.min.js",
                "validate/messages_zh.min.js"
            ));
            $data["guid"] = $guid;
            $data["model"] = $this->m_aiw_sys_user->get($guid);
            $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
        }
    }

    function save(){
        $post = $this->input->post();
        $guid = isset($post["form_guid"])?$post["form_guid"]:"";
        if($guid==""){
            $id = create_id();
            $model = $this->admin_get_model();
            //判断用户名是否重复
            if($this->m_aiw_sys_user->get_count("username='".$model["username"]."' and isdel='0'")>0){
                exit(helper_return_json("用户名重复",site_url2("add"),false));
            };
            $model["guid"] = create_guid();
            $model["id"] = $this->m_aiw_sys_user->get_id($id);
            $model["pwd"] = md5($model["pwd"]);
            $model["islogin"] = '1';
            $model["isdel"] = '0';
            $model["createdate"] = time();
            $model["createuser"] = $this->admin_guid();
            $this->m_aiw_sys_user->add($model);
            $role_arr = is_array($post["role_id"])?$post["role_id"]:array();
            $i=0;
            foreach($role_arr as $v){
                $role_model["guid"] = create_guid();
                $role_model["user_guid"] = $model["guid"];
                $role_model["role_guid"] = $v;
                $role_model["createdate"] = time();
                $role_model["createuser"] = $this->admin_guid();
                $role_model["main_sort"] = ($i+=10);//第一个排序值为主角色
                $this->m_aiw_sys_user_role->add($role_model);
            }
            exit(helper_return_json("保存成功",site_url2("add"),true));
        }
        else{
            $id = create_id();
            $model = $this->admin_get_model();
            if($model["pwd"]=="") {
                unset($model["pwd"]);
            }
            else{
                $model["pwd"] = md5($model["pwd"]);
            }
            $old_model = $this->m_aiw_sys_user->get($guid);
            foreach ($old_model as $k=>$v){
                if(!isset($model[$k])){
                    $model[$k] = $old_model[$k];
                }
            }
            $model["updatedate"] = time();
            $model["updateuser"] = $this->admin_guid();
            $this->m_aiw_sys_user->update($model);

            $role_arr = is_array($post["role_id"])?$post["role_id"]:array();
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
                    $role_model["user_guid"] = $model["guid"];
                    $role_model["role_guid"] = $v;
                    $role_model["createdate"] = time();
                    $role_model["createuser"] = $this->admin_guid();
                    $role_model["main_sort"] = ($i+=10);//第一个排序值为主角色
                    $this->m_aiw_sys_user_role->add($role_model);
                }
            }
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
            if($v!="" && strtolower($v)!="9364547d-4572-4bd2-aaaa-6bc09680e68b") {
                $model = $this->m_aiw_sys_user->get($v);
                $model["isdel"]='1';
                $model["deldate"]=time();
                $model["deluser"]=$this->admin_guid();
                $this->m_aiw_sys_user->update($model);
            }
        }
        $json["ok"] = "1";
        $json["msg"] = "删除成功";
        exit(json_encode($json));
    }
}

?>