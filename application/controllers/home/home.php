<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:48
 */

class home extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

    }

    function index(){
        header("location:".site_url2("home/login/index"));
    }


    function guid2(){
        echo "<input type='text' style='width:300px;' value='".create_guid()."'/>";
    }

}