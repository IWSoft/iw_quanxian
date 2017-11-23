<?php

class Desktop extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_module");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("main/m_aiw_files");
    }

    function index()
    {
        $data = array();

        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }
}