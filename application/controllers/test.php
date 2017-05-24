<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:52
 */
class Test extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //die("aaa=".time());
    }

    public function index(){

        $this->load->helper('file');
        print_r(get_file_info("./data/2017/20170429_23_29_3720184e9c-4ba1-4aeb-b874-e6b0ae1aca79_160_120_thumb.jpg"));
    }
}