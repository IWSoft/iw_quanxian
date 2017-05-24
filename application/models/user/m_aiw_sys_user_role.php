<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/6
 * Time: 12:02
 */
class m_aiw_sys_user_role extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    function get_roles($user_guid,$orderby="t2.main_sort asc , t2.createdate asc"){
        $sql = "select t1.*,t2.main_sort from aiw_sys_role t1 left join aiw_sys_user_role t2 on t1.guid=t2.role_guid
          where t2.user_guid='".$user_guid."' order by ".$orderby;
        $list = $this->querylist($sql);
        return $list;
    }

}