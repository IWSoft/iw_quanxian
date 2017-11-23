<?php
/**
 * Created by PhpStorm.
 * User: 战神UPC
 * Date: 2017/10/24
 * Time: 22:20
 */

class Category extends MY_AdminController
{
    public $categorylist = array();
    public $categorylist2 = array();
    public $upload_path = '';
    public $upload_path_save = '';
    public $isadmin = false;

    private $admin_is_add;
    private $admin_is_del;

    function __construct()
    {
        parent::__construct();
        $this->load->library("lib_website");
        $this->load->library("lib_pin");
        $this->load->model('user/m_aiw_dd');
        $this->load->model("website/m_aiw_website_category","wc");
        $this->load->model("website/m_aiw_website_model","wcm");
        $this->upload_path = $this->lib_website->get_upload_path();

        /*
        $this->upload_path_save = "data/upload/info/" . date("Y") . "/";
        $this->load->model('M_common');
        $this->load->model('M_website_common_model', 'wcm');
        $this->load->model('M_website_common_info', 'wcmi');
        $this->load->model('M_website_category', 'wc');
        $this->load->model('M_website_model_zuixindongtai_dyart', 'dyart');

        $this->load->model('m_hmt_yuyue', 'pxyuyue');
        $this->load->model('m_website_common_info_comment', 'cic');


        $this->load->library('MyText');
        $this->load->library('MyEditor');
        $this->load->library('MyAlbum');
        $this->load->library('MyRadio');
        $this->load->library('MyCheckbox');
        $this->load->library('MyDatebox');
        $this->load->library("pin");
        $this->isadmin = is_super_admin();
        $this->admin_is_add = $this->permition_for("website_category", "addinfo");
        $this->admin_is_del = $this->permition_for("website_category", "delinfo");
*/
    }

    /**
     * 取得所有栏目
     * @param $pid
     * @param $tree
     * @param string $add_id 将指定的栏目过滤出来 用于实现管理指定栏目及信息
     * @return array
     */
    private function GetCategory($pid,$tree,$add_id=""){
        global $categorylist,$categorylist2;

        if($add_id!=""){

            $categorylist = $this->wc->GetList("id in(".$add_id.")","id asc");
            //PID找不到对应记录，就设为0
            foreach($categorylist as $k=>$v){
                $pass = false;
                foreach($categorylist as $kk=>$vv){
                    if($vv["id"]==$v["pid"]){
                        $pass = true;
                        break;
                    }
                }
                if(!$pass){
                    $categorylist[$k]["pid"] = "0";
                }
                //$categorylist[$k]["tree"] = "├" . $tree;
            }
            //得新遍历一次，得出层次关系
            $categorylist2 = array();
            $this->Digui_shuzu("0","");
            $categorylist = $categorylist2;
            //print_r($categorylist);
        }
        else {
            $model = $this->wc->get_list("pid=".$pid);
            foreach ($model as $v) {
                $v["tree"] = "&nbsp;&nbsp;".$tree;
                $categorylist[] = $v;
                $this->GetCategory($v["id"], $v["tree"]);
            }
        }

        return $categorylist;
    }






    //批量添加
    function add(){
        $data = array();

        $post = $this->input->post();
        $data = array();
        if(count($post)>0){
            //批量保存
            $arr = isset($post["form_title"])?$post["form_title"]:array();
            if(is_array($arr)) {
                foreach ($arr as $v) {
                    if (trim($v) != "") {
                        $addr = $this->lib_pin->Pinyin($v, 'UTF8');
                        while ($this->wc->GetAddrCount(0, $addr) > 0) {
                            $addr .= "_";
                        }
                        $parent_path = "0";
                        if ($post["pid"] > 0) {
                            $parent_model = $this->wc->GetModel($post["pid"]);
                            $parent_path = $parent_model["parent_path"] . "," . $parent_model["id"];
                        }
                        $model["title"] = $v;
                        $model["addr"] = $addr;
                        $model["isshow"] = "1";
                        $model["model_id"] = empty($post["model_id"]) ? 0 : $post["model_id"];
                        $model["pid"] = $post["pid"];
                        $model["orderby"] = 50;
                        $model["content"] = $post["content"];
                        $model["beizhu"] = $post["beizhu"];
                        $model["parent_path"] = $parent_path;


                        $result = $this->wc->Insert($model);
                        write_action_log(
                            $result['sql'],
                            $this->uri->uri_string(),
                            login_name(),
                            get_client_ip(),
                            1,
                            "批量添加栏目：" . $v . "生成ID:" . $result['insert_id']);
                    }
                }
            }
            echo "<script>
					parent.tip_show('添加成功',1,1000);
					//top.topManager.closePage();
					setTimeout(\"window.location.href='".site_url("website_category/add")."'\",1000);
				 </script>";
            die();
        }
        else{
            //读出模型
            $modellist = $this->wcm->get_list("pid=0");
            $data["modellist"] = $modellist;
            global $categorylist;
            $this->GetCategory(0,'└');
            $data["categorylist"] = $categorylist;
        }


        $this->load->view(__ADMIN_TEMPLATE__ . "/website/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function index()
    {
        $data = array();
        helper_include_css($data, array(
            "bootstrap-table/bootstrap-table.min.css",
            "bootstrap-table/bootstrap-table.my.css",
            "iCheck/custom.css"
        ));
        helper_include_js($data, array(
            "bootstrap-table/bootstrap-table.min.js",
            "bootstrap-table/bootstrap-table-mobile.min.js",
            "bootstrap-table/locale/bootstrap-table-zh-CN.min.js",
            "iCheck/icheck.min.js"
        ));

        $data["pagesize"] = $this->config->item("def_pagesize");
        $data["form_btn"] = $this->admin_get_form_btn();
        $data["form_list_btn"] = $this->admin_get_list_btn();
        $this->load->view(__ADMIN_TEMPLATE__ . "/website/" . strtolower(__CLASS__ . "/" . __FUNCTION__), $data);
    }

    function ajax(){
        //pageSize, pageNumber, searchText, sortName, sortOrder
        $get = $this->input->get();
        $pagesize = isset($get["pageSize"]) ? $get["pageSize"] : $this->config->item("def_pagesize");
        $pageindex = isset($get["pageNumber"]) ? $get["pageNumber"] : 1;
        $searchText = isset($get["searchText"]) ? $get["searchText"] : "";

        $sortName = isset($get["sortName"]) ? $get["sortName"] : "id";
        $sortOrder = isset($get["sortOrder"]) ? $get["sortOrder"] : "asc";

        $key = isset($get["searchText"]) ? $get["searchText"] : "";
        $pid = isset($get["pid"]) ? $get["pid"] : "0";
        $where = " (pid=".$pid.") ";
        if ($key != "") {
            $where .= " and (title like '%".$key."%' or t1.fulltitle like '%" . $key . "%' ) ";
        }
        $model = $this->wc->get_list_pager($pageindex, $pagesize, $where, $sortName." ".$sortOrder);
        $tmplist = $model["list"];
        foreach($tmplist as $k=>$v){
            $arr = explode(",",$v["parent_path"]);
            if(count($arr)>0 && $v["parent_path"]!="") {
                $tmplist[$k]["tree_flag"] = str_repeat("&nbsp;└", count($arr));
            }
            else{
                $tmplist[$k]["tree_flag"] = "";
            }
        }
        $list["rows"] = $tmplist;
        $list["total"] = $model["total"];
        helper_get_json_header();
        exit(json_encode($list));
    }

}