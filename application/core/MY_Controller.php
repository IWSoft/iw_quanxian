<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
//region 前后台顶级类
/**
 * 通用顶层类，前台和后台共用
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 11:26
 */
class MY_Controller extends CI_Controller
{

    var $common_data = array();
    private $cookie_name = "iwei_s";//session的键名

    /**
     * common constructor.
     */
    public function __construct()
    {
        parent::__construct();


    }

    /**
     * 通过curl方式保存出错日志
     * @param $errno 错误NUM 自定义就写 -1， -2来自MYLOG
     * @param $msg
     */
    function top_save_log($errno, $msg)
    {
        if ($this->config->item("my_save_err_to_database")) {
            if (function_exists("curl_init")) {
                $url = site_url("/log/home/save_log");
                $post_data = array("errstr" => $msg, "errno" => $errno);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // post数据
                curl_setopt($ch, CURLOPT_POST, 1);
                // post的变量
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_exec($ch);
                curl_close($ch);
            }
        }
    }

    function top_set_session($user_guid)
    {
        $this->load->library('session');
        $this->load->model("user/m_aiw_sys_user");
        $this->load->model("user/m_aiw_sys_role");
        $this->load->model("user/m_aiw_sys_user_role");
        $this->load->model("user/m_aiw_sys_module");
        //用户实体
        $model = $this->m_aiw_sys_user->get($user_guid);
        //用户角色集
        $roles = $this->m_aiw_sys_user_role->get_roles($user_guid);
        //用户权限集
        $modules = $this->m_aiw_sys_module->get_modules($user_guid);
        $sess["model"] = $model;
        $sess["roles"] = $roles;
        $sess["modules"] = $modules;

        //$seri_session = serialize($sess);
        $cname = $this->getcookie_name();

        $this->session->set_userdata($cname, $sess);
    }

    function top_get_session()
    {
        $this->load->library('session');
        $cname = $this->getcookie_name();
        return $this->session->userdata($cname);
    }

    private function getcookie_name()
    {
        $cname = $this->cookie_name;
        $cname_arr = explode("_", $cname);
        $cname = "";
        $cname_prefix = "";
        if (count($cname_arr) > 0) {
            if (count($cname_arr) > 1) {
                $cname_prefix = $cname_arr[0];
                $cname = $cname_arr[1];
            } else {
                $cname = $cname_arr[0];
            }

        }
        if ($cname_prefix != "") {
            $cname = $cname_prefix . "_" . $cname;
        }
        return $cname;
    }

    function top_del_session()
    {
        $this->load->library('session');
        $cname = $this->getcookie_name();
        $this->session->unset_userdata($cname);
        $this->session->sess_destroy();
    }


    function top_curr_router()
    {
        return $this->router->fetch_class();
    }

    /**
     * 取得当前类所在路径
     * @return mixed
     */
    function top_curr_router_dir()
    {
        return $this->router->fetch_directory();
    }

    function top_curr_method()
    {
        return $this->router->fetch_method();
    }

    /**根据当前类名和方法名，取出权限表中对应的模块名
     * @return string 返回按parent_path顺序数组或空
     */
    function top_curr_module_arr()
    {
        $c = $this->top_curr_router();
        $m = $this->top_curr_method();
        $d = $this->top_curr_router_dir();

        if ($m != "" && $c != "") {
            $this->load->model("user/m_aiw_sys_module");

            $list = $this->m_aiw_sys_module->get_list("controller='/" . $d . "" . $c . "' and method='" . $m . "'");

            if (count($list) > 0) {
                $model = $list[0];
                //按 parent_path 读出所有节点名
                if ($model["parent_path"] != "") {
                    $path = str_replace(",", "','", $model["parent_path"]);
                    $all_list = $this->m_aiw_sys_module->get_list("guid in('" . $path . "')", "FIND_IN_SET(guid,'" . $model["parent_path"] . "')");
                    array_push($all_list, $model);
                }
            }
        }
        if (isset($all_list)) {
            return $all_list;
        } else {
            return array();
        }
    }

