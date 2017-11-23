<?php

/**
 * 已继承基本单表增删查改
 */
class m_aiw_sys_user extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 返回实体或false
     * @param $key
     * @param $pwd
     * @return bool
     */
    function dologin($key,$pwd){
        $where = "isdel='0' and check_status='10' and (username='".$key."' or email='".$key."' or tel='".$key."') and pwd=md5('".$pwd."')";
        $list = $this->get_list($where);
        if(count($list)==0){
            return false;
        }
        else{
            $model = $list[0];
            return $model;
        }

    }


    /**
     * 通用分页
     * @param int $pageindex
     * @param int $pagesize
     * @param string $where
     * @param string $orderby
     * @return array
     */
    public function get_list_pager($pageindex = 1, $pagesize = 10, $where = "", $orderby = "")
    {
        //$this->load->library("common_page");
        $page = $pageindex;//$this->input->get_post("per_page");
        if ($page <= 0) {
            $page = 1;
        }
        $limit = ($page - 1) * $pagesize;
        $limit .= ",{$pagesize}";
        if ($where != "") {
            $where = ' where ' . $where;
        }
        if ($orderby != "") {
            $orderby = " order by t1.check_status asc ," . $orderby . " ";
        }
        $table = " aiw_sys_user t1 left join aiw_company_user_link t2 on t1.guid=t2.user_guid left join aiw_company t3 on t2.company_guid=t3.guid ";
        $sql_count = "SELECT t1.guid FROM ".$table.$where." group by  t2.user_guid";
        //echo $sql_count;
        //$total = $this->query_count($sql_count);
        $total = $this->querylist($sql_count);
        $total = count($total);
        $page_string = "";//$this->common_page->page_string2($total, $pagesize, $page);
        $sql = "SELECT t1.*,group_concat(cast(t3.guid as char)) as company_list FROM $table {$where}  group by  t1.guid {$orderby} limit {$limit}";
        //echo $sql;
        $list = $this->querylist($sql);
        foreach($list as $k=>$v){
            if($list[$k]["company_list"]!=""){
                $arr = explode(",",$list[$k]["company_list"]);
                $company_name = "";
                foreach($arr as $vv){
                    $com_model = $this->query_one("select name from aiw_company where guid='".$vv."'");

                    if(isset($com_model["name"])){
                        if($company_name==""){
                            $company_name = $com_model["name"];
                        }
                        else{
                            $company_name .=",".$com_model["name"];
                        }
                    }
                }
                $list[$k]["company_name"] = $company_name;
            }
        }
        $data = array(
            "pager" => "",
            "total"=>$total,
            "list" => $list
        );
        return $data;
    }

}