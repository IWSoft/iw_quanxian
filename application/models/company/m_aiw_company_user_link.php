<?php

class m_aiw_company_user_link extends m_common
{
    function __construct()
    {
        parent::__construct();
    }


    /**
     * 返回userguid选中的所有单位
     * @param $userguid
     */
    function get_by_user_guid($userguid){
        $sql = "select * from aiw_company_user_link where user_guid='".$userguid."'";
        $list = $this->querylist($sql);
        return $list;
    }

    /**
     * 返回一个实体
     * @param $userguid
     */
    function get_model_by_user_guid($userguid){
        return $this->query_one("select * from aiw_company_user_link where user_guid='".$userguid."'");
    }
}