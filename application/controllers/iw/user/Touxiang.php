<?php
class Touxiang extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("main/m_aiw_files");
        $this->autoimport();
    }

    function edit(){
        $data = array();
        $post = $this->input->post();
        if(count($post)>0){
            $logoguid = $post["logoguid"];
            $model = $this->m_aiw_sys_user->get($this->admin_guid());
            $model["logo"] = $logoguid;
            $model["updatedate"] = time();
            $model["updateuser"] = $this->admin_guid();
            $this->m_aiw_sys_user->update($model);
            $filemodel = $this->m_aiw_files->get($logoguid);
            $arr["err"] = "ok";
            $arr["filepath"] = $filemodel["filepath"];//返回LOGO文件路径
            exit(json_encode($arr));
        }
        else {
            $data["model"] = $this->admin_get_model();
            $guid = "d343a277-0b4b-a6de-13fd-5267e72fe70c";//$this->top_curr_module_guid();//当前上传模块的GUID
            $usermodel = $this->m_aiw_sys_user->get($this->admin_guid());
            $data["curr_logo_guid"] = $usermodel["logo"];
            $data["list"] = $this->m_aiw_files->get_list("sys_module_guid='" . $guid . "'");
            $this->load->view(__ADMIN_TEMPLATE__ . "/user/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
        }
    }

    /**
     * 自动将static\img\user_logo的头像保存到数据字典
     */
    function autoimport(){
        $dir="static/img/user_logo";
        $files=scandir($dir);
        $this->load->helper('file');
        $i=0;
        $guid = "d343a277-0b4b-a6de-13fd-5267e72fe70c";
        foreach($files as $v){
            if($v=="." || $v==".."){
                continue;
            }
            if($this->m_aiw_files->get_count("sys_module_guid='".$guid."' and filepath='".$dir."/".$v."'")>0){
                continue;
            }
            $filemodel = get_file_info($dir."/".$v);
            $img_info = getimagesize($dir."/".$v);
            $model["guid"] = create_guid();
            $model["filesize"] = $filemodel["size"];
            $model["mime"] = $img_info["mime"];
            $model["filepath"] = $dir."/".$v;
            $model["title"] = $filemodel["name"];
            $model["filename"] = $filemodel["name"];
            $model["can_del"] = '0';
            $model["create_user"] = $this->admin_guid();
            $model["create_date"] = date("Y-m-d H:i:s");
            $model["sys_module_guid"] = $guid;//$this->top_curr_module_guid();
            $model["upload_setup_guid"] = "";
            $model["image_width"] = $img_info[0];
            $model["image_height"] = $img_info[1];
            $model["parent_guid"] = "";
            $i++;
            $this->m_aiw_files->add($model);
            //region  格式
            /*
             * Array
(
    [name] => 1.1.jpg
    [server_path] => static/img/user_logo/1.1.jpg
    [size] => 9616
    [date] => 1491911772
    [pathinfo] => Array
        (
            [dirname] => static/img/user_logo
            [basename] => 1.1.jpg
            [extension] => jpg
            [filename] => 1.1
        )

)
             */
            //endregion
        }
        //检查是否存在图片，无存在则删除
        $list = $this->m_aiw_files->get_list("sys_module_guid='".$guid."' ");
        foreach($list as $v){
            if(!is_file($v["filepath"])){
                $this->m_aiw_files->del("guid='".$v["guid"]."'");
            }
        }
    }
}