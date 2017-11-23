<?php

class m_dingcan_excel extends m_common
{
    function __construct()
    {
        parent::__construct();
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
            $orderby = " order by " . $orderby . " ";
        }
        $table1 = " (select t1.* from (select * from (SELECT guid,realname,tel,if(IFNULL(card_no,'')='',RAND(),card_no) as card_no,'系统' as '来源' from aiw_sys_user where isdel='0' UNION  select guid,realname,'无' as tel,card_no,'外部系统' as '来源' from dingcan_excel) t
group by card_no ) t1 left join aiw_company_user_link t2 on t1.guid=t2.user_guid ) usertable";

        $table1 = " aiw_sys_user usertable";

        $table2 = "(
SELECT
		IF(IFNULL(t2.card_no,'')='',RAND(),t2.card_no) as card_no_v2,
		t1.guid,
		t1.dingcan_user,
		t1.dingcan_riqi,
		t1.dingcan_type,
		t1.dingcan_status,
		'1' AS 'laiyuan',
		'0' AS 'laiyuan_other',
		'-1' as 'dengji'
	FROM
		dingcan_list t1
	LEFT JOIN aiw_sys_user t2 ON t1.dingcan_user = t2.guid
	WHERE
		t1.dingcan_status = 0
	OR t1.dingcan_status = 20
	UNION ALL
		SELECT
			IF(IFNULL(card_no,'')='',RAND(),card_no) as card_no_v2,
			guid,
			guid AS dingcan_user,
			use_time AS dingcan_riqi,

		IF (
			DATE_FORMAT(use_time, '%H') <= 9,
			'5f81d52e-a48b-dc1f-f8a2-dca543c55463',
			'3d99f59f-3b6b-965b-2065-3423326dc20d'
		) AS dingcan_type,
		'0' AS dingcan_status,
		'0' AS 'laiyuan',
		'1' AS 'laiyuan_other',
		dingcan_dengji as dengji
	FROM
		dingcan_excel ) 
  dingcan";


        $sql_count = "SELECT usertable.guid FROM " . $table1 . " right join " . $table2 . " on usertable.card_no=dingcan.card_no_v2  $where group by card_no_v2,to_days(dingcan_riqi)";
        //echo $sql_count;exit();
        $list = $this->querylist($sql_count);
        $total = count($list);// $this->query_count($sql_count);
        $page_string = "";//$this->common_page->page_string2($total, $pagesize, $page);
        $sql = "SELECT 
 	usertable.realname,
	usertable.tel,
	dingcan.card_no_v2,
	dingcan.dingcan_user,
	dingcan.dingcan_riqi,
	dingcan.dingcan_type,
	dingcan.dengji,
	sum(dingcan.laiyuan) AS laiyuan,
	sum(dingcan.laiyuan_other) AS laiyuan_other
	
 FROM " . $table1 . " right join " . $table2 . " on usertable.card_no=dingcan.card_no_v2  {$where}  group by  card_no_v2,to_days(dingcan_riqi)";
        //统计份数
        $tongji_list = $this->querylist($sql);
        $zc_amount = 0;
        $zc_amount_other = 0;
        $wc_amount = 0;
        $wc_amount_other = 0;
        foreach ($tongji_list as $v) {

            if ("5f81d52e-a48b-dc1f-f8a2-dca543c55463" == $v["dingcan_type"]) {
                //早餐
                $zc_amount += $v["laiyuan"];
                $zc_amount_other += $v["laiyuan_other"];
            } else {
                //午餐
                $wc_amount += $v["laiyuan"];
                $wc_amount_other += $v["laiyuan_other"];
            }
        }

        $sql .= "  {$orderby} limit {$limit} ";
        //echo $sql;exit();
        $list = $this->querylist($sql);


        //$company_list = $this->querylist("select t1.name,t1.fullname,t2.user_guid,t3.dengji from aiw_company t1 left join aiw_company_user_link t2 on t1.guid=t2.company_guid left join dingcan_company_attr t3 on t1.guid=t3.company_guid");
        //$dengji_list =  $this->querylist("select guid,title from aiw_dd where parent_guid='20a1f321-c55c-fc81-e03e-a4ae0d697b7a'");
        foreach ($list as $k => $v) {
            //$list[$k]["dingcan_status_name"] = isset($dingcan_status[$v["dingcan_status"]])?$dingcan_status[$v["dingcan_status"]]:"";
            if ($this->config->item("dingcan_zaocan_guid") == $v["dingcan_type"]) {
                $list[$k]["dingcan_type_name"] = "早餐";
            } else {
                $list[$k]["dingcan_type_name"] = "午餐";
            }
            $list[$k]["zc_amount"] = $zc_amount;
            $list[$k]["wc_amount"] = $wc_amount;
            $list[$k]["zc_amount_other"] = $zc_amount_other;
            $list[$k]["wc_amount_other"] = $wc_amount_other;
        }


        $data = array(
            "pager" => "",
            "total" => $total,
            "list" => $list,
            "zc_amount" => $zc_amount,
            "wc_amount" => $wc_amount
        );
        return $data;
    }
}