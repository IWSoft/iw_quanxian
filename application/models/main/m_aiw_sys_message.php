<?php

class m_aiw_sys_message extends m_common
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * 向单个用户发送提醒
     * @param $user_guid 用户表guid
     * @param $title 标题
     * @param $content 内容
     * @param $msg_level 数字越高，重要性越强 如：0普通信息 10提醒 20警告 100重点关注
     * @param string $sys_module_id 模块ID，为空使用默认方式打开
     * @param string $param  网址参数
     */
    function send_msg($user_guid,$title,$content,$msg_level=0,$sys_module_id="",$param="")
    {
        $model["guid"] = create_guid();
        $model["title"] = $title;
        $model["content"] = $content;
        $model["sys_module_id"] = $sys_module_id;
        $model["param"] = $param;
        $model["createdate"] = time();
        $model["createuser"] = $user_guid;
        $model["receive_user"] = $user_guid;
        $model["msg_level"] = $msg_level;
        $model["isread"] = 0;
        $this->add($model);
    }

    /**
     * 向多个用户发送提醒
     * @param $user_guids 用户表guid 用","隔开
     * @param $title 标题
     * @param $content 内容
     * @param int $msg_level 数字越高，重要性越强 如：0普通信息 10提醒 20警告 100重点关注
     * @param string $sys_module_id  模块ID，为空使用默认方式打开
     * @param string $param 网址参数
     */
    function send_msg_to_users($user_guids,$title,$content,$msg_level=0,$sys_module_id="",$param=""){
        $arr = explode(",",$user_guids);
        foreach($arr as $v){
            $this->send_msg($v,$title,$content,$msg_level,$sys_module_id,$param);
        }
    }

    /**
     * 读出提醒
     * @param $user_guid 用户表GUID
     * @param bool $isread 默认是读出未读信息
     * @param int $limit 默认输出3条
     */
    function get_msg($user_guid,$isread=false,$limit=3){
        $where = "receive_user='".$user_guid."'".(!$isread?" and isread='0' ":"");
        $orderby = "createdate desc";
        $list = $this->get_list($where, $orderby);
        foreach($list as $k=>$v){
            if(function_exists('mb_substr')) {
                if (mb_strlen($v["title"]) > 13) {
                    $list[$k]["title"] = mb_substr($v["title"], 0, 13);
                    $list[$k]["fulltitle"] = $v["title"];
                }
                else{
                    $list[$k]["fulltitle"] = $v["title"];
                }
            }
            if($v["isread"]==0){
                $list[$k]["bold_title"] = "<b>".$v["title"]."</b>";
            }
            else{
                $list[$k]["bold_title"] = $v["title"];
            }
            $fen = floor((time()-$v["createdate"])/60);
            if($fen==0){
                $list[$k]["createdate"] = "1分钟内";
            }
            else{
                if($fen<60){
                    $list[$k]["createdate"] = $fen."分钟前";
                }
                else if($fen>=60 && $fen<120){
                    $list[$k]["createdate"] = "1小时前";
                }
                else if($fen>=120 && $fen<1440){
                    $list[$k]["createdate"] = date("H:i",$list[$k]["createdate"]);
                }
                else{
                    $list[$k]["createdate"] = date("m-d",$list[$k]["createdate"]);
                }
            }
            /*
            switch ($v["msg_level"]){
                case 10:
                    $list[$k]["flag"] = "fa fa-info";
                    $list[$k]["flag_color"] = "#8B7B8B";
                    $list[$k]["flag_font_color"] = "#000";
                    break;
                case 20:
                    $list[$k]["flag"] = "fa fa-flag";
                    $list[$k]["flag_color"] = "#FFFF00";
                    $list[$k]["flag_font_color"] = "#000";
                    break;
                case 100:
                    $list[$k]["flag"] = "fa fa-warning";
                    $list[$k]["flag_color"] = "red";
                    $list[$k]["flag_font_color"] = "#fff";
                    break;
                default:
                    $list[$k]["flag"] = "fa fa-paw";
                    $list[$k]["flag_color"] = "#8B814C";
                    $list[$k]["flag_font_color"] = "#000";
                    break;
            }
            */
            $this->msg_level_to_array($v["msg_level"], $list[$k]);
        }
        return $list;
    }

    /**
     * 根据警告等级返回字体颜色及图标
     * @param $msg_level
     * @param $array
     */
    function msg_level_to_array($msg_level,&$array){
        switch ($msg_level){
            case 10:
                $array["flag"] = "fa fa-info";
                $array["flag_color"] = "#8B7B8B";
                $array["flag_font_color"] = "#000";
                break;
            case 20:
                $array["flag"] = "fa fa-flag";
                $array["flag_color"] = "#FFFF00";
                $array["flag_font_color"] = "#000";
                break;
            case 100:
                $array["flag"] = "fa fa-warning";
                $array["flag_color"] = "red";
                $array["flag_font_color"] = "#fff";
                break;
            default:
                $array["flag"] = "fa fa-paw";
                $array["flag_color"] = "#8B814C";
                $array["flag_font_color"] = "#000";
                break;
        }
    }

    function send_msg_to_role(){

    }


}