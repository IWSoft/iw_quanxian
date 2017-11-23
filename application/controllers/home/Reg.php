<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:48
 */

class Reg extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user/m_aiw_sys_user');
        $this->load->model('user/m_aiw_sys_user_role');
        $this->load->model('user/m_aiw_dd');
        $this->load->model('main/m_aiw_files');
        $this->load->model('company/m_aiw_company');
        $this->load->model('company/m_aiw_company_user_link');

    }

    function index(){
        $data = array();
        $this->setup($data);
        $data["url"] = site_url2("home/login");
        $data["company"] = $this->m_aiw_company->get_list("isdel='0'");
        if(helper_is_mobile()){
            $this->load->view(__HOME_TEMPLATE__ . "/" . strtolower(__CLASS__ . "/" . __FUNCTION__."_youbao"), $data);
        }
        else {
            $this->load->view(__HOME_TEMPLATE__ . "/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
        }
    }

    private function setup(&$data){
        //系统名称
        $model = $this->m_aiw_dd->get("b72b7935-c089-88c7-c9b6-07a5b97652d7");
        if(isset($model)){
            $system_name = $model["val"];
        }
        else{
            $system_name = "管理后台";
        }
        //读出背景图
        $bk_img_model = $this->m_aiw_dd->get("5d49cac6-d9a5-2aea-6b39-c9040d2a2b4b");
        if(isset($bk_img_model["val"])){
            if($bk_img_model["val"]!="") {
                $filemodel = $this->m_aiw_files->get($bk_img_model["val"]);
            }
        }
        $data["login_bk_img"] = (isset($filemodel["filepath"])?$filemodel["filepath"]:"");
        $data["system_name"] = $system_name;
    }

    function chktel(){
        $get = $this->input->get();
        $tel = isset($get["tel"])?$get["tel"]:"";
        exit($this->chkusertel($tel));
    }

    function chkcode($val_yzm="",$istext=true){
        $get = $this->input->get();
        //$this->load->library("code");

        //$yzm = $this->code->getCode();
        $yzm2 = isset($get["yzm"])?$get["yzm"]:"";
        if(!$istext){
            $yzm2 = strtolower($val_yzm);
        }
        @session_start();
        $yzm = strtolower($_SESSION['code']);
        if($yzm==$yzm2){
            if($istext){
                exit("ok");
            }
            else{
                return true;
            }

        }
        else{
            if($istext) {
                exit("输入验证码不正确");
            }
            else{
                return false;
            }
        }
    }

    function chk_form(){
        $get = $this->input->get();
        $model["tel"] = isset($get["tel"])?trim($get["tel"]):"";
        $model["realname"] = isset($get["realname"])?trim($get["realname"]):"";
        $model["company_guid"] = isset($get["cmopany_guid"])?trim($get["company_guid"]):"";
        $msg = $this->chk($model);
        exit($msg);
    }

    private function chkusertel($tel,$isjson=true){
        $msg["flag"] = "0";
        $msg["msg"] = "";
        if(trim($tel=="")){
            $msg["msg"] = "没有资料";
        }
        else {
            $list = $this->m_aiw_sys_user->get_list("(username='".$tel."' or tel='" . $tel . "') and check_status in(0,5,10) and isdel='0'");
            if (count($list) > 0) {
                $msg["flag"] = '1';
            } else {
                $msg["msg"] = "该手机号已正在使用中，请跟管理员查明原因。";
            }
        }
        if($isjson){
            exit(json_encode($msg));
        }
        else{
            return $msg;
        }
    }

    private function chk($model,$isjson=true){
        $msg["flag"] = '0';// 成功：10:已存在用户，但无手机号  20：不存在用户 30：待验证时，完全匹配，直接可以用
        $msg["msg"] = "";


        $realname = trim($model["realname"]);
        $company_guid = trim($model["company_guid"]);
        $tel = trim($model["tel"]);
        if($realname=="" || $company_guid=="" || $tel==""){
            $msg["msg"] = "没有参数";
            if($isjson){
                exit(json_encode($msg));
            }
            else{
                return $msg;
            }
        }
        //判断手机号是否重复
        $list = $this->m_aiw_sys_user->get_list("(username='".$tel."' or tel='" . $tel . "') and check_status in(0,5,10) and isdel='0'");
        if(count($list)>0){
            $msg["msg"] = "您的手机号已存在，无法完成注册。";
            if($isjson){
                exit(json_encode($msg));
            }
            else{
                return $msg;
            }
        }

        //优先判断用户手机号是否状态为5:待验证，如是：则要准确判断所有资料
        $list = $this->m_aiw_sys_user->get_list("check_status='5' and realname='".$realname."'  and isdel='0'");//and tel='".$tel."'
        if(count($list)>0){
            $link_model = $this->m_aiw_company_user_link->get_model_by_user_guid($list[0]["guid"]);

            if(count($link_model)>0){
                if($link_model["company_guid"]==$company_guid){
                    $msg["flag"] = "30";
                    $msg["msg"] = "您的资料完全正确，可以直接登录使用。";
                    if($isjson){
                        exit(json_encode($msg));
                    }
                    else{
                        return $msg;
                    }
                }
                else{
                    $msg["msg"] = "您的资料初始化时已入库，但所选单位跟库不同，请重新选择单位，如有疑问请找管理员处理。";
                    if($isjson){
                        exit(json_encode($msg));
                    }
                    else{
                        return $msg;
                    }
                }
            }
        }

        $list = $this->m_aiw_sys_user->get_list("realname='".$realname."' and isdel='0'");
        foreach($list as $v) {
            $link_model = $this->m_aiw_company_user_link->get_model_by_user_guid($v["guid"]);
            if(isset($link_model["company_guid"]) && $link_model["company_guid"]==$company_guid){
                $usermodel = $v;
                $company_model = $this->m_aiw_company->get($company_guid);
                break;
            }
        }



        if(isset($usermodel["guid"]) && isset($company_model["guid"]) && $usermodel["tel"]==""){
            $msg["msg"] = "注册成功，请使用手机号登录";
            $msg["flag"] = "10";
        }
        elseif(isset($usermodel["guid"]) && isset($company_model["guid"]) && $usermodel["tel"]!="" && $usermodel["check_status"]=='0'){
            $msg["flag"] = "0";
            $msg["msg"] = "提交失败，会员正在待审，无法再提交。";
        }
        elseif(isset($usermodel["guid"]) && isset($company_model["guid"]) && $usermodel["tel"]!="" && $usermodel["check_status"]=='10'){
            $msg["flag"] = "0";
            $msg["msg"] = "提交失败，会员已存在。";
        }
        else{
            $msg["flag"] = "20";
        }

        if($isjson){
            exit(json_encode($msg));
        }
        else{
            return $msg;
        }
    }

    function save(){

        $post = $this->input->post();
        $chkmodel["realname"] = isset($post["realname"])?$post["realname"]:"";
        $chkmodel["tel"] = isset($post["tel"])?$post["tel"]:"";
        $chkmodel["company_guid"] = isset($post["company"])?$post["company"]:"";
        $msg = $this->chk($chkmodel,false);
        if($msg["flag"]=='0'){
            exit(json_encode($msg));
        }
        $realname = isset($post["realname"])?$post["realname"]:"";
        $tel = isset($post["tel"])?$post["tel"]:"";
        $pwd = isset($post["pwd"])?$post["pwd"]:"";
        $company = isset($post["company"])?$post["company"]:"";

        if(!$this->chkcode((isset($post["yzm"])?$post["yzm"]:""),false)){
            $msg["flag"] = '0';
            $msg["msg"] = '验证码不正确';
            exit(json_encode($msg));
        }

        // 成功：
        //10:已存在用户，但无手机号
        //  20：不存在用户
        // 30：待验证时，完全匹配，直接可以用
        //$msg = json_decode($msg,true);
        $flag = $msg["flag"];
        if($flag=='10' || $flag=='30'){
            $list = $this->m_aiw_sys_user->get_list(" realname='$realname' and check_status='5'");//username='$tel'
            if(count($list)==1){
                $model = $list[0];
                //$model["id"] = $this->m_aiw_sys_user->get_id(rand(10000,99999));
                $model["username"] = $tel;
                $model["tel"] = $tel;
                $model["pwd"] = md5($pwd);
                $model["check_status"] = '10';
                $model["check_time"] = time();
                $model["check_content"] = "自动审核成功（".$msg["msg"].")";
                $model["check_user"] = "";
                $this->m_aiw_sys_user->update($model);
                //更新公司关系表
                $link_company = $this->m_aiw_company_user_link->get_list("user_guid='".$model["guid"]."' and company_guid='".$company."'");
                if(count($link_company)>0){
                    //不用处理
                }
                else{
                    $company_model["user_guid"] = $model["guid"];
                    $company_model["company_guid"] = $company;
                    $company_model["guid"] = create_guid();
                    $this->m_aiw_company_user_link->add($company_model);
                }
                $msg["flag"] = '1';
                $msg["msg"] = '注册成功，登录系统。';
                exit(json_encode($msg));
            }
            else{
                $msg["flag"] = '0';
                $msg["msg"] = '存在多条用户记录，注册异常';
                exit(json_encode($msg));
            }
        }
        //不存在于初始化资料中，需要审核才能用
        if($flag=='20'){
                $model["id"] = $this->m_aiw_sys_user->get_id(rand(10000,99999));
                $model["guid"] = create_guid();
                $model["username"] = $tel;
                $model["realname"] = $realname;
                $model["tel"] = $tel;
                $model["pwd"] = md5($pwd);
                $model["check_status"] = '0';
                $model["check_time"] = "0";
                $model["check_content"] = "";
                $model["check_user"] = "";
                $this->m_aiw_sys_user->add($model);
                //更新公司关系表
                $link_company = $this->m_aiw_company_user_link->get_list("user_guid='".$model["guid"]."' and company_guid='".$company."'");
                if(count($link_company)>0){
                    //不用处理
                }
                else{
                    $company_model["user_guid"] = $model["guid"];
                    $company_model["company_guid"] = $company;
                    $company_model["guid"] = create_guid();
                    $this->m_aiw_company_user_link->add($company_model);
                }
            $msg["flag"] = '1';
            $msg["msg"] = '注册成功，待审核通过后才能使用。';
            helper_send_msg(helper_get_superadmin_tel(),"新会员".$model["realname"].",手机".$model["tel"]."，请及时到后台审核");
            exit(json_encode($msg));
        }

        $msg["flag"] = '0';
        $msg["msg"] = '注册异常，请提交姓名、手机号、单位资料给管理员处理。';
        exit(json_encode($msg));
    }

}