<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:48
 */

class home extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
    }

    function index(){
        echo create_guid();
        //echo "aaa=".($this->config->item("save_err_to_database")?"yes":"no");
        //echo $this->config->item("time_reference");

        echo "aaaa";
    }
}