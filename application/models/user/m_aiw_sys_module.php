<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/6
 * Time: 12:03
 */
class m_aiw_sys_module extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     *取出有权限的模块或方法
     * @param $user_guid
     * @return array|mixed|string 返回数组
     */
    function get_modules($user_guid)
    {
        $sql = "select t1.* from aiw_sys_role t1 left join aiw_sys_user_role t2 on t1.guid=t2.role_guid
          where t2.user_guid='" . $user_guid . "'  ";
        //echo $sql;
        $list = $this->querylist($sql);
        $modules = "";
        foreach ($list as $k => $v) {
            $modules .= $v["modules"] . " ";
        }


        //将" "替换成 , 号
        $modules = str_replace(" ", ",", trim($modules));
        $modules = explode(",", $modules);
        $modules = array_unique($modules);
        if ($modules[0] == "") {
            return array();
        }

        return $modules;
    }

    /**
     * 以列表方式返回
     * @param $user_guid
     * @param int $module_type 0:不限制 10：菜单 20：按钮 30：字段
     */
    function get_module_list($user_guid, $module_type = 0)
    {
        $modules = $this->get_modules($user_guid);
        $list = array();
        if (count($modules) > 0) {
            $sql = "select * from aiw_sys_module where isdel='0' and guid in('" . implode("','", $modules) . "') " . ($module_type > 0 ? " and module_type='$module_type'" : "") . " order by sort asc,createdate asc";
            $list = $this->querylist($sql);
        }
        return $list;
    }

    /**
     * 根据用户权限，读出当前模块下的有权操作的按钮
     * @param $user_guid  用户GUID
     * @param $module_guid 当前模块
     */
    function get_curr_module_btn_list($user_guid, $module_guid)
    {

        $modules = $this->get_modules($user_guid);
        $list = array();
        //20 30 50 内页顶部按钮 表单按钮 列表按钮
        if (count($modules) > 0) {
            $sql = "select * from aiw_sys_module where isdel='0'
                    and parent_guid='" . $module_guid . "'
                    and (module_type='20' or module_type='30' or module_type='50')   
                    order by sort asc,createdate asc";
            //echo $sql;
            $list = $this->querylist($sql);
        }
        $btn = array();
        foreach ($list as $v) {
            if (in_array($v["guid"], $modules)) {
                array_push($btn, $v);
            }
        }

        return $btn;
    }

    function get_all(){
        $sql = "select * from aiw_sys_module where isdel='0'";
        return $this->querylist($sql);
    }


    function get_module_type(){
        //10：菜单 20：顶部按钮 30：表单按钮 40：字段 50：列表按钮 60:方法函数
        return array(
            "10"=>"菜单",
            "20"=>"顶部按钮",
            "30"=>"表单按钮",
            "40"=>"字段",
            "50"=>"列表按钮",
            "60"=>"方法函数"
        );
    }

}