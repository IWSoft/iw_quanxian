<?php
class Weixin extends MY_HomeController
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('CI_Wechat');
        $this->load->model("user/m_aiw_dd","dd");
    }

    function index(){
        $wxobj = $this->ci_wechat;
        $type =  $wxobj->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $wxobj->text("hello, I'm wechat")->reply();
                exit;
                break;
            case Wechat::MSGTYPE_EVENT:
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $wxobj->text("help info")->reply();
        }
    }

    function valid()
    {
        $this->ci_wechat->valid();
    }

    function cmenu()
    {
        $menu = '{
		     "button":[
			  {
				   "name":"登录/注册",
				   "sub_button":[
									{
									   "type":"view",
									   "name":"注册",
									   "url":"'.(site_url("/home_wx/reg")).'"
									},
									{
									   "type":"view",
									   "name":"登录",
									   "url":"'.(site_url("/home_wx/login")).'"
									}								   											 							
								]
	
			  },
			 {
				   "name":"我的推广",
				   "sub_button":[
								   {
									   "type":"view",
									   "name":"我的名片",
									   "url":"'.(site_url("home_wx/mingpian")).'"
									},									   		
								   {
									   "type":"view",
									   "name":"商城",
									   "url":"'.(site_url("home_wx/wxmall")).'"
									},
									{
									   "type":"view",
									   "name":"资讯",
									   "url":"'.(site_url("huiyuan_wx/category")).'"
									}											
								]
			  },
			  {
				   "name":"管理中心",
				   "sub_button":[
								   {
									   "type":"view",
									   "name":"修改密码",
										"url":"'.(site_url("huiyuan_wx/pwd")).'"
									},
									{
										"type":"view",
										"name":"我的介绍",
										"url":"'.(site_url("huiyuan_wx/js")).'"
									},	
									{
									   "type":"view",
									   "name":"上传头像",
									   "url":"'.(site_url("huiyuan_wx/phone_logo")).'"
									},													
									{
										"type":"view",
										"name":"我的订单",
										"url":"'.(site_url("huiyuan_wx/dingdan")).'"
									}											
								]
			  }
		]
	}';
        echo "<pre>";
        echo $this->ci_wechat->createMenu($menu)?"yes":(	$this->ci_wechat->errCode."|".$this->ci_wechat->errMsg);

        echo "<hr/>";
        print_r($menu);
        echo "</pre>";
    }
}