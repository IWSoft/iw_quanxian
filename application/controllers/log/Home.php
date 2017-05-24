<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 14:26
 * 用于记录PHP错误日志，或者自定日志
 */
class home extends MY_Controller
{

    /**
     * home constructor.
     */
    function __construct()
    {

        parent::__construct();

//        if($this->config->item("save_err_to_database")) {
//            //不使用这个，改用MYLOG里边的的出错日志
//            //set_error_handler(array(&$this, "myErrorHandler"),E_ALL);// E_ERROR
//        }
    }



    private function myErrorHandler( $errno ,  $errstr ,$errfile,$errline,$errcontext)
    {

//        if (!(error_reporting() & $errno)) {
//            // This error code is not included in error_reporting
//            return 0;
//        }
//        $this->load->helper('url');
//        $model["guid"] = create_guid();
//        $model["errno"] = $errno;
//        $model["errstr"] = $errstr;
//        $model["errfile"] = $errfile;
//        $model["errline"] =  $errline;
//        $model["errcontext"] = print_r($errcontext,true);
//        $model["beizhu"] = "PHP".PHP_VERSION.("(".PHP_OS.")");
//        $model["createdate"] = time();
//        $model["sys_user_guid"] = "";
//        $model["sys_user_username"] = "";
//        $insert_id = $this->m_com_err_log->add($model);
//        return $insert_id;
    }

    /**
     * 用于接收网址或参数传来的出错日志，并写入数据库
     * 参数：
     * $model["errno"] string,
     * $model["errstr"] string,
     * $model["errfile"] string,
     * $model["errline"] string,
     * $model["errcontext"] array 无就传空 array()
     */
    function save_log($model=""){
//        if($model!="") {
//            $this->myErrorHandler(
//                $model["errno"],
//                $model["errstr"],
//                $model["errfile"],
//                $model["errline"],
//                $model["errcontext"]
//            );
//        }
//        else{
//            $post = $this->input->post();
//            $errstr = isset($post["errstr"])?$post["errstr"]:"";
//            $errno =  isset($post["errno"])?$post["errno"]:"";
//            if($errstr!=""){
//                $this->myErrorHandler( $errno ,
//                    $errstr ,
//                    "",
//                    "",
//                    array());
//            }
//        }

    }


}