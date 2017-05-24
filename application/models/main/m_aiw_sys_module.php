<?php

class m_aiw_sys_module extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 取出当前GUID的下级所有记录+
     * @param $pid
     * @param string $sort
     * @param bool $onelevel TRUE只取一层，FALSE向下取所有层
     * @return array
     */
    function get_list_pid($pid,$onelevel=false,$sort="sort desc"){

        if($pid==""){
            $sql = "select * from aiw_dd where isdel='0' 
            and (isnull(parent_guid) or parent_guid='') ";
            $sql .= " order by " . $sort;
        }
        else {
            if (!$onelevel) {
                $sql = "select * from aiw_dd where isdel='0' 
            and concat(',',parent_path,',') like '%," . $pid . ",%'";
                $sql .= " order by " . $sort;
            } else {
                $sql = "select * from aiw_dd where isdel='0' 
            and parent_guid='" . $pid . "'";
                $sql .= " order by " . $sort;
/*
                $tmp = file_get_contents("./aa.txt");
                $tmp.= "\n".$sql;
                file_put_contents("./aa.txt",$tmp);
*/
            }
        }
        return $this->querylist($sql);
    }

    function get_model_by_id($id){
            return $this->get($id,false);
    }


    /**
     * 通过ID返回GUID
     * @param $id
     * @return string
     */
    function get_guid_by_id($id){
        $sql = "select guid from aiw_sys_module where lower(id)='".strtolower($id)."'";
        $model = $this->query_one($sql);
        $guid = "";
        if(isset($model["guid"])){
            $guid = $model["guid"];
        }
        return $guid;
    }

    /**
     * 通过GUID返回ID
     * @param $guid
     * @return string
     */
    function get_id_by_guid($guid){
        $sql = "select id from aiw_sys_module where lower(guid)='".strtolower($guid)."'";
        $model = $this->query_one($sql);
        $id = "";
        if(isset($model["id"])){
            $id = $model["id"];
        }
        return $id;
    }

    function create_id(){
        //生成一个不重复的ID
        $rnd = mt_rand(100000,999999);
        $guid = $this->get_guid_by_id($rnd);
        while($guid!=""){
            $rnd = mt_rand(100000,999999);
            $guid = $this->get_guid_by_id($rnd);
        }
        return $rnd;
    }







}