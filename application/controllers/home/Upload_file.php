<?php

/**
 * 上传附件
 * Class Upload_file
 */
class Upload_file extends MY_HomeController
{
    function __construct()
    {
        parent::__construct();

        $this->load->model("main/m_aiw_files");

    }

    /**
     * 输出文件
     */
    function get()
    {
        $get = $this->input->get();
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        if ($guid != "") {
            $model = $this->m_aiw_files->get($guid);
            @header("Content-Type:" . $model["mime"]);
            $imagespath = $model["filepath"];
            echo file_get_contents($imagespath);
        }
        exit();
    }

}