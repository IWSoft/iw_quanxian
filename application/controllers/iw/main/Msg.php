<?php

class Msg extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();

    }

    function index()
    {

    }

    function ok()
    {
        $data["header_include_js"] = array("static/js/plugins/sweetalert/sweetalert.min.js");
        $data["header_include_css"] = array("static/css/plugins/sweetalert/sweetalert.css");
        $get = $this->input->get();
        $url = isset($get["url"]) ? $get["url"] : get_url();
        $miao = isset($get["miao"]) ? $get["miao"] : 3;
        $msg = isset($get["msg"]) ? $get["msg"] : "";
        $miao = 1000 * $miao;
        $data["url"] = $url;
        $data["miao"] = $miao;
        $data["msg"] = $msg;

        $this->load->view(__ADMIN_TEMPLATE__ . "/main/msg/ok", $data);


    }

    function err()
    {
        $data["header_include_js"] = array("static/js/plugins/sweetalert/sweetalert.min.js");
        $data["header_include_css"] = array("static/css/plugins/sweetalert/sweetalert.css");
        $get = $this->input->get();
        $url = isset($get["url"]) ? $get["url"] : get_url();
        $miao = isset($get["miao"]) ? $get["miao"] : 3;
        $msg = isset($get["msg"]) ? $get["msg"] : "";
        $miao = 1000 * $miao;
        $data["url"] = $url;
        $data["miao"] = $miao;
        $data["msg"] = $msg;

        $this->load->view(__ADMIN_TEMPLATE__ . "/main/msg/err", $data);


    }
}