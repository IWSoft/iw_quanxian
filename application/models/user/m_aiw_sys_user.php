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
        $where = "(username='".$key."' or email='".$key."' or tel='".$key."') and pwd=md5('".$pwd."')";
        $list = $this->get_list($where);
        if(count($list)==0){
            return false;
        }
        else{
            $model = $list[0];
            return $model;
        }

    }
}