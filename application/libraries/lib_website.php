<?php
/**
 * 针对网站管理控制器，定义通用的方法
 */
if (! defined('BASEPATH')) {
	exit('Access Denied');
}

class lib_website extends CI_Model{
	
	function __construct(){	
		parent::__construct();
		$this->load->model('m_aiw_dd');
	}

    /**
     * 返回上传路径
     * @param bool $isfull 是否带全路径
     * @return string
     */
	function get_upload_path($isfull = false){
        $model_fujian_root = $this->m_aiw_dd->get_model_by_id("554468");
        $model_fujian_info = $this->m_aiw_dd->get_model_by_id("895318");
        $root = isset($model_fujian_root["val"])?$model_fujian_root["val"]:"data";
        $info = isset($model_fujian_info["val"])?$model_fujian_info["val"]:"info";
        $path = ($isfull?(__ROOT__."/"):"").$root."/".$info."/";
        return $path;
    }
}