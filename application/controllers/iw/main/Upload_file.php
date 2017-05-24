<?php

/**
 * 上传附件
 * Class Upload_file
 */
class Upload_file extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("main/m_aiw_upload_setup");
        $this->load->model("user/m_aiw_dd");
        $this->load->model("log/m_aiw_opt_log");
        $this->load->model("main/m_aiw_files");
        $this->load->model("main/m_aiw_sys_module");
    }

    function add(){
        $data = array();
        helper_include_css($data,
            array(
                "webuploader/webuploader.css",
                "webuploader/mystyle.css",
                "bootstrap-table/bootstrap-table.min.css",
                "bootstrap-table/bootstrap-table.my.css",
                "iCheck/custom.css"

            ));
        helper_include_js($data,
            array(
                "webuploader/webuploader.min.js",
                "webuploader/my.js",
                "bootstrap-table/bootstrap-table.min.js",
                "bootstrap-table/bootstrap-table-mobile.min.js",
                "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
                "iCheck/icheck.min.js"
            )
        );



        $data["pagesize"] = $this->config->item("def_pagesize");

        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";
        $boxid =  isset($get["boxid"])?$get["boxid"]:"";//父页面控件ID
        $data["boxid"] = $boxid;
        $data["model"] = $this->m_aiw_upload_setup->get($guid);
        $filetype_arr = explode("|",$data["model"]["filetype"]);
        $where = "";
        foreach($filetype_arr as $v){
            if($v!=""){
                if($where==""){
                    $where = " val='".$v."'";
                }
                else{
                    $where .= "or val='".$v."'";
                }
            }
        }

        if($where!=""){
            $where = "isdel='0' and (".$where.")";
            $filetype_list = $this->m_aiw_dd->get_list($where);
            $data["filetype"] = "";
            foreach($filetype_list as $v){
                $data["filetype"] .= $v["title"]." ";
            }
            $data["filetype"] = str_replace(" ","-",trim($data["filetype"]));
        }
        else {
            $data["filetype"] = "";
        }
        if($data["filetype"]==""){
            helper_err("由管理员没有指定上传类型，禁止使用上传功能。","",0);
            exit();
        }
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    /**
     * 上传的总入口
     */
    function common_upload(){
        $this->upload();
    }

    private function upload(){

        //region 调试信息
        //file_put_contents("./aa.txt",print_r($this->input->get(),true)."|||||||||||||||||||||||||||||||".print_r($this->input->post(),true));
        //file_put_contents("./aa.txt",print_r($this->input->files(),true));
        /*
         * Array
(
    [id] => WU_FILE_2
    [name] => p2 - 鍓湰.jpg
    [type] => image/jpeg
    [lastModifiedDate] => Tue Jan 10 2017 17:06:39 GMT+0800 (涓浗鏍囧噯鏃堕棿)
    [size] => 63249
)

         */
        //endregion

        $post = $this->input->post();
        $this->load->model("m_aiw_upload_setup");
        $this->load->model("m_aiw_dd");
        $get = $this->input->get();
        $guid = isset($get["guid"])?$get["guid"]:"";//处理方案
        if($guid==""){
            exit();
        }
        $rotation = $post["rotation"];//图片的旋转信息 参数是正负数，可能值有0 90 180 360 450 ....720
        $model = $this->m_aiw_upload_setup->get($guid);
        if(!isset($model["guid"])){
            exit();
        }
        //获取保存位置
        $dd_model = $this->m_aiw_dd->get('40f1209f-bfad-b431-ad21-86ba4ef8622c');
        //按年存放
        $config['upload_path']      = './'.$dd_model["val"].(helper_endwith($dd_model["val"],"/")?"":"/").date("Y");
        if(!is_dir($config['upload_path'])){
            @mkdirs($config['upload_path'],'0666');
        }
        $config['allowed_types']    = implode("|",$this->get_mime($guid));//指定类型
        $config['max_size']     = $model["filesize"]*1024;
        $config['file_name'] = date("Ymd_H_i_s").create_guid();
        $config['file_ext_tolower'] = true;//如果设置为 TRUE ，文件后缀名将转换为小写
        $config['encrypt_name'] = false;//如果设置为 TRUE ，文件名将会转换为一个随机的字符串 如果你不希望上传文件的人知道保存后的文件名，这个参数会很有用
        $config['max_width']        = 0;
        $config['max_height']       = 0;
        $this->load->library('upload', $config);
         if ( !  $this->upload->do_upload('shangchuan'))
         {
             $err = $this->upload->display_errors();
             //写日志
             $this->m_aiw_err_log->log(
                 print_r($err,true),
                 $this->admin_guid(),
                 $this->admin_get_username(),
                 __LINE__,
                 get_url(false)
             );
         }
         else
         {
             $info = $this->upload->data();
             //$im = @imagecreatefromjpeg($info["full_path"]);
             //if(true) {
             //   file_put_contents("./aa.txt",imagesx($im)."|".imagesy($im));
             //}
             //file_put_contents("./aa.txt","aaa=".print_r($_FILES,true));
             //region 返回式格
             /*
              * Array
                    (
                        [file_name] => 20170424_10_11_47059bb42e-59a4-8359-321e-e76e7fb22dcc.jpg
                        [file_type] => image/jpeg
                        [file_path] => E:/m/iwei/iwei/iwei/data/2017/
                        [full_path] => E:/m/iwei/iwei/iwei/data/2017/20170424_10_11_47059bb42e-59a4-8359-321e-e76e7fb22dcc.jpg
                        [raw_name] => 20170424_10_11_47059bb42e-59a4-8359-321e-e76e7fb22dcc
                        [orig_name] => 20170424_10_11_47059bb42e-59a4-8359-321e-e76e7fb22dcc.jpg
                        [client_name] => 大图测度.jpg
                        [file_ext] => .jpg
                        [file_size] => 3864.54
                        [is_image] => 1
                        [image_width] => 3216
                        [image_height] => 2028
                        [image_type] => jpeg
                        [image_size_str] => width="3216" height="2028"
                    )
              */
             //endregion
             //region 旋转图片
             unset($config);
             if(abs($rotation)>0 && abs($rotation)%360!=0) {
                 //判断有无GD
                 if (extension_loaded('gd')) {
                     //旋转图片
                     $config['image_library'] = 'gd';
                     $config['source_image'] = $info["full_path"];
                     $config['create_thumb'] = false;
                     $config['maintain_ratio'] = false;//生成的缩略图将保持图像的纵横比例，同时尽可能的在宽度和 高度上接近所设定的 width 和 height 。 缩略图将被命名为类似 mypic_thumb.jpg 的形式，并保存在 source_image 的同级目录中
                     //$config['width'] = 75;
                     //$config['height'] = 50;
                     //由于他的方向是逆时针算起
                     if ($rotation < 0) {
                         $config['rotation_angle'] = (abs($rotation) % 360);
                     } else {
                         $config['rotation_angle'] = 360 - ($rotation % 360);
                     }
                     $this->load->library('image_lib');
                     $this->image_lib->initialize($config);
                     if ($this->image_lib->rotate()) {

                     } else {
                         //写日志
                         //file_put_contents("./aa.txt", print_r($this->image_lib->display_errors(), true));
                         $this->m_aiw_err_log->log(
                             print_r($this->image_lib->display_errors(), true),
                             $this->admin_guid(),
                             $this->admin_get_username(),
                             __LINE__,
                             get_url(false)
                             );
                     }
                     $this->image_lib->clear();

                 }
                 else{
                     //写日志
                     $this->m_aiw_err_log->log(
                         "未启用GD库，所以不能做图片旋转操作",
                         $this->admin_guid(),
                         $this->admin_get_username(),
                         __LINE__,
                         get_url(false)
                     );
                 }
             }

             //endregion
             //根据处理方案，修改文件
             $thumb_arr = array();
             if($info["is_image"]) {
                 switch ($model["dd_fangan"]) {
                     //压缩图片并保留源图
                     case "40132112-eb77-0057-0556-1034d0814fce":
                         $thumb_arr = $this->fangan_pic($info);
                         $this->save_pic_1($thumb_arr,$info);
                         break;
                     //压缩图片不保留源图
                     case "b33218af-0c79-6d84-b804-24ad1ce7291a":
                         $thumb_arr = $this->fangan_pic2($info);
                         $this->save_pic_2($thumb_arr,$info);
                         break;
                     default:

                         break;
                 }
             }
             else{
                 //保存非图片数据
                 //无方案的处理方式
                 $this->save_fujian($info);
             }
         }
    }

    /**
     * 保存压缩图片并保留源图方案的保存方法
     * @param $arr 生成的所有图片全路径地址，含源图
     * @param $fileinfo 上传的源图
     */
    private function save_pic_1($arr,$fileinfo){
        $this->load->model("main/m_aiw_files");
        $this->load->helper('file');
        //优先插入源图
        $parent_guid = create_guid();
        $pathinfo = get_file_info($fileinfo["full_path"]);
        //由于full_path是含盘符的路径 所以必须要替换成 根目绿路径
        $rootpath = str_replace("\\","/",realpath("./"));
        if(!helper_endwith($rootpath,"/")){
            $rootpath .= "/";
        }
        $serverpath = str_replace($rootpath,"",$fileinfo["full_path"]);
        $model["guid"] = $parent_guid;
        $model["title"] = $fileinfo["client_name"];
        $model["filepath"] = $serverpath;
        $model["filename"] = $fileinfo["client_name"];
        $model["fullfilename"] = "";
        $model["filesize"]=$fileinfo["file_size"]*1024;//字节数
        $model["mime"] = $fileinfo["file_type"];
        $model["can_del"] = "1";//能删除
        $model["create_user"] = $this->admin_guid();
        $model["create_date"] = date("Y-m-d H:i:s");
        $model["update_user"] = "";
        $model["update_date"] = "";
        $model["del_user"] = "";
        $model["del_date"] = "";
        $model["isdel"] = "0";
        $model["sys_module_guid"] = $this->top_curr_module_guid();
        $model["upload_setup_guid"] = "";
        $model["parent_guid"] = "";
        $model["image_width"] = $fileinfo["image_width"];
        $model["image_height"] = $fileinfo["image_height"];
        $this->m_aiw_files->add($model);
        foreach($arr as $v){
            if($v!=$fileinfo["full_path"]) {
                $pathinfo = get_file_info($v,array("server_path","pathinfo","size"));
                $imgsize = getimagesize($v);
                $model["guid"] = create_guid();
                $model["title"] = $pathinfo["pathinfo"]["basename"];
                $model["filepath"] = $pathinfo["server_path"];
                $serverpath = str_replace($rootpath,"",$pathinfo["server_path"]);
                $model["filepath"] = $serverpath;
                $model["filename"] = $pathinfo["pathinfo"]["basename"];
                $model["fullfilename"] = "";
                $model["filesize"] = $pathinfo["size"];// * 1024;//字节数
                $model["mime"] = $fileinfo["file_type"];
                $model["can_del"] = "1";//能删除
                $model["create_user"] = $this->admin_guid();
                $model["create_date"] = date("Y-m-d H:i:s");
                $model["update_user"] = "";
                $model["update_date"] = "";
                $model["del_user"] = "";
                $model["del_date"] = "";
                $model["isdel"] = "0";
                $model["sys_module_guid"] = $this->top_curr_module_guid();
                $model["upload_setup_guid"] = "";
                $model["parent_guid"] = $parent_guid;
                $model["image_width"] = $imgsize[0];//$fileinfo["image_width"];
                $model["image_height"] = $imgsize[1];//$fileinfo["image_height"];
                $this->m_aiw_files->add($model);
            }
        }
    }


    /**
     * 保存压缩图片并不保留源图方案的保存方法
     * @param $arr 生成的所有图片全路径地址
     * @param $fileinfo 上传的源图
     */
    private function save_pic_2($arr,$fileinfo){
        $this->load->model("main/m_aiw_files");
        $this->load->helper('file');
        //优先插入源图
        $parent_guid = create_guid();
        $pathinfo = get_file_info($fileinfo["full_path"]);
        //由于full_path是含盘符的路径 所以必须要替换成 根目绿路径
        $rootpath = str_replace("\\","/",realpath("./"));
        if(!helper_endwith($rootpath,"/")){
            $rootpath .= "/";
        }
        $serverpath = str_replace($rootpath,"",$fileinfo["full_path"]);
        //默认第一张为主图
        $i=0;
        foreach($arr as $v){
            if($v!=$fileinfo["full_path"]) {
                $pathinfo = get_file_info($v,array("server_path","pathinfo","size"));
                $imgsize = getimagesize($v);
                $model["guid"] = ($i==0?$parent_guid:create_guid());
                $model["title"] =  $pathinfo["pathinfo"]["basename"];
                $serverpath = str_replace($rootpath,"",$pathinfo["server_path"]);
                $model["filepath"] = $serverpath;
                $model["filename"] = $pathinfo["pathinfo"]["basename"];//$fileinfo["client_name"];
                $model["fullfilename"] = "";
                $model["filesize"] = $pathinfo["size"];//$fileinfo["file_size"] * 1024;//字节数
                $model["mime"] = $fileinfo["file_type"];
                $model["can_del"] = "1";//能删除
                $model["create_user"] = $this->admin_guid();
                $model["create_date"] = date("Y-m-d H:i:s");
                $model["update_user"] = "";
                $model["update_date"] = "";
                $model["del_user"] = "";
                $model["del_date"] = "";
                $model["isdel"] = "0";
                $model["sys_module_guid"] = $this->top_curr_module_guid();
                $model["upload_setup_guid"] = "";
                $model["parent_guid"] = ($i==0?"":$parent_guid);
                $model["image_width"] = $imgsize[0];//$fileinfo["image_width"];
                $model["image_height"] = $imgsize[0];//$fileinfo["image_height"];
                $this->m_aiw_files->add($model);
            }
            $i++;
        }
    }

    /**
     * 保存图片以外的附件
     * @param $fileinfo
     */
    private function save_fujian($fileinfo)
    {
        $this->load->model("main/m_aiw_files");
        $this->load->helper('file');
        $pathinfo = get_file_info($fileinfo["full_path"]);
        //由于full_path是含盘符的路径 所以必须要替换成 根目绿路径
        $rootpath = str_replace("\\", "/", realpath("./"));
        if (!helper_endwith($rootpath, "/")) {
            $rootpath .= "/";
        }
        $model["guid"] = create_guid();
        $model["title"] = $fileinfo["client_name"];
        $serverpath = str_replace($rootpath, "",$fileinfo["full_path"]);
        $model["filepath"] = $serverpath;
        $model["filename"] = $fileinfo["client_name"];
        $model["fullfilename"] = "";
        $model["filesize"] = $fileinfo["file_size"] * 1024;//字节数
        $model["mime"] = $fileinfo["file_type"];
        $model["can_del"] = "1";//能删除
        $model["create_user"] = $this->admin_guid();
        $model["create_date"] = date("Y-m-d H:i:s");
        $model["update_user"] = "";
        $model["update_date"] = "";
        $model["del_user"] = "";
        $model["del_date"] = "";
        $model["isdel"] = "0";
        $model["sys_module_guid"] = $this->top_curr_module_guid();
        $model["upload_setup_guid"] = "";
        $model["parent_guid"] = "";
        $model["image_width"] = 0;
        $model["image_height"] = 0;
        $this->m_aiw_files->add($model);
    }
    /**
     * 通过 GUID 取得 对应的 扩展名
     * @param $update_setup_guid
     * @return array 返回标题数组
     */
    private function get_mime($update_setup_guid){
        $model = $this->m_aiw_upload_setup->get($update_setup_guid);
        $filetype_arr = explode("|",$model["filetype"]);
        $where = "";
        foreach($filetype_arr as $v){
            if($v!=""){
                if($where==""){
                    $where = " val='".$v."'";
                }
                else{
                    $where .= "or val='".$v."'";
                }
            }
        }

        if($where!=""){
            $where = "isdel='0' and (".$where.")";
            $filetype_list = $this->m_aiw_dd->get_list($where);
            $filetype = array();
            foreach($filetype_list as $v){
                array_push($filetype,$v["title"]);
            }
            return $filetype;
        }
        return array();
    }

    /**
     * 压缩并保留源图 返回图片路径
     * @param $info  CI 上传后返回的数组
     */
    private function fangan_pic($info){
        //$this->load->model("user/m_aiw_dd");
        $this->load->helper('file');
        $fullfile = $info["full_path"];
        $list = $this->m_aiw_dd->get_list("parent_guid='40132112-eb77-0057-0556-1034d0814fce'");
        $file_arr = array();
        foreach($list as $v){
            if($v["guid"]=="948ac57c-267a-566e-466d-6bdbfa807a80"){
                //源图，不用动
                $file_arr[] = $fullfile;
            }
            else{
                //处理缩略图
                if(function_exists("pathinfo")){
                    $fileinfo = get_file_info($fullfile,array('pathinfo'));
                    $fileinfo = $fileinfo["pathinfo"];
                    //file_put_contents("./aa.txt",print_r($fileinfo,true));
                    //region 返回格式
                     /*
Array
(
    [pathinfo] => Array
        (
            [dirname] => E:/site/iwei/iwei/data/2017
            [basename] => 20170428_22_48_32f354e39e-1436-70cf-cf12-81f4fcd7a022.jpg
            [extension] => jpg
            [filename] => 20170428_22_48_32f354e39e-1436-70cf-cf12-81f4fcd7a022
        )

)

                     */
                    //endregion
                    if($info["image_width"]>$v["val"]) {
                        //file_put_contents("./aa.txt",file_get_contents("./aa.txt").$info["image_width"]."|".$v["val"]."\n");
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $fullfile;
                        $config['new_image'] = $fileinfo["dirname"].(helper_endwith($fileinfo["dirname"],"/")?"":"/") .$fileinfo["filename"]."_".$v["val"] . "_" . $v["val2"].".".$fileinfo["extension"];
                        //由于CI自动新增thbumb后辍
                        $newname = $fileinfo["dirname"].(helper_endwith($fileinfo["dirname"],"/")?"":"/") .$fileinfo["filename"]."_".$v["val"] . "_" . $v["val2"]."_thumb.".$fileinfo["extension"];
                       // file_put_contents("./aa.txt",print_r($config['new_image'],true));
                        $config['create_thumb'] = TRUE;
                        $config['maintain_ratio'] = TRUE;
                        $config['quality'] = 100;//图片质量
                        $config['width'] = $v["val"];//75;
                        $config['height'] = $v["val2"];
                        $this->load->library('image_lib');//, $config);
                        $this->image_lib->initialize($config);
                        //file_put_contents("./aa.txt",file_get_contents("./aa.txt").print_r($config,true)."\n");
                        if ($this->image_lib->resize()) {
                            $file_arr[] = $newname;//$config['new_image'];
                        } else {
                            //压缩失败写日志
                            $this->m_aiw_err_log->log(
                                print_r($this->image_lib->display_errors(), true),
                                $this->admin_guid(),
                                $this->admin_get_username(),
                                __LINE__,
                                get_url(false)
                            );
                        }
                        $this->image_lib->clear();
                        unset($config);
                    }
                }
            }
        }
        //file_put_contents("./aa.txt",print_r($file_arr,true));
        return $file_arr;
    }

    /**
     * 压缩不保留源图
     * @param $fullfile
     */
    private function fangan_pic2($info){
        //$this->load->model("user/m_aiw_dd");
        $this->load->helper('file');
        $fullfile = $info["full_path"];
        $list = $this->m_aiw_dd->get_list("parent_guid='b33218af-0c79-6d84-b804-24ad1ce7291a'");
        $file_arr = array();
        foreach($list as $v){
            if($v["guid"]=="948ac57c-267a-566e-466d-6bdbfa807a80"){
                //源图，不用动
            }
            else{
                //处理缩略图
                if(function_exists("pathinfo")){
                    $fileinfo = get_file_info($fullfile,array('pathinfo'));
                    $fileinfo = $fileinfo["pathinfo"];
                    //file_put_contents("./aa.txt",print_r($fileinfo,true));
                    //region 返回格式
                    /*
Array
(
   [pathinfo] => Array
       (
           [dirname] => E:/site/iwei/iwei/data/2017
           [basename] => 20170428_22_48_32f354e39e-1436-70cf-cf12-81f4fcd7a022.jpg
           [extension] => jpg
           [filename] => 20170428_22_48_32f354e39e-1436-70cf-cf12-81f4fcd7a022
       )

)

                    */
                    //endregion
                    if($info["image_width"]>$v["val"]) {
                        //file_put_contents("./aa.txt",file_get_contents("./aa.txt").$info["image_width"]."|".$v["val"]."\n");
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $fullfile;
                        $config['new_image'] = $fileinfo["dirname"].(helper_endwith($fileinfo["dirname"],"/")?"":"/") .$fileinfo["filename"]."_".$v["val"] . "_" . $v["val2"].".".$fileinfo["extension"];
                        //由于CI自动新增thbumb后辍
                        $newname = $fileinfo["dirname"].(helper_endwith($fileinfo["dirname"],"/")?"":"/") .$fileinfo["filename"]."_".$v["val"] . "_" . $v["val2"]."_thumb.".$fileinfo["extension"];
                        $config['create_thumb'] = TRUE;
                        $config['maintain_ratio'] = TRUE;
                        $config['quality'] = 100;//图片质量
                        $config['width'] = $v["val"];//75;
                        $config['height'] = $v["val2"];
                        $this->load->library('image_lib');//, $config);
                        $this->image_lib->initialize($config);
                        if ($this->image_lib->resize()) {
                            $file_arr[] = $newname;//$config['new_image'];
                        } else {
                            //压缩失败写日志
                            $this->m_aiw_err_log->log(
                                print_r($this->image_lib->display_errors(), true),
                                $this->admin_guid(),
                                $this->admin_get_username(),
                                __LINE__,
                                get_url(false)
                            );
                        }
                        $this->image_lib->clear();
                        unset($config);
                    }
                }
            }
        }
        if(count($file_arr)>0){
            //如果有处理图，则删除源图。无就将源图写入数组
            @unlink($fullfile);
        }
        else{
            $file_arr[] = $fullfile;
        }
        return $file_arr;
    }


    function ajax()
    {
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "create_date";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "desc";

        $key = isset($get["key"]) ? $get["key"] : "";
        $key = $searchText;
        $where = " isdel='0' and create_user='".$this->admin_guid()."'";
        if ($key != "") {
            $where .= " and title like '%" . $key . "%'";
        }

        $model = $this->m_aiw_files->get_list_pager($pageindex, $pagesize, $where, $sortName . " " . $sortOrder);
        $list = $model["list"];
        foreach ($list as $k => $v) {
            $sys_module_model = $this->m_aiw_sys_module->get($v["sys_module_guid"]);
            $list[$k]["sys_module_guid_title"] = isset($sys_module_model["title"])?$sys_module_model["title"]:"-";
            $list[$k]["filesize"] = number_format($v["filesize"] / 1024 / 1024,2);
        }
        $list["rows"] = $list;
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
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
            @header("Content-Type:".$model["mime"]);
            $imagespath = $model["filepath"];
            echo file_get_contents($imagespath);
        }
        exit();
    }

}