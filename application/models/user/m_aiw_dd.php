<?php

class m_aiw_dd extends m_common
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
            and (isnull(parent_guid) or parent_guid='' or upper(parent_guid)='N/A') ";
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
        $sql = "select guid from aiw_dd where lower(id)='".strtolower($id)."'";
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
        $sql = "select id from aiw_dd where lower(guid)='".strtolower($guid)."'";
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

    /**
     * 提取按钮下所有图标 读val val2 val3
     * @return array
     */
    function get_btn_icon_list(){
        //$guid = "006d4453-5466-4973-c9c4-89e2d754095b";
        $guid = $this->config->item("def_dd_btn_guid");
        $list = $this->get_list_pid($guid);
        return $list;
    }


    /**
     * 返回微信设置
     */
    function get_weixin_config(){
        $list = $this->get_list("parent_guid='290aa85f-c081-e228-5375-9fd32713e1de'");
        $arr = array("token","encodingaeskey","appid","appsecret");
        $config = array();
        foreach ($list as $k=>$v){
            if(in_array($v["title"],$arr)){
                $config[$v["title"]] = $v["val"];
            }
        }
        return $config;
    }







}