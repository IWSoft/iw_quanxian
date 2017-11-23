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

        $key = isset($get["searchText"]) ? $get["searchText"] : "";
        $where = " (t1.isdel='0' ) ";
        if ($key != "") {
            $where .= " and (t3.name like '%".$key."%' or t1.realname like '%" . $key . "%' or t1.username like '%" . $key . "%' or t1.email  like '%" . $key . "%' or t1.tel  like '%" . $key . "%') ";
        }

        $model = $this->m_aiw_sys_user->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        $check_status=array(
            "0"=>"<span style='color:red'>未审</span>",
            "5"=>"<span style='color:#0000cc'>待验证</span>",
            "10"=>"通过",
            "99"=>"<span style='color:#ce8735'>不通过</span>",
        );
        foreach($model["list"] as $k=>$v){
            //因为编辑按钮的取值 是title
            $model["list"][$k]["title"] = $model["list"][$k]["username"];
            if(isset($check_status[$v["check_status"]])){
                $model["list"][$k]["check_status_name"] = $check_status[$v["check_status"]];
            }
            else{
                $model["list"][$k]["check_status_name"] = "-";
            }
            if($v["check_content"]!=""){
                $model["list"][$k]["check_status_name"]."<br/>".$v["check_content"];
            }
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

    /**
     * 检查卡号
     */
    function check_card_no(){
        $get = $this->input->get();
        $guid = isset($get["form_guid"]) ? $get["form_guid"] : "";
        $card_no = isset($get["form_card_no"]) ? $get["form_card_no"] : "";
        if ($this->m_aiw_sys_user->get_count("isdel='0' and card_no='".$card_no."'".($guid==""?"":" and guid<>'".$guid."'"))>0) {//存在返回TRUE
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
        $data["company_list"] = $this->getcompany();
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
        $data["company_list"] = $this->getcompany();
        $tmp = $this->m_aiw_sys_user_role->get_list("user_guid='".$guid."'");
        $role_user = array();
        foreach($tmp as $v){
            $role_user[] = $v["role_guid"];
        }
        $data["role_user"] = $role_user;
        $data["company_guid"] = $this->getcompany_sel($guid);
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
        $this->load->model("company/m_aiw_company_user_link");
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
            $model["check_status"] = '10';//新增时就是默认通过
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
            $company_guid = isset($post["company"])?$post["company"]:"";
            if($company_guid!="") {
                $company_user_link_model["guid"] = create_guid();
                $company_user_link_model["company_guid"] = $company_guid;
                $company_user_link_model["user_guid"] = $model["guid"];
                $this->m_aiw_company_user_link->add($company_user_link_model);
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
            $company_guid = isset($post["company"])?$post["company"]:"";
            if($company_guid!="") {
                if($this->m_aiw_company_user_link->get_count("user_guid='".$guid."'")>0) {
                    $company_user_link_model = $this->m_aiw_company_user_link->get_model_by_user_guid($guid);
                    $company_user_link_model["company_guid"] = $company_guid;
                    $this->m_aiw_company_user_link->update($company_user_link_model);
                }
                else{
                    $company_user_link_model["guid"] = create_guid();
                    $company_user_link_model["company_guid"] = $company_guid;
                    $company_user_link_model["user_guid"] = $model["guid"];
                    $this->m_aiw_company_user_link->add($company_user_link_model);
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

    private function getcompany(){
        $this->load->model("company/m_aiw_company");
        return $this->m_aiw_company->get_list("isdel='0'");
    }

    /**
     * 返回选中的company_guid
     */
    private function getcompany_sel($user_guid){
        $this->load->model("company/m_aiw_company_user_link");
        $list = $this->m_aiw_company_user_link->get_by_user_guid($user_guid);
        if(count($list)>0){
            return $list[0]["company_guid"];
        }
        else{
            return "";
        }
    }

    function check_yes(){
        $post = $this->input->post();
        $json["isok"] = 1;
        $json["msg"] = "";
        //file_put_contents("./aa.txt",print_r($post,true));
        if(!isset($post["guid"])){
            $json["msg"] = "没有值";
            exit(json_encode($json));
        }
        $guid = $post["guid"];
        $check_content = isset($post["check_content"])?$post["check_content"]:"";
        $isend =  isset($post["issend"])?$post["issend"]:"";
        $arr = explode(",",$guid);
        foreach($arr as $v) {
            if($v!="" && strtolower($v)!="9364547d-4572-4bd2-aaaa-6bc09680e68b") {
                $model = $this->m_aiw_sys_user->get($v);
                $model["check_content"]=$check_content;
                $model["check_time"]=time();
                $model["check_status"]="10";
                $model["check_user"]=$this->admin_guid();
                $this->m_aiw_sys_user->update($model);
                if($isend=="yes" && strlen($model["tel"])==11){
                    helper_send_msg($model["tel"],"您好，账号审核成功，请使用手机号登录");
                }
                $json["url"] = site_url2("sys_user/edit")."?guid=".$v;
                $this->set_default_role($v);//设置默认角色
            }
        }
        $json["isok"] = 1;
        $json["msg"] = "审核成功";
        exit(json_encode($json));
    }

    function check_no(){
        $post = $this->input->post();
        $json["isok"] = 0;
        $json["msg"] = "";
        $json["url"] = "";
        //file_put_contents("./aa.txt",print_r($post,true));
        if(!isset($post["guid"])){
            $json["msg"] = "没有值";
            exit(json_encode($json));
        }
        $guid = $post["guid"];
        $check_content = isset($post["check_content"])?$post["check_content"]:"";
        $isend =  isset($post["issend"])?$post["issend"]:"";
        $arr = explode(",",$guid);
        foreach($arr as $v) {
            if($v!="" && strtolower($v)!="9364547d-4572-4bd2-aaaa-6bc09680e68b") {
                $model = $this->m_aiw_sys_user->get($v);
                $model["check_content"]=$check_content;
                $model["check_time"]=time();
                $model["check_status"]="99";
                $model["check_user"]=$this->admin_guid();
               $this->m_aiw_sys_user->update($model);
                if($isend=="yes" && strlen($model["tel"])==11){
                    helper_send_msg($model["tel"],"您好，账号审核不通过，原因：".$check_content);
                }
                $json["url"] = site_url2("sys_user/edit")."?guid=".$v;
            }
        }
        $json["isok"] = 1;
        $json["msg"] = "操作成功";
        exit(json_encode($json));
    }

    private function set_default_role($guid){
        $role_arr = array("b35d6023-e6d3-f31c-6c06-4d57c330f7cf");//默认员工角色
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
                $role_model["user_guid"] = $guid;
                $role_model["role_guid"] = $v;
                $role_model["createdate"] = time();
                $role_model["createuser"] = $this->admin_guid();
                $role_model["main_sort"] = ($i+=10);//第一个排序值为主角色
                $this->m_aiw_sys_user_role->add($role_model);
            }
        }
    }
}

?>