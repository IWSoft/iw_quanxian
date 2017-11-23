<?php

class m_aiw_company extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 定制分页
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
            $orderby = " order by " . $orderby . " ";
        }
        $table = "aiw_company t1 left join dingcan_company_attr t2 on t1.guid=t2.company_guid";
        $sql_count = "SELECT COUNT(*) AS tt FROM ".$table.$where;
        //echo $sql_count;
        $total = $this->query_count($sql_count);
        $page_string = "";//$this->common_page->page_string2($total, $pagesize, $page);
        $sql = "SELECT t1.guid,t1.id,t1.name,t1.fullname,t1.sortnum,t2.dengji,t2.dingcan_start,t2.dingcan_end,t2.quanxian,t2.week_0,t2.week_1,t2.week_2,t2.week_3,t2.week_4,t2.week_5,t2.week_6 FROM {$table} {$where}  {$orderby} limit {$limit}";
        //echo $sql;
        $list = $this->querylist($sql);
        $dengji_list = $this->get_dengji();
        $quanxian_list = $this->get_quanxian();
        foreach($list as $k=>$v){
            $list[$k]["youxiaoqi"] = date("Y-m-d",$v["dingcan_start"])."~".date("Y-m-d",$v["dingcan_end"]);
            $list[$k]["quanxian_name"] = $this->sel_quanxian_name($v["quanxian"],$quanxian_list);
            $list[$k]["dengji_name"] = $this->sel_dengji_name($v["dengji"],$dengji_list);
        }
        $data = array(
            "pager" => "",
            "total"=>$total,
            "list" => $list
        );
        return $data;
    }

    /**
     * 根据值返回名
     * @param $guid
     * @param $dnegji_list
     */
    private function sel_quanxian_name($guid,$quanxian_list){
        $arr = explode(",",$guid);
        foreach ($quanxian_list as $v){
            if(in_array($v["guid"],$arr)) {
                return $v["title"];
            }
        }
        return "";
    }


    /**
     * @param $guid
     * @param $dengji_list
     * @return string
     */
    private function sel_dengji_name($guid,$dengji_list){
        foreach ($dengji_list as $v){
            if($v["guid"]==$guid) {
                return $v["title"];
            }
        }
        return "";
    }

    public function get_dengji(){
        return $this->querylist("select * from aiw_dd where isdel='0' and parent_guid='20a1f321-c55c-fc81-e03e-a4ae0d697b7a'");
    }

    public function get_quanxian(){
        return $this->querylist("select * from aiw_dd where isdel='0' and parent_guid='da6cdafa-4fe4-4a40-b369-e062e72f8fc2'");
    }

}