    /**
     * 获得当前模块的GUID
     * @return string
     */
    function top_curr_module_guid()
    {
        $module_list = $this->top_curr_module_arr();
        if (isset($module_list[count($module_list) - 1]["guid"])) {
            return $module_list[count($module_list) - 1]["guid"];
        } else {
            return "";
        }
    }

    /**
     * 读出最后一个操作名
     */
    function top_curr_module_last_name()
    {
        $arr = $this->top_curr_module_arr();
        if (count($arr) > 0) {
            return $arr[count($arr) - 1]["title"];
        } else {
            return "";
        }
    }

    /**
     * 读出第一个操作名
     */
    function top_curr_module_root_name()
    {
        $arr = $this->top_curr_module_arr();
        if (count($arr) > 0) {
            return $arr[0]["title"];
        } else {
            return "";
        }
    }

    /**
     * 按 parent_path 顺序读出所有节点名
     */
    function top_curr_module_path_name($split = ">")
    {
        $arr = $this->top_curr_module_arr();
        $path = "";
        if (count($arr) > 0) {
            foreach ($arr as $k => $v) {

                $path .= $v["title"] . ($k < (count($arr) - 1) ? " > " : "");
            }
        }
        return $path;
    }


}

//endregion

#region 后 台 通 用 父 类
/**
 * 后台通用父类
 * Class MY_AdminController
 */
class MY_AdminController extends MY_Controller
{
    var $admin_data = array();
    var $curr_path = "";
    var $curr_quanxian_btn = "";
    var $curr_top_btn = "";
    var $curr_form_btn = "";
    var $curr_form_list_btn = "";//列表行中按钮
    var $curr_curr_module_name = "";//当前模块名

    public function __construct()
    {
        parent::__construct();
        //比业务控制器优先执行
        if ($this->admin_get_session() == "") {
            header("location:" . site_url2("/home/login/index") . "?url=");//ndex.html?v=4.0
        }
        $this->admin_get_curr_module_name();
        $this->curr_path = $this->top_curr_module_path_name();//获取路径
        $this->get_btn();//获取按钮

        foreach ($this->curr_quanxian_btn as $k => $v) {
            //内页顶部按钮
            if ($v["module_type"] == "20") {
                $this->curr_top_btn[] = $v;
            }
            //内页表单按钮
            if ($v["module_type"] == "30") {
                $this->curr_form_btn[] = $v;
            }
            //内页表单列表每行的按钮
            if ($v["module_type"] == "50") {
                $this->curr_form_list_btn[] = $v;
            }
        }

        $this->admin_chk_permission();
    }


    function index()
    {


    }


    private function get_btn()
    {
        $session = $this->admin_get_session();

        if (isset($session["modules"]) && isset($session["model"]["guid"])) {
            //取得当前模块
            //$this->top_curr_router()
            $this->load->model("user/m_aiw_sys_module");
            $curr_module_guid = $this->top_curr_module_guid();
            if ($curr_module_guid != "") {
                //根据用户权限，读出当前模块下的有权操作的按钮
                $btn = $this->m_aiw_sys_module->get_curr_module_btn_list($session["model"]["guid"], $curr_module_guid);
            } else {
                $btn = array();
            }
        } else {
            $btn = array();
        }
        //将数据字典中的按钮风格 初始化按钮数组
        $this->load->model("user/m_aiw_dd");
        $dd_btn_list = $this->m_aiw_dd->get_list_pid($this->config->item("def_dd_btn_guid"), false, $sort = "sort desc");//false为多层
        foreach ($btn as $k => $v) {
            $btn[$k]["btn_color_css"] = "btn btn-default";
            $btn[$k]["btn_icon_style"] = "";
            foreach ($dd_btn_list as $v2) {
                if ($v["method"] == $v2["val"]) {
                    $btn[$k]["btn_color_css"] = $v2["val3"];
                    $btn[$k]["btn_icon_style"] = $v2["val2"];
                    break;
                }
            }
        }

        $this->curr_quanxian_btn = $btn;

    }

