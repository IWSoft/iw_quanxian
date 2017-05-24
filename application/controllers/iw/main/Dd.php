<?php

class Dd extends MY_AdminController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("user/m_aiw_sys_module");

        //读取语言文件
        $this->lang->load(array('iw_main_dd', 'btn'), $this->config->item("language"));
    }

    function index()
    {
        //$this->output->enable_profiler(TRUE);
        $data = $this->admin_data;
        $data["session"] = $this->admin_get_session();
        $module_list = $this->m_aiw_sys_module->get_module_list($data["session"]["model"]["guid"], "10");
        $data["module_list"] = $module_list;
        //print_r($data["session"]);
        $data["header_include_js"] = array("static/js/plugins/jsTree/jstree.min.js", "static/js/plugins/validate/jquery.validate.min.js", "static/js/plugins/validate/messages_zh.min.js", "static/js/plugins/slimscroll/jquery.slimscroll.min.js");
        $data["header_include_css"] = array("static/css/plugins/jsTree/style.min.css", "static/css/font-awesome.min.css?v=4.4.0");
        //$data["list"] = $this->getnode("");
        $data["icon"] = $this->geticon();
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/dd/index", $data);
    }

    /**
     * 读节点，优先读取网址参数的PID，控制器内调用，通过方法的PID参数来传递
     * @param string $id 父GUID
     * @param boomixed
     */
    function getnode($id = "", $json = false)
    {
        //echo '[{"text":"fff","children":true,"id":"a75ec5d1-470a-f7dc-fb30-a23eb5fb6b7b","icon":"fa fa-folder"}]';
        //exit();
        $this->load->model("user/m_aiw_dd");
        $get = $this->input->get();

        if (isset($get["id"])) {
            $id = $get["id"];
            if ($id == "#" || strtoupper($id)=="N/A") {
                $id = "";
            }
            $json = true;
        }

        $list = $this->m_aiw_dd->get_list_pid($id, true);
        //{"text":"Abhishek","children":true,"id":"Abhishek","icon":"folder"
        $tree = array();
        //{"text":"Abhishek","children":true,"id":"Abhishek","icon":"fa fa-folder"}
        foreach ($list as $k => $v) {
            $tmp["text"] = $v["title"] . "[NO." . $v["id"] . "]";

            //检查有无子级
            $tmp["children"] = $this->m_aiw_dd->get_count("parent_guid='" . $v["guid"] . "' and isdel='0'") > 0;
            $tmp["id"] = $v["guid"];
            $tmp["icon"] = "fa fa-file";
            if ($tmp["children"]) {
                $tmp["icon"] = "fa fa-folder";
            } else {
                $tmp["type"] = "file";
            }
            array_push($tree, $tmp);
        }
        if ($json) {

            helper_get_json_header();
            exit(json_encode($tree));
        } else {
            return $tree;
        }
    }

    /**
     * @param $guid
     */
    function getmodel($guid = "")
    {
        $this->load->model("user/m_aiw_dd");
        $get = $this->input->get();
        if (isset($get["guid"])) {
            $guid = $get["guid"];
        }
        $model = $this->m_aiw_dd->get($guid);
        $data = array("err" => "", "model" => array());
        if (count($model) > 0) {
            $model["parent_id"] = $this->m_aiw_dd->get_id_by_guid($model["parent_guid"]);
            if ($model["parent_id"] == "") {
                $model["parent_id"] = "N/A";
            }
            $data = array("err" => "ok", "model" => $model);
        } else {
            $data = array("err" => lang("err_no_data"), "model" => array());
        }
        header("HTTP/1.0 200 OK");
        //header('Content-type: text/html; charset=utf-8');
        header('Content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Pragma: no-cache");
        exit(json_encode($data));
    }

    function save()
    {
        $this->load->model("user/m_aiw_dd");
        $model = $this->admin_get_model();
        $post = $this->input->post();
        $parent_id = isset($post["parent_id"]) ? $post["parent_id"] : "";
        $parent_id = strtolower($parent_id);
        $model["guid"] = isset($model["guid"]) ? $model["guid"] : "";
        //根据父ID取出父ID的GUID
        if ($parent_id != "" && $parent_id != "n/a") {

            $parent_guid = $this->m_aiw_dd->get_guid_by_id($parent_id);
            $parent_model = $this->m_aiw_dd->get($parent_guid);
            $model["parent_guid"] = $parent_guid;
            if (isset($parent_model["guid"])) {
                if ($parent_model["parent_path"] == "") {
                    $model["parent_path"] = $parent_guid;
                } else {
                    $model["parent_path"] = $parent_model["parent_path"] . "," . $parent_guid;
                }
            } else {
                exit(json_encode(array("err" => "err", "msg" => lang("err_parent_null"))));
            }

        }

        if ($model["guid"] != "") {
            $old_model = $this->m_aiw_dd->get($model["guid"]);
            $model = array_merge($old_model, $model);
            $model["update_date"] = date("Y-m-d H:i:s");
            $model["update_user"] = $this->admin_guid();
            $this->m_aiw_dd->update($model);
            exit(json_encode(array("err" => "ok", "msg" => lang("ok_msg"))));
        } else {
            $model["id"] = $this->m_aiw_dd->create_id();
            $model["create_date"] = date("Y-m-d H:i:s");
            $model["create_user"] = $this->admin_guid();
            $this->m_aiw_dd->add($model);
            exit(json_encode(array("err" => "ok", "msg" => lang("ok_msg_update"))));
        }
    }

    function batch_save()
    {
        $this->load->model("user/m_aiw_dd");
        $post = $this->input->post();
        $field = array("title", "fulltitle", "val", "beizhu", "val2", "beizhu2", "val3", "beizhu3","can_del");
        //file_put_contents("./aa.txt","aaa=".print_r($post,true));
        $i = 0;
        foreach ($post as $k => $v) {

            if (is_array($post["form_" . $field[0]])) {
                if (isset($post["form_" . $field[0]][$i])) {
                    if ($post["form_" . $field[0]][$i] != "") {
                        $model = "";
                        foreach ($field as $k2 => $v2) {
                            if (strtolower($post["parent_id"][$i]) != "n/a" && $post["parent_id"][$i] != "") {
                                //读取父节点的实体
                                $parent_model = $this->m_aiw_dd->get_model_by_id($post["parent_id"][$i]);
                                if (count($parent_model) == 0) {
                                    exit(json_encode(array("err" => "err", "msg" => lang("iw_main_dd_err_parent_null"))));
                                }
                                $model["parent_guid"] = $parent_model["guid"];
                                $model["parent_path"] = $parent_model["parent_path"] . "," . $model["parent_guid"];
                            } else {
                                $model["parent_guid"] = "N/A";
                                $model["parent_path"] = "";
                            }
                            $model["create_date"] = date("Y-m-d H:i:s");
                            $model["create_user"] = $this->admin_guid();
                            $model[$v2] = $post["form_" . $v2][$i];
                        }
                        if (is_array($model)) {
                            $model["id"] = create_id();
                            while (true) {
                                //检查是否重复
                                if ($this->m_aiw_dd->get_count("id='" . $model["id"] . "'") > 0) {
                                    $model["id"] = create_id();
                                } else {
                                    break;
                                }
                            }
                            $model["guid"] = create_guid();
                            $this->m_aiw_dd->add($model);
                        }
                        //file_put_contents("./aa.txt","aaa=".print_r($model,true));
                    }
                }
            }

            $i++;
        }
        exit(json_encode(array("err" => "ok", "msg" => lang("ok_msg_submit"))));
    }

    function add()
    {
        $data = array();
        $data["header_include_css"] = array("static/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");
        $get = $this->input->get();
        if (isset($get["parent_guid"])) {
            $this->load->model("user/m_aiw_dd");
            $data["parent_id"] = $this->m_aiw_dd->get_id_by_guid($get["parent_guid"]);
        } else {
            $data["parent_id"] = "";
        }
        $data["icon"] = $this->geticon();
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/dd/add", $data);
    }

    function edit()
    {
        $data = array();
        $get = $this->input->get();
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        if ($guid == "") {
            helper_err("没有资料");
            exit();
        }
        $data["model"] = $this->m_aiw_dd->get($guid);
        if ($data["model"]["parent_guid"] != "" && strtoupper($data["model"]["parent_guid"]) != "N/A") {
            $data["parent_model"] = $this->m_aiw_dd->get($data["model"]["parent_guid"]);
        }
        $data["icon"] = $this->geticon();//读出可选的图标
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/dd/edit", $data);
    }

    /**
     * 读取图片集，用于选择
     */
    function geticon()
    {
        $this->load->model("user/m_aiw_dd");
        $list = $this->m_aiw_dd->get_list_pid("faa9d9d2-63c1-4b70-a03f-9147761f9cc4");
        return $list;
    }

    /**
     * 普通列表方式
     */
    function list2()
    {
        $data = array();
        helper_include_css($data, array(
            "bootstrap-table/bootstrap-table.min.css",
            "bootstrap-table/bootstrap-table.my.css",
            "awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css",
            "iCheck/custom.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js"
        ));
        $get = $this->input->get();
        $parent_guid = isset($get["guid"]) ? $get["guid"] : "";
        if ($parent_guid != "") {
            $data["model"] = $this->m_aiw_dd->get($parent_guid);
        } else {
            $data["model"] = "";
        }


        $data["pagesize"] = $this->config->item("def_pagesize");
        $data["form_btn"] = $this->admin_get_form_btn();
        $data["form_list_btn"] = $this->admin_get_list_btn();
        $data["parent_guid"] = $parent_guid;
        $this->load->view(__ADMIN_TEMPLATE__ . "/main/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax()
    {
        $this->load->model("user/m_aiw_dd");
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";
        $parent_guid = isset($get["guid"]) ? $get["guid"] : "";
        $module_type = isset($get["mt"]) ? $get["mt"] : "";
        $sortName = isset($get["sortName"]) ? $get["sortName"] : "";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " isdel='0' ";
        if ($key != "") {
            $where .= " and title like '%" . $key . "%'";
        }
        if ($searchText != "") {
            $where .= " and title like '%" . $searchText . "%'";
        }
        if ($module_type != "") {
            $where .= " and module_type='" . $module_type . "'";
        } else {
            //$where .= " and module_type='-1'";
        }
        if ($parent_guid == "") {
            $where .= " and (isnull(parent_guid) or parent_guid='')";
        } else {
            $where .= " and (parent_guid='" . $parent_guid . "')";
        }

        $model = $this->m_aiw_dd->get_list_pager($pageindex, $pagesize, $where, "sort asc");
        $list["rows"] = $model["list"];

        foreach ($list["rows"] as $k => $v) {

            //父名称
            if ($list["rows"][$k]["parent_guid"] == "" || strtolower($list["rows"][$k]["parent_guid"]) == "n/a") {
                $list["rows"][$k]["parent_title"] = "顶级";
            } else {
                $parent_model = $this->m_aiw_dd->get($list["rows"][$k]["parent_guid"]);
                if (isset($parent_model["title"])) {
                    $list["rows"][$k]["parent_title"] = $parent_model["title"];
                } else {
                    $list["rows"][$k]["parent_title"] = "-";
                }
            }
        }
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

    /**
     * 用于普通列表
     */
    function ajax2()
    {
        $this->load->model("user/m_aiw_dd");
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";
        $parent_guid = isset($get["guid"]) ? $get["guid"] : "";
        $module_type = isset($get["mt"]) ? $get["mt"] : "";
        $sortName = isset($get["sortName"]) ? $get["sortName"] : "";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "";

        $key = isset($get["key"]) ? $get["key"] : "";
        $where = " isdel='0' ";
        if ($key != "") {
            $where .= " and (title like '%" . $key . "%' or id='".$searchText."')";
        }
        if ($searchText != "") {
            $where .= " and (title like '%" . $searchText . "%' or id='".$searchText."')";
        }
        if ($module_type != "") {
            $where .= " and module_type='" . $module_type . "'";
        } else {
            //$where .= " and module_type='-1'";
        }
        if ($parent_guid == "") {
            //$where .= " and (isnull(parent_guid) or parent_guid='')";
        } else {
            $where .= " and (parent_guid='" . $parent_guid . "')";
        }


        $model = $this->m_aiw_dd->get_list_pager($pageindex, $pagesize, $where, "sort asc");
        $list["rows"] = $model["list"];

        foreach ($list["rows"] as $k => $v) {

            //父名称
            if ($list["rows"][$k]["parent_guid"] == "" || strtolower($list["rows"][$k]["parent_guid"]) == "n/a") {
                $list["rows"][$k]["parent_title"] = "顶级";
            } else {
                $parent_model = $this->m_aiw_dd->get($list["rows"][$k]["parent_guid"]);
                if (isset($parent_model["title"])) {
                    $list["rows"][$k]["parent_title"] = $parent_model["title"];
                } else {
                    $list["rows"][$k]["parent_title"] = "-";
                }
            }
        }
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

    /**
     * 普通列表 读下级按钮
     */
    function get_level_down()
    {
        $get = $this->input->get();
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        header("location:" . site_url2("list2") . "?guid=" . $guid);
        exit();
    }

    /**
     * 普通列表 读下级按钮
     */
    function get_level_same()
    {
        $get = $this->input->get();
        $guid = isset($get["guid"]) ? $get["guid"] : "";
        $parent_guid = "";
        if ($guid != "") {
            $parent_model = $this->m_aiw_dd->get($guid);
            if (isset($parent_model["parent_guid"])) {
                $parent_guid = $parent_model["parent_guid"];
            }
        }
        header("location:" . site_url2("list2") . "?guid=" . $parent_guid);
        exit();
    }

    function del(){
        $this->load->model("user/m_aiw_dd");
        $post = $this->input->post();
        $json["ok"] = 0;
        $json["msg"] = "";
        if(!isset($post["guid"])){
            $json["msg"] = "没有值";
            exit(json_encode($json));
        }
        $guid = $post["guid"];
        $arr = explode(",",$guid);
        foreach($arr as $v) {
            if($v!="" && strtolower($v)!="f0761c0f98c1") {
                $model = $this->m_aiw_dd->get($v);
                $model["isdel"]='1';
                $model["del_date"]=date("Y-m-d H:i:s");
                $model["del_user"]=$this->admin_guid();
                $this->m_aiw_dd->update($model);
            }
        }
        $json["ok"] = "1";
        $json["msg"] = "删除成功";
        exit(json_encode($json));
    }

}