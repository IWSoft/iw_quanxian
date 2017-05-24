<?php


define('BASEPATH', "");
define('ENVIRONMENT', 'production');
function create_guid() {
    $charid = strtolower(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);// "-"
    //$uuid = chr(123)// "{"
    $uuid=
        substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12);
    //.chr(125);// "}"
    return $uuid;
}
include ("config.inc.php");
$post = $_POST;
if(count($post)>0){
    //$host = '', $user = '', $password = '', $database = '', $port = '', $socket = ''
    $port = $db['default']["hostname"];
    $port = explode(":",$port);
    if(count($port)>1){
        $port = $port[1];
    }
    else{
        $port = "3306";
    }
    $conn  = mysqli_connect(
        $db['default']["hostname"],
        $db['default']["username"],
        $db['default']["password"],
        $db['default']["database"],
        $port
    );
    foreach($post as $k=>$v){
        $post[$k] = str_replace("'","",$v);
        $post[$k] = str_replace("<","&lt;",$post[$k]);
        $post[$k] = str_replace(">","&gt;",$post[$k]);

    }

    $sql = "insert into com_err_log(guid,errno,errstr,errfile,errline,errcontext,beizhu,createdate
,sys_user_guid,sys_user_username
) values('".create_guid() ."',
'".(isset($post["errno"])?$post["errno"]:"")."',
'".(isset($post["errstr"])?$post["errstr"]:"")."',
'".(isset($post["errfile"])?$post["errfile"]:"")."',
'".(isset($post["errline"])?$post["errline"]:"")."',
'".(isset($post["errcontext"])?$post["errcontext"]:"")."',
'".(isset($post["beizhu"])?$post["beizhu"]:"")."',
".time().",'','')";
    mysqli_query($conn,$sql);
    mysqli_close($conn);

}




?>