<?php

class m_dingcan_list extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 查出某天的订餐记录
     * @param $userguid
     * @param $date
     */
    function dingcan_list($userguid, $date,$status='-1')
    {
        $dingcan_status = $this->get_dingcan_status(false);
        $where = "";
        $orderby = "";
        if($status!="-1"){
            $where = " and t1.dingcan_status='".$status."'";
            $orderby = "t1.dingcan_zhuanchu_time asc";//按转出时间来排队
        }
        $sql = "select t1.*,t2.title as dingcan_type_name from dingcan_list t1 left join aiw_dd t2 
        on t1.dingcan_type=t2.guid where t1.dingcan_user='" . $userguid . "' and t1.dingcan_riqi='" . $date . "' ".$where." ".$orderby;
        //echo $sql;
        $list = $this->querylist($sql);
        foreach($list as $k=>$v){
            $list[$k]["dingcan_status_name"] = isset($dingcan_status[$v["dingcan_status"]])?$dingcan_status[$v["dingcan_status"]]:"";
            switch ($v["dingcan_type"]){
                case $this->config->item("dingcan_zaocan_guid"):
                    $list[$k]["dingcan_type_name"] = "早餐";
                    break;
                case $this->config->item("dingcan_wucan_guid"):
                    $list[$k]["dingcan_type_name"] = "午餐";
                    break;
                default:
                    $list[$k]["dingcan_type_name"] = "-";
                    break;
            }
        }
        //echo $sql;
        return $list;
    }

    /**
     * 查看某天转出中订餐
     * @param $date
     * @param string $status
     * @return array
     */
    function dingcan_zhuanchu_list($date,$status='-1')
    {
        $dingcan_status = $this->get_dingcan_status(false);
        $where = "";
        $orderby = "";
        if($status!="-1"){
            $where = " and t1.dingcan_status='".$status."'";
            $orderby = " order by t1.dingcan_zhuanchu_time asc";//按转出时间来排队
        }
        $sql = "select t1.*,t2.title as dingcan_type_name,t3.username,t3.realname,t3.tel,t5.name as company_name from dingcan_list t1 left join aiw_dd t2 
        on t1.dingcan_type=t2.guid left join aiw_sys_user t3 on t1.dingcan_user=t3.guid
         left join aiw_company_user_link t4 on t1.dingcan_user=t4.user_guid left join aiw_company t5 on t4.company_guid=t5.guid
         where t1.dingcan_riqi='" . $date . "' ".$where." ".$orderby;
        //echo $sql;
        $list = $this->querylist($sql);
        foreach($list as $k=>$v){
            $list[$k]["dingcan_status_name"] = isset($dingcan_status[$v["dingcan_status"]])?$dingcan_status[$v["dingcan_status"]]:"";
            switch ($v["dingcan_type"]){
                case $this->config->item("dingcan_zaocan_guid"):
                    $list[$k]["dingcan_type_name"] = "早餐";
                    break;
                case $this->config->item("dingcan_wucan_guid"):
                    $list[$k]["dingcan_type_name"] = "午餐";
                    break;
                default:
                    $list[$k]["dingcan_type_name"] = "-";
                    break;
            }
            if($v["realname"]==""){
                $list[$k]["realname"] = $v["username"];
            }
        }
        //echo $sql;
        return $list;
    }


    function dingcan_list_groupby($userguid, $date)
    {
        $dingcan_status = $this->get_dingcan_status(false);
        $sql = "select t1.*,t2.title as dingcan_type_name,count(t1.id) as can_amount from dingcan_list t1 left join aiw_dd t2 
        on t1.dingcan_type=t2.guid where t1.dingcan_status<>'99' and t1.dingcan_user='" . $userguid . "' and t1.dingcan_riqi='" . $date . "' group by t1.dingcan_riqi,t1.dingcan_type";
        $list = $this->querylist($sql);
        foreach($list as $k=>$v){
            $list[$k]["dingcan_status_name"] = isset($dingcan_status[$v["dingcan_status"]])?$dingcan_status[$v["dingcan_status"]]:"";
            switch ($v["dingcan_type"]){
                case $this->config->item("dingcan_zaocan_guid"):
                    $list[$k]["dingcan_type_name"] = "早餐";
                    break;
                case $this->config->item("dingcan_wucan_guid"):
                    $list[$k]["dingcan_type_name"] = "午餐";
                    break;
                default:
                    $list[$k]["dingcan_type_name"] = "-";
                    break;
            }
        }
        //echo $sql;
        return $list;
    }



    /**
     * 临时订餐截止时间
     */
    function dingcan_linshi_jiezhi()
    {

    }

    /**
     * @param $riqi 日期
     * @param $can_type 早餐或午餐的GUID
     */
    function dingcan_amount($riqi, $can_type)
    {
        $sql = "select count(1) as dd from dingcan_list where dingcan_riqi='" . $riqi . "' and dingcan_type='" . $can_type . "' and is_tmp='1' and dingcan_status<>99 ";
        $model = $this->m_common->query_one($sql);
        return $model["dd"];
    }

    /**
     * 查看某状态下的订餐数量
     * @param $riqi 日期
     * @param $can_type 早餐或午餐的GUID
     * @param $dingcan_status
     * @return mixed
     */
    function dingcan_amount_by_status($riqi, $can_type,$dingcan_status)
    {
        $sql = "select count(1) as dd from dingcan_list where dingcan_status='".$dingcan_status."' and dingcan_riqi='" . $riqi . "' and dingcan_type='" . $can_type . "' and is_tmp='1' and dingcan_status<>99 ";
        $model = $this->m_common->query_one($sql);
        return $model["dd"];
    }

    /**
     * 查看某状态某用户的临时订餐数量(含转入的)
     * @param $riqi
     * @param $can_type
     * @param $dingcan_status
     * @param $user_guid
     * @return mixed
     */
    function dingcan_amount_by_status_user($riqi, $can_type,$dingcan_status,$user_guid)
    {
        $sql = "select count(1) as dd from dingcan_list where 
                    (dingcan_user='".$user_guid."')
                     and dingcan_status='".$dingcan_status."' and dingcan_riqi='" . $riqi . "' and dingcan_type='" . $can_type . "' and is_tmp='1' and dingcan_status<>99 ";
        $model = $this->m_common->query_one($sql);
        return $model["dd"];
    }


    /**
     * 返回某用户某天的订餐数
     * @param $riqi
     * @param $can_type
     * @param $user_guid
     * @return mixed
     */
    function dingcan_amount_by_user($riqi, $can_type,$user_guid)
    {
        $sql = "select count(1) as dd from dingcan_list where dingcan_user='".$user_guid."' and dingcan_riqi='" . $riqi . "' and dingcan_type='" . $can_type . "' and is_tmp='1' and dingcan_status<>99 ";
        $model = $this->m_common->query_one($sql);
        return $model["dd"];
    }
    public function get_dingcan_status($iscolor=false){
        //这状态上边列表，还有一个要改
        if(!$iscolor){
            $dingcan_status = array(
                "0" => "正常就餐",
                "10" => "转出中",
                "20" => "已转入",
                "30" => "转出成功",
                "40" => "转出失败",
                "99" => "取消"
            );
        }
        else {
            $dingcan_status = array(
                "0" => "正常就餐",
                "10" => "<span style='color:blue'>转出中</span>",
                "20" => "<span style='color:darkgreen'>已转入</span>",
                "30" => "<span style='color:royalblue'>转出成功</span>",
                "40" => "<span style='color:red'>转出失败</span>",
                "99" => "<span style='color:mediumvioletred'>取消</span>"
            );
        }
        return $dingcan_status;
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
        $dingcan_status = $this->get_dingcan_status(true);
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
        $tablename=" dingcan_list";
        $sql_count = "SELECT COUNT(*) AS tt FROM " . $tablename . $where;
        //echo $sql_count;
        $total = $this->query_count($sql_count);
        $page_string = "";//$this->common_page->page_string2($total, $pagesize, $page);
        $sql = "SELECT * FROM {$tablename} {$where}  {$orderby} limit {$limit}";
        //echo $sql;
        $list = $this->querylist($sql);
        $company_list = $this->querylist("select t1.name,t1.fullname,t2.user_guid,t3.dengji from aiw_company t1 left join aiw_company_user_link t2 on t1.guid=t2.company_guid left join dingcan_company_attr t3 on t1.guid=t3.company_guid");
        $dengji_list =  $this->querylist("select guid,title from aiw_dd where parent_guid='20a1f321-c55c-fc81-e03e-a4ae0d697b7a'");
        foreach($list as $k=>$v){
            $list[$k]["dingcan_status_name"] = isset($dingcan_status[$v["dingcan_status"]])?$dingcan_status[$v["dingcan_status"]]:"";
            switch ($v["dingcan_type"]){
                case $this->config->item("dingcan_zaocan_guid"):
                    $list[$k]["dingcan_type_name"] = "早餐";
                    break;
                case $this->config->item("dingcan_wucan_guid"):
                    $list[$k]["dingcan_type_name"] = "午餐";
                    break;
                default:
                    $list[$k]["dingcan_type_name"] = "-";
                    break;
            }
            $list[$k]["company_name"] = "-";
            $list[$k]["dengji_name"] = "-";
            foreach ($company_list as $com_row){
                if($com_row["user_guid"]==$v["dingcan_user"]){
                    $list[$k]["company_name"] = $com_row["name"];
                    foreach ($dengji_list as $row){
                        if($com_row["dengji"]==$row["guid"]){
                            $list[$k]["dengji_name"] = $row["title"];
                            break;
                        }
                    }
                    break;
                }
            }
            if($v["is_tmp"]=='1'){
                $list[$k]["is_tmp_name"] = "临时订餐";
            }
            else{
                $list[$k]["is_tmp_name"] = "固定订餐";
            }

            $list[$k]["realname"] = "";
            $list[$k]["tel"] = "";
            $usermodel = $this->query_one("select realname,tel,username from aiw_sys_user where guid='".$v["dingcan_user"]."'");
            if(isset($usermodel["realname"]) || isset($usermodel["username"])){
                $list[$k]["realname"] = $usermodel["realname"]==""?$usermodel["username"]:$usermodel["realname"];
                $list[$k]["tel"] = $usermodel["tel"];
            }
        }
        foreach($list as $k=>$v){
            $list[$k]["dingcan_status_name"] = "-";
            if(isset($dingcan_status[$v["dingcan_status"]])){
                $list[$k]["dingcan_status_name"] = $dingcan_status[$v["dingcan_status"]];
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