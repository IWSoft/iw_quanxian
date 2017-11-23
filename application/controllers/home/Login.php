<?php
class Login extends MY_HomeController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('user/m_aiw_sys_user');
        $this->load->model('user/m_aiw_sys_user_role');
        $this->load->model('user/m_aiw_dd');
        $this->load->model('main/m_aiw_files');
    }

    function index(){
        //判断是否在微信端
        if(helper_is_weixin() && !__WEIXIN_KAIFA__){
            //取OPENIND
            //初始化
            $ch = curl_init();
            //设置选项，包括URL
            curl_setopt($ch, CURLOPT_URL, site_url2("weixin/wxauth/index"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            //执行并获取HTML文档内容
            $output = curl_exec($ch);
            //释放curl句柄
            curl_close($ch);
            //打印获得的数据
            print_r($output);
        }
        $get  = $this->input->get();
        $data = $this->home_data;
        $data["url"] = isset($get["url"])?$get["url"]:"";
        //系统名称
        $model = $this->m_aiw_dd->get("b72b7935-c089-88c7-c9b6-07a5b97652d7");
        if(isset($model)){
            $system_name = $model["val"];
        }
        else{
            $system_name = "管理后台";
        }
        //读出背景图
        $bk_img_model = $this->m_aiw_dd->get("5d49cac6-d9a5-2aea-6b39-c9040d2a2b4b");
        if(isset($bk_img_model["val"])){
            if($bk_img_model["val"]!="") {
                $filemodel = $this->m_aiw_files->get($bk_img_model["val"]);
            }
        }
        $data["login_bk_img"] = (isset($filemodel["filepath"])?$filemodel["filepath"]:"");
        $data["system_name"] = $system_name;
        if(helper_is_mobile()) {
            $this->load->view(__HOME_TEMPLATE__ . "/main/login_youbao", $data);
        }
        else{
            $this->load->view(__HOME_TEMPLATE__ . "/main/login", $data);
        }
    }

    /**
     * 登录
     */
    function dologin(){
        $post = $this->input->post();
        $result["err"] = "";
        $result["isok"] = "0";
        $user = isset2($post["user"]);
        $pwd = isset2($post["pwd"]);
        if($user!="" && $pwd!=""){
            $model = $this->m_aiw_sys_user->dologin($user,$pwd);
            if(!$model){
                $result["err"] = lang("err_user_or_pwd_error");
            }
            else{
                //成功登录
                $result["isok"] = "1";
                $this->home_set_session($model["guid"]);
            }
        }
        else{
            $result["err"] = lang("err_user_pwd");

        }

        exit(json_encode($result));
    }

    function logout(){
        $this->home_del_session();
        $get = $this->input->get();
        $url = isset($get["url"])?$get["url"]:"";
        $url = site_url2("/home/login/index")."?url=".urlencode($url);
        header("location:".$url);
    }

    function testupload(){
        if(count($_FILES)>0){

            //按年存放
            $config['upload_path']      = './data/2017/';
            $config['allowed_types']    = "jpg";
            $config['max_size']     = 50*1024;
            $config['file_name'] = date("Ymd_H_i_s").create_guid();
            $config['file_ext_tolower'] = true;//如果设置为 TRUE ，文件后缀名将转换为小写
            $config['encrypt_name'] = false;//如果设置为 TRUE ，文件名将会转换为一个随机的字符串 如果你不希望上传文件的人知道保存后的文件名，这个参数会很有用
            //$config['max_width']        = 0;
            //$config['max_height']       = 0;
            $this->load->library('upload', $config);

            if ( !  $this->upload->do_upload('shangchuan'))
            {

            }
            else {
                $info = $this->upload->data();
                echo "<pre>";
                print_r($info);
                echo "</pre>";
            }
        }
        $this->load->view(__HOME_TEMPLATE__."/main/testupload");
    }

    /**
     * 登录成功处理权限会话
     * @param $model
     */
    private function getsession($model){

    }
}