    /**保存会话
     * @param $user_guid 用户表的GUID
     * 返回sessionid
     */
    function admin_set_session($user_guid)
    {
        $this->top_set_session($user_guid);
    }

    /**
     * 返回表单或表格的按钮组
     */
    function admin_get_form_btn()
    {
        $list = $this->curr_form_btn;
        $html = "";
        if (is_array($list)) {
            foreach ($list as $k => $v) {
                if ($v["url_target"] == "_layerbox") {


                    $html .= '<button type="button" id="form_' . $v["guid"] . '" url="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . '"  onclick="my_open_box({title:\'' . $v["title"] . '\',url:$(\'#' . ('top_menu_' . $v["guid"]) . '\').attr(\'url\'),width:0,height:0,func:chk_form_btn,args:[this]})"  class="btn btn-sm ' . $v["btn_color_css"] . '" >';
                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                    }
                    $html .= '&nbsp;' . $v["title"] . '</button>';
                } elseif ($v["url_target"] == "_blank") {
                    $html .= '<button type="button" id="form_' . $v["guid"] . '" href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . '" title="' . $v["title"] . '"  class="page-action btn btn-sm ' . $v["btn_color_css"] . '" >';

                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                    }
                    $html .= '&nbsp;' . $v["title"] . '</button>';
                } else {
                    //_self
                    $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                    $html .= '<a id="form_' . $v["guid"] . '" href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . '"  class="btn btn-sm ' . $v["btn_color_css"] . '" >';
                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                    }
                    if ($v["title"] != "") {
                        $html .= '&nbsp;' . $v["title"] . '</a>';
                    } else {
                        $html .= '</a>';
                    }
                }
            }
        }

