<?php

class m_aiw_sms extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 返回类型列表
     */
    function get_type(){
        return $this->get_list("parent_guid='54f4f320-cc2f-30c6-776b-3dfd0a555f63'");
    }

    //返回自写短信GUID
    function get_zx_guid(){
        return "3ddf00a1-0e9c-b36f-7327-d67623db8b9a";
    }

    function add($model, $isguid = true)
    {
        $tablename = "aiw_sms";
        $insert_id = $this->insert_one(
            $tablename, $model);
        if($isguid){
			 //发短信
            if($insert_id!="" && strlen($model["tel"])==11) {
                 helper_send_msg($model["tel"], $model["content"]);
            }
            return $model["guid"];
        }
        else{
            if($insert_id>0){
                //发短信
                if(strlen($model["tel"])==11) {
                    helper_send_msg($model["tel"], $model["content"]);
                }
            }
            return $insert_id;
        }
    }



}