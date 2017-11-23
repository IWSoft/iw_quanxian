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

        //以下是提取订餐数据
        $this->load->model("company/m_aiw_company");
        $this->load->model("company/m_aiw_company_user_link");
        $this->load->model("dingcan/m_dingcan_company_attr");


        //用户实体
        $model = $this->m_aiw_sys_user->get($user_guid);
        //用户角色集
        $roles = $this->m_aiw_sys_user_role->get_roles($user_guid);
        //用户权限集
        $modules = $this->m_aiw_sys_module->get_modules($user_guid);
        $sess["model"] = $model;
        $sess["roles"] = $roles;
        $sess["modules"] = $modules;

        //region 抓取实体
        $company_guid_list = $this->m_aiw_company_user_link->get_by_user_guid($user_guid);
        if (count($company_guid_list) > 0) {
            $sess["company"] = $this->m_aiw_company->get($company_guid_list[0]["company_guid"]);
            $sess["company_attr"] = $this->m_dingcan_company_attr->get_by_guid($company_guid_list[0]["company_guid"]);
        } else {
            $sess["company"] = "";
            $sess["company_attr"] = "";
        }
        //endregion
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


    /**
     * 发送系统通知
     * @param $receive_user   接收人的GUID，用逗号隔开 ，如无，跟发给自己除外所有用户
     * @param $sys_module_id  所在模块ID，如无，则用默认方式显示
     * @param $msg_level 数字越高，重要性越强 如：0普通信息 10提醒 20警告 100重点关注
     * @param $title 消息标题
     * @param $msg   消息内容
     */
    function top_send_sys_message($receive_user = "", $sys_module_id = "", $msg_level = 0, $title, $msg)
    {
        //数字越高，重要性越强 如：0普通信息 10提醒 20警告 100重点关注
        $this->load->model('main/m_aiw_sys_message');
        $this->load->model('user/m_aiw_sys_user');
        $userguids = "";
        $usermodel = $this->top_get_session();
        $guid = isset($usermodel["model"]["guid"]) ? $usermodel["model"]["guid"] : "";
        if ($receive_user == "") {
            $list = $this->m_aiw_sys_user->get_list("guid<>'" . $guid . "' and isdel='0'");
            foreach ($list as $k => $v) {
                if ($k == 0) {
                    $userguids = $v["guid"];
                } else {
                    $userguids .= "," . $v["guid"];
                }
            }
            $receive_user = $userguids;
        } else {
            $receive_user = $userguids;
        }
        if ($userguids != "") {
            $this->m_aiw_sys_message->send_msg_to_users($receive_user, $title, $msg, $msg_level, $sys_module_id, "");
        }
    }

    /**
     * 按日期删除订餐
     * @param $riqi
     * @param $shiduan 早餐或午餐 ，为空则所有
     */
    function top_del_dingcan($riqi, $shiduan = '')
    {
        $this->load->model("m_common");
        $sql = "delete from dingcan_list where dingcan_riqi='" . $riqi . "' " . ($shiduan != "" ? " and dingcan_type='" . $shiduan . "'" : "");
        $this->m_common->del_data($sql);
    }

    function top_dingcan_create($riqi, $shiduan = '')
    {
        $this->load->model("dingcan/m_dingcan_list");
        $this->load->model("company/m_aiw_company");
        $company_list = $this->m_aiw_company->get_list_pager(1, 1000, "t1.isdel='0'", "");
        $company_list = $company_list["list"];
        foreach ($company_list as $v) {
            if (isset($v["week_" . date("w", strtotime($riqi))]) && $v["week_" . date("w", strtotime($riqi))] == '1' && time() > $v["dingcan_start"] && time() < $v["dingcan_end"]) {
                //按日期生成并且在有效期内
                $this->top_dingcan_create_bycompany($v, $riqi, $shiduan);
            }
        }
    }

    private function top_dingcan_create_bycompany($company_model, $riqi, $shiduan = '')
    {
        $this->load->model("company/m_aiw_company_user_link");
        $this->load->model("dingcan/m_dingcan_list");
        $this->load->model("user/m_aiw_sys_user");
        $arr = array(
            "zc" => $this->config->item("dingcan_zaocan_guid"),
            "wc" => $this->config->item("dingcan_wucan_guid")
        );
        $guding_can_guid = $this->config->item("dingcan_guding_guid");
        $quanxian = $company_model["quanxian"];
        $quanxian = explode(",", $quanxian);


        $userlist = $this->m_aiw_company_user_link->get_list("company_guid='" . $company_model["guid"] . "'");
        $iszc = count($quanxian) > 0 && in_array($guding_can_guid, $quanxian) && ($shiduan == '' || $shiduan == $arr["zc"]);
        $iswc = count($quanxian) > 0 && in_array($guding_can_guid, $quanxian) && ($shiduan == '' || $shiduan == $arr["wc"]);
        foreach ($userlist as $v) {
            //只能check_status=10的人才能生成
            $usermodel = $this->m_aiw_sys_user->get($v["user_guid"]);
            if (isset($usermodel["check_status"]) && $usermodel["check_status"] == 10) {
                if ($iszc) {

                    $count = $this->m_dingcan_list->get_count(
                        "is_tmp='0' and 
                    dingcan_riqi='" . $riqi . "' and 
                    dingcan_type='" . $arr["zc"] . "' and
                    dingcan_user='" . $v["user_guid"] . "'
                    ");
                    if ($count == 0) {
                        $model["guid"] = create_guid();
                        $model["is_tmp"] = '0';
                        $model["dingcan_riqi"] = $riqi;
                        $model["dingcan_type"] = $arr["zc"];
                        $model["dingcan_user"] = $v["user_guid"];
                        $model["dingcan_status"] = "0";
                        $model["dingcan_cancel_time"] = "0";
                        $model["create_time"] = time();
                        $model["dingcan_cancel_beizhu"] = "";
                        $model["dingcan_zhuanchu_beizhu"] = "";
                        $model["dingcan_zhuanchu_time"] = "";
                        $model["dingcan_zhuanchu_user"] = "";
                        $model["dingcan_jieshou_beizhu"] = "";
                        $this->m_dingcan_list->add($model);
                    }
                }
                if ($iswc) {

                    $count = $this->m_dingcan_list->get_count(
                        "is_tmp='0' and 
                    dingcan_riqi='" . $riqi . "' and 
                    dingcan_type='" . $arr["wc"] . "' and
                    dingcan_user='" . $v["user_guid"] . "'
                    ");
                    if ($count == 0) {
                        $model["guid"] = create_guid();
                        $model["is_tmp"] = '0';
                        $model["dingcan_riqi"] = $riqi;
                        $model["dingcan_type"] = $arr["wc"];
                        $model["dingcan_user"] = $v["user_guid"];
                        $model["dingcan_status"] = "0";
                        $model["dingcan_cancel_time"] = "0";
                        $model["create_time"] = time();
                        $model["dingcan_cancel_beizhu"] = "";
                        $model["dingcan_zhuanchu_beizhu"] = "";
                        $model["dingcan_zhuanchu_time"] = "";
                        $model["dingcan_zhuanchu_user"] = "";
                        $model["dingcan_jieshou_beizhu"] = "";
                        $this->m_dingcan_list->add($model);
                    }
                }
            }
        }
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
     * @param bool $isdropdown 以下拉菜单形式展示
     * @return string
     */
    function admin_get_list_btn($isdropdown = true)
    {
        $list = $this->curr_form_list_btn;
        $html = "";
        $arr = array();
        $i = 0;
        if (is_array($list)) {

            if ($isdropdown && count($list)>1) {
                $arr[$i]["clickToSelect"] = "false";
                $arr[$i]["field"] = "btn_list_dropdown";
                $arr[$i]["title"] = "操作";
                $arr[$i]["width"] = "1%";
                $arr[$i]["formatter"] = "";
                $html = "<div class=\"btn-group dropup\"><button data-toggle=\"dropdown\" class=\"btn btn-white dropdown-toggle\" type=\"button\">操作<span class=\"caret\"></span></button><ul style=\"z-index:9999;\" class=\"dropdown-menu\">";
                foreach ($list as $k => $v) {
                    $html .= "<li>";
                    if ($v["url_target"] == "_blank") {

                        $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                        $html .= '<a id="form_list_' . $v["guid"] . '_\'+row.guid+\'" href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid="\'+row.guid+\'" title="' . $v["title"] . '\'+row.title+\'"  class="page-action btn btn-sm ' . $v["btn_color_css"] . '" >';
                        if ($v["btn_color_css"] != "") {
                            $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                        }
                        if ($v["title"] != "") {
                            $html .= '&nbsp;' . $v["title"] . '</a>';
                        } else {
                            $html .= '</a>';
                        }
                    } elseif ($v["url_target"] == "_layerbox") {
                        $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                        $html .= '<a href="javascript:void(0);" id="form_list_' . $v["guid"] . '_\'+row.guid+\'" url="'.create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid=\'+row.guid+\'"  onclick="my_open_box({title:\\\'' . $v["title"] . '\\\',url:$(\\\'#' . ('form_list_' . $v["guid"]) . '_\'+row.guid+\'\\\').attr(\\\'url\\\'),width:0,height:0,func:chk_form_list_btn,args:[this]})"  class="btn btn-sm ' . $v["btn_color_css"] . '" >';
                        if ($v["btn_color_css"] != "") {
                            $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                        }
                        if ($v["title"] != "") {
                            $html .= '&nbsp;' . $v["title"] . '</a>';
                        } else {
                            $html .= '</a>';
                        }
                    } else {

                        $v["param"] .= ($v["param"] == "" ? ("rnd=" . mt_rand(1, 100) . "&") : "&");//初始化一个值 ,无实际用途
                        $html .= '<a id="form_list_' . $v["guid"] . '" href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . 'guid=\'+row.guid+\'"  class="btn btn-sm ' . $v["btn_color_css"] . '" >';
                        if ($v["btn_color_css"] != "") {
                            $html .= '<i class="' . $v["btn_icon_style"] . '"></i> ';
                        }
                        if ($v["title"] != "") {
                            $html .= '&nbsp;' . $v["title"] . '</a>';
                        } else {
                            $html .= '</a>';
                        }
                    }
                    $html .= "</li>";
                    //<li><a href="form_basic.html#">选项1</a></li></ul>
                }
                $html .= "</ul></div>";
                if(count($list)==0){
                    $html = "";
                }
                $html = " function (value, row, index) {return '" . $html . "'}";
                $arr[$i]["formatter"] = $html;
            } else {
                foreach ($list as $k => $v) {
                    $arr[$i]["clickToSelect"] = "false";
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

                    if ($k == "formatter" || $k=="clickToSelect") {
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
    function admin_get_username()
    {
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
        $guest_permission = array("home/index", "msg/ok", "msg/err", "login/dologin", "home/index", "home/get_system_msg", "home/view_system_msg", "home/list_system_msg", "home/list_system_msg_ajax");
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

                    helper_err(lang("err_no_permission"), get_url(false));
                    exit();
                }

                if (!in_array($curr_module_guid, $sess["modules"])) {
                    helper_err(lang("err_no_permission"), get_url(false));
                    exit();
                }

            } else {
                helper_err(lang("err_no_permission"), get_url(false));
                exit();
            }
        }

    }

    /**
     * 取得最新的订餐时效
     * 修改后，记录同步到home里边也有相同一个
     */
    function get_dingcan_config()
    {
        $config["err"] = "";//不为空，代表有些值没有配置

        //针对单位今天和明天是否开放订餐
        $sql = "select t2.* from aiw_company_user_link t1 left join 
      dingcan_company_attr t2 on t1.company_guid=t2.company_guid where user_guid='" . $this->admin_guid() . "'";
        $company_attr_model = $this->m_common->query_one($sql);
        if (isset($company_attr_model["dingcan_start"])) {
            if ($company_attr_model["dingcan_start"] > 0) {
                $config["start"] = $company_attr_model["dingcan_start"];
            } else {
                $config["err"] = "单位无设置订餐开始时间 ";
            }
        } else {
            $config["err"] = "单位无设置订餐开始时间 ";
        }
        if (isset($company_attr_model["dingcan_end"])) {
            if ($company_attr_model["dingcan_end"] > 0) {
                $config["end"] = $company_attr_model["dingcan_end"];
            } else {
                $config["err"] = "单位无设置订餐结束时间 ";
            }
        } else {
            $config["err"] = "单位无设置订餐结束时间 ";
        }
        $curr_w = date("w");
        $curr_w2 = date("w") == 6 ? 0 : (date("w") + 1);
        $config["curr_week"] = isset($company_attr_model["week_" . $curr_w]) ? $company_attr_model["week_" . $curr_w] : 0;//今天
        $config["curr_week2"] = isset($company_attr_model["week_" . $curr_w2]) ? $company_attr_model["week_" . $curr_w2] : 0;//明天


        $config["linshi_amount_text"] = "每天每人可订份数";//临时订餐数量设置
        $config["linshi_amount"] = "";
        $config["linshi_amount_zaocan_text"] = "早餐总数量";//临时订餐数量设置
        $config["linshi_amount_zaocan"] = "";
        $config["linshi_amount_wucan_text"] = "午餐总数量";//临时订餐数量设置
        $config["linshi_amount_wucan"] = "";

        $config["linshi_btn_text"] = "开放或关闭临时订餐功能";
        $config["linshi_btn"] = "0";//1为开启临时订餐

        $config["can_flag_text"] = "固定和临餐开关";
        $config["can_flag"] = "0";//1为开启订餐


        $config["jiezhi_linshi_text"] = "接收明天临时订餐(早、午餐)时间是今天：";
        $config["jiezhi_linshi_start"] = "";
        $config["jiezhi_linshi_end"] = "";
        $config["jiezhi_linshi_jintian_text"] = "今天临时订餐结束时间";
        $config["jiezhi_linshi_jintian_zaocan"] = "";
        $config["jiezhi_linshi_jintian_wucan"] = "";
        $config["jiezhi_gucan_text"] = "明天固定订餐取消截止时间是今天：";
        $config["jiezhi_gucan_end"] = "";
        $config["jiezhi_zhuanchu_text"] = "今天的转出或接收餐的截止时间：";
        $config["jintian_zhuanchu_zaocan"] = "";
        $config["jintian_zhuanchu_wucan"] = "";

        $mingtian_start_guid = $this->config->item("dingcan_jintian_mingtian_linshi");//知道认明天的开始时间
        $mingtian = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $mingtian_start_guid . "'");
        $config["jiezhi_linshi_start"] = isset($mingtian["val"]) ? $mingtian["val"] : "";
        $config["jiezhi_linshi_end"] = isset($mingtian["val2"]) ? $mingtian["val2"] : "";
        $jintian_jiezhi_guid = $this->config->item("dingcan_jintian_linshi");
        $jintian_jiezhi = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $jintian_jiezhi_guid . "'");
        $config["jiezhi_linshi_jintian_zaocan"] = isset($jintian_jiezhi["val"]) ? $jintian_jiezhi["val"] : "";
        $config["jiezhi_linshi_jintian_wucan"] = isset($jintian_jiezhi["val2"]) ? $jintian_jiezhi["val2"] : "";
        $gucan_jin_ming_jiezhi_guid = $this->config->item("dingcan_mingtian_quxiao_gucan");
        $gucan_jin_ming_jiezhi = $this->m_common->query_one("select val from aiw_dd where guid='" . $gucan_jin_ming_jiezhi_guid . "'");
        $config["jiezhi_gucan_end"] = isset($gucan_jin_ming_jiezhi["val"]) ? $gucan_jin_ming_jiezhi["val"] : "";
        $jintian_zhuanchu_guid = $this->config->item("dingcan_zhuanchu_jieshou");
        $jintian_zhuanchu = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $jintian_zhuanchu_guid . "'");
        $config["jintian_zhuanchu_zaocan"] = isset($jintian_zhuanchu["val"]) ? $jintian_zhuanchu["val"] : "";
        $config["jintian_zhuanchu_wucan"] = isset($jintian_zhuanchu["val2"]) ? $jintian_zhuanchu["val2"] : "";

        $dingcan_linshi_amount_guid = $this->config->item("dingcan_linshi_amount");
        $dingcan_linshi_amount = $this->m_common->query_one("select val,val2,val3 from aiw_dd where guid='" . $dingcan_linshi_amount_guid . "'");
        $config["linshi_amount"] = isset($dingcan_linshi_amount["val"]) ? $dingcan_linshi_amount["val"] : "";
        $config["linshi_amount_zaocan"] = isset($dingcan_linshi_amount["val2"]) ? $dingcan_linshi_amount["val2"] : "";
        $config["linshi_amount_wucan"] = isset($dingcan_linshi_amount["val3"]) ? $dingcan_linshi_amount["val3"] : "";


        $dingcan_linshi_btn_guid = $this->config->item("dingcan_linshi_btn");
        $dingcan_linshi_btn = $this->m_common->query_one("select val,val2,val3 from aiw_dd where guid='" . $dingcan_linshi_btn_guid . "'");
        $config["linshi_btn"] = isset($dingcan_linshi_btn["val"]) ? $dingcan_linshi_btn["val"] : "0";
        $config["can_flag"] = isset($dingcan_linshi_btn["val2"]) ? $dingcan_linshi_btn["val2"] : "0";

        if ($config["jiezhi_linshi_start"] == "") {
            $config["err"] = $config["jiezhi_linshi_text"] . "开始时间没有设置";
        }
        if ($config["jiezhi_linshi_end"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_text"] . "结束时间没有设置";
        }
        if ($config["jiezhi_linshi_jintian_zaocan"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_jintian_text"] . "早餐无设置";
        }
        if ($config["jiezhi_linshi_jintian_wucan"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_jintian_text"] . "午餐无设置";
        }
        if ($config["jiezhi_gucan_end"] == "") {
            $config["err"] = " " . $config["jiezhi_gucan_text"] . "取消截止时间无设置";
        }
        if ($config["jintian_zhuanchu_zaocan"] == "") {
            $config["err"] = " " . $config["jiezhi_zhuanchu_text"] . "早餐转出或接取截止时间无设置";
        }
        if ($config["jintian_zhuanchu_wucan"] == "") {
            $config["err"] = " " . $config["jiezhi_zhuanchu_text"] . "午餐转出或接取截止时间无设置";
        }
        if ($config["linshi_amount"] == "") {
            $config["err"] = " " . $config["linshi_amount_text"] . "无设置";
        }
        if ($config["linshi_amount_zaocan"] == "") {
            $config["err"] = " " . $config["linshi_amount_zaocan_text"] . "无设置";
        }
        if ($config["linshi_amount_wucan"] == "") {
            $config["err"] = " " . $config["linshi_amount_wucan_text"] . "无设置";
        }
        return $config;
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

    /**
     * 取得最新的订餐时效
     * 修改后，记录同步到admin里边也有相同一个
     */
    function home_get_dingcan_config()
    {
        $config["err"] = "";//不为空，代表有些值没有配置


        $curr_w = date("w");
        $curr_w2 = date("w") == 6 ? 0 : (date("w") + 1);
        $config["curr_week"] = isset($company_attr_model["week_" . $curr_w]) ? $company_attr_model["week_" . $curr_w] : 0;//今天
        $config["curr_week2"] = isset($company_attr_model["week_" . $curr_w2]) ? $company_attr_model["week_" . $curr_w2] : 0;//明天


        $config["linshi_amount_text"] = "每天每人可订份数";//临时订餐数量设置
        $config["linshi_amount"] = "";
        $config["linshi_amount_zaocan_text"] = "早餐总数量";//临时订餐数量设置
        $config["linshi_amount_zaocan"] = "";
        $config["linshi_amount_wucan_text"] = "午餐总数量";//临时订餐数量设置
        $config["linshi_amount_wucan"] = "";

        $config["linshi_btn_text"] = "开放或关闭临时订餐功能";
        $config["linshi_btn"] = "0";//1为开启临时订餐

        $config["can_flag_text"] = "固定和临餐开关";
        $config["can_flag"] = "0";//1为开启订餐


        $config["jiezhi_linshi_text"] = "接收明天临时订餐(早、午餐)时间是今天：";
        $config["jiezhi_linshi_start"] = "";
        $config["jiezhi_linshi_end"] = "";
        $config["jiezhi_linshi_jintian_text"] = "今天临时订餐结束时间";
        $config["jiezhi_linshi_jintian_zaocan"] = "";
        $config["jiezhi_linshi_jintian_wucan"] = "";
        $config["jiezhi_gucan_text"] = "明天固定订餐取消截止时间是今天：";
        $config["jiezhi_gucan_end"] = "";
        $config["jiezhi_zhuanchu_text"] = "今天的转出或接收餐的截止时间：";
        $config["jintian_zhuanchu_zaocan"] = "";
        $config["jintian_zhuanchu_wucan"] = "";

        $mingtian_start_guid = $this->config->item("dingcan_jintian_mingtian_linshi");//知道认明天的开始时间
        $mingtian = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $mingtian_start_guid . "'");
        $config["jiezhi_linshi_start"] = isset($mingtian["val"]) ? $mingtian["val"] : "";
        $config["jiezhi_linshi_end"] = isset($mingtian["val2"]) ? $mingtian["val2"] : "";
        $jintian_jiezhi_guid = $this->config->item("dingcan_jintian_linshi");
        $jintian_jiezhi = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $jintian_jiezhi_guid . "'");
        $config["jiezhi_linshi_jintian_zaocan"] = isset($jintian_jiezhi["val"]) ? $jintian_jiezhi["val"] : "";
        $config["jiezhi_linshi_jintian_wucan"] = isset($jintian_jiezhi["val2"]) ? $jintian_jiezhi["val2"] : "";
        $gucan_jin_ming_jiezhi_guid = $this->config->item("dingcan_mingtian_quxiao_gucan");
        $gucan_jin_ming_jiezhi = $this->m_common->query_one("select val from aiw_dd where guid='" . $gucan_jin_ming_jiezhi_guid . "'");
        $config["jiezhi_gucan_end"] = isset($gucan_jin_ming_jiezhi["val"]) ? $gucan_jin_ming_jiezhi["val"] : "";
        $jintian_zhuanchu_guid = $this->config->item("dingcan_zhuanchu_jieshou");
        $jintian_zhuanchu = $this->m_common->query_one("select val,val2 from aiw_dd where guid='" . $jintian_zhuanchu_guid . "'");
        $config["jintian_zhuanchu_zaocan"] = isset($jintian_zhuanchu["val"]) ? $jintian_zhuanchu["val"] : "";
        $config["jintian_zhuanchu_wucan"] = isset($jintian_zhuanchu["val2"]) ? $jintian_zhuanchu["val2"] : "";

        $dingcan_linshi_amount_guid = $this->config->item("dingcan_linshi_amount");
        $dingcan_linshi_amount = $this->m_common->query_one("select val,val2,val3 from aiw_dd where guid='" . $dingcan_linshi_amount_guid . "'");
        $config["linshi_amount"] = isset($dingcan_linshi_amount["val"]) ? $dingcan_linshi_amount["val"] : "";
        $config["linshi_amount_zaocan"] = isset($dingcan_linshi_amount["val2"]) ? $dingcan_linshi_amount["val2"] : "";
        $config["linshi_amount_wucan"] = isset($dingcan_linshi_amount["val3"]) ? $dingcan_linshi_amount["val3"] : "";


        $dingcan_linshi_btn_guid = $this->config->item("dingcan_linshi_btn");
        $dingcan_linshi_btn = $this->m_common->query_one("select val,val2,val3 from aiw_dd where guid='" . $dingcan_linshi_btn_guid . "'");
        $config["linshi_btn"] = isset($dingcan_linshi_btn["val"]) ? $dingcan_linshi_btn["val"] : "0";
        $config["can_flag"] = isset($dingcan_linshi_btn["val2"]) ? $dingcan_linshi_btn["val2"] : "0";

        if ($config["jiezhi_linshi_start"] == "") {
            $config["err"] = $config["jiezhi_linshi_text"] . "开始时间没有设置";
        }
        if ($config["jiezhi_linshi_end"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_text"] . "结束时间没有设置";
        }
        if ($config["jiezhi_linshi_jintian_zaocan"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_jintian_text"] . "早餐无设置";
        }
        if ($config["jiezhi_linshi_jintian_wucan"] == "") {
            $config["err"] = " " . $config["jiezhi_linshi_jintian_text"] . "午餐无设置";
        }
        if ($config["jiezhi_gucan_end"] == "") {
            $config["err"] = " " . $config["jiezhi_gucan_text"] . "取消截止时间无设置";
        }
        if ($config["jintian_zhuanchu_zaocan"] == "") {
            $config["err"] = " " . $config["jiezhi_zhuanchu_text"] . "早餐转出或接取截止时间无设置";
        }
        if ($config["jintian_zhuanchu_wucan"] == "") {
            $config["err"] = " " . $config["jiezhi_zhuanchu_text"] . "午餐转出或接取截止时间无设置";
        }
        if ($config["linshi_amount"] == "") {
            $config["err"] = " " . $config["linshi_amount_text"] . "无设置";
        }
        if ($config["linshi_amount_zaocan"] == "") {
            $config["err"] = " " . $config["linshi_amount_zaocan_text"] . "无设置";
        }
        if ($config["linshi_amount_wucan"] == "") {
            $config["err"] = " " . $config["linshi_amount_wucan_text"] . "无设置";
        }
        return $config;
    }
}
#endregion

