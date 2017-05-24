<?php

/**
 * 已继承基本单表增删查改
 * Class m_com_err_log
 */
class m_aiw_err_log extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    function log($content,$admin_guid="",$admin_username,$line=0,$url=""){
        //region 数据结构
        /*
        guid	varchar	250	0	0	0	0	0	0		0		utf8	utf8_general_ci		-1	0
        errno	varchar	100	0	0	0	0	0	0		0	包含了错误的级别，是一个 integer。	utf8	utf8_general_ci		0	0
        errstr	text	0	0	-1	0	0	0	0		0	包含了错误的信息，是一个 string。	utf8	utf8_general_ci		0	0
        errfile	text	0	0	-1	0	0	0	0		0	 包含了发生错误的文件名，是一个 string。	utf8	utf8_general_ci		0	0
        errline	varchar	50	0	-1	0	0	0	0		0	包含了错误发生的行号，是一个 integer。	utf8	utf8_general_ci		0	0
        errcontext	text	0	0	-1	0	0	0	0		0	 是一个指向错误发生时活动符号表的 array。 也就是说，errcontext 会包含错误触发处作用域内所有变量的数组。 用户的错误处理程序不应该修改错误上下文（context）。	utf8	utf8_general_ci		0	0
        beizhu	text	0	0	-1	0	0	0	0		0	描述	utf8	utf8_general_ci		0	0
        createdate	int	11	0	-1	0	0	0	0		0					0	0
        sys_user_guid	varchar	250	0	-1	0	0	0	0		0		utf8	utf8_general_ci		0	0
        sys_user_username	varchar	50	0	-1	0	0	0	0		0	用户登录名	utf8	utf8_general_ci		0	0
        */
        //endregion

        $model["guid"] = create_guid();
        $model["errno"] = "-999";//手动添加
        $model["errstr"] = "";
        $model["errline"] = $line;
        $model["errcontext"] = "";
        $model["errfile"] = $url;
        $model["beizhu"] = $content;
        $model["createdate"] = time();
        $model["sys_user_guid"] = $admin_guid;
        $model["sys_user_username"] = $admin_username;
        $this->add($model);
    }
}