        return $html;
    }

    /**
     * 返回表单列表每行的按钮，转换为 Bootstrap table 格式
     */
    function admin_get_list_btn()
    {
        $list = $this->curr_form_list_btn;
        $html = "";
        $arr = array();
        $i = 0;
        if (is_array($list)) {
            foreach ($list as $k => $v) {

                $arr[$i]["field"] = "btn_list_" . $i;
                $arr[$i]["title"] = " ";
                $arr[$i]["width"] = "5%";
                $arr[$i]["formatter"] = "";
                $html = "";
                if ($v["url_target"] == "_blank") {

                    $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                    $html .= '<button type=\"button\" id=\"form_list_' . $v["guid"] . '_"+row.guid+"\" href=\"' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid="+row.guid+"\" title=\"' . $v["title"] . '"+row.title+"\"  class=\"page-action btn btn-sm ' . $v["btn_color_css"] . '\" >';
                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class=\"' . $v["btn_icon_style"] . '\"></i> ';
                    }
                    if ($v["title"] != "") {
                        $html .= '&nbsp;' . $v["title"] . '</button>';
                    } else {
                        $html .= '</button>';
                    }
                } elseif ($v["url_target"] == "_layerbox") {
                    $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                    $html .= '<button type=\"button\" id=\"form_list_' . $v["guid"] . '_"+row.guid+"\" url=\"' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid="+row.guid+"\"  onclick=\"my_open_box({title:\'' . $v["title"] . '\',url:$(\'#' . ('form_list_' . $v["guid"]) . '_"+row.guid+"\').attr(\'url\'),width:0,height:0,func:chk_form_list_btn,args:[this]})\"  class=\"btn btn-sm ' . $v["btn_color_css"] . '\" >';
                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class=\"' . $v["btn_icon_style"] . '\"></i> ';
                    }
                    if ($v["title"] != "") {
                        $html .= '&nbsp;' . $v["title"] . '</button>';
                    } else {
                        $html .= '</button>';
                    }
                } else {

                    $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                    $html .= '<a id=\"form_list_' . $v["guid"] . '\" href=\"' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid="+row.guid+"\"  class=\"btn btn-sm ' . $v["btn_color_css"] . '\" >';
                    if ($v["btn_color_css"] != "") {
                        $html .= '<i class=\"' . $v["btn_icon_style"] . '\"></i> ';
                    }
                    if ($v["title"] != "") {
                        $html .= '&nbsp;' . $v["title"] . '</a>';
                    } else {
                        $html .= '</a>';
                    }
                }
                $html = " function (value, row, index) {return \"" . $html . "\"}";
                $arr[$i]["formatter"] = $html;
                $i++;
            }
            //组合HTML
            $html = "";
            for ($i = 0; $i < count($arr); $i++) {
                $html .= ",{\n";
                $j = 0;
                foreach ($arr[$i] as $k => $v) {

                    if ($j > 0) {
                        $html .= ",";
                    }
                    if ($k == "formatter") {
                        $html .= $k . ":" . $v . "\n";
                    } else {
                        $html .= $k . ":'" . $v . "'\n";
                    }
                    $j++;
                }
                $html .= "}\n";
            }
        }


        return $html;
    }

    /**取用户会话
     * @param $guid 用户表的GUID
     */
    function admin_get_session()
    {
        return $this->top_get_session();
    }

    function admin_del_session()
    {
        $this->top_del_session();
    }

    /**
     * 获取管理员的GUID
     * @return string
     */
    function admin_guid()
    {
        $sess = $this->top_get_session();
        if (isset($sess["model"])) {
            return $sess["model"]["guid"];
        } else {
            return "";
        }
    }

    function admin_roles()
    {
        $sess = $this->top_get_session();
        if (isset($sess["roles"])) {
            return $sess["roles"];
        } else {
            return "";
        }
    }

    //region 读取form开头的字段，合成一个MODEL
    /**
     * @return array
     */
    function admin_get_model()
    {
        $post = $this->input->post();
        $model = array();
        foreach ($post as $k => $v) {
            if (stripos($k, "form_") !== false) {
                $model[substr($k, 5, strlen($k) - 5)] = $v;
            }
        }
        return $model;
    }
    //endregion
    /**
     * 读出当前模块名称
     * @return string
     */
    function admin_get_curr_module_name()
    {
        $this->curr_curr_module_name = $this->top_curr_module_last_name();
        return $this->curr_curr_module_name;
    }

    /**
     * 获取用户名
     */
    function admin_get_username(){
        $sess = $this->top_get_session();
        if (isset($sess["model"])) {
            return $sess["model"]["username"];
        } else {
            return "";
        }
    }


    /**
     * 根据用户权限检查当前模块是否有权访问
     */
    function admin_chk_permission()
    {


        //不需要检查的模块
        $guest_permission = array("home/index", "msg/ok", "msg/err", "login/dologin","home/index");
        //取得当前控制器和方法名
        $controller = strtolower($this->router->class);
        $method = strtolower($this->router->method);
        //echo $controller."/".$method;
        //die();
        $sess = $this->admin_get_session();

        //过滤公共方法
        if (!in_array($controller . "/" . $method, $guest_permission)) {
            //取出用户拥有权限
            if (isset($sess["modules"])) {
                //获取当前模块的GUID
                $curr_module_guid = $this->top_curr_module_guid();
                if ($curr_module_guid == "") {

                    helper_err(lang("err_no_permission"),get_url(false));
                    exit();
                }

                if (!in_array($curr_module_guid, $sess["modules"])) {
                    helper_err(lang("err_no_permission"),get_url(false));
                    exit();
                }

            } else {
                helper_err(lang("err_no_permission"),get_url(false));
                exit();
            }
        }

    }
}

#endregion

#region 前 台 通 用 父 类
/**
 * 前台通用父类
 * Class MY_AdminController
 */
class MY_HomeController extends MY_Controller
{
    var $home_data = array();

    public function __construct()
    {
        parent::__construct();

    }

    function index()
    {

    }


    /**保存会话
     * @param $user_guid 用户表的GUID
     * 返回sessionid
     */
    function home_set_session($user_guid)
    {
        $this->top_set_session($user_guid);
    }

    /**取用户会话
     * @param $guid 用户表的GUID
     */
    function home_get_session()
    {
        return $this->top_get_session();
    }

    function home_del_session()
    {
        $this->top_del_session();
    }
}
#endregion

