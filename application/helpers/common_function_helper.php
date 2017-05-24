<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


/**
 * 显示页面
 * @param string $message 错误信息
 * @param string $url 页面跳转地址
 * @param string $timeout 时间
 * @param string $iserror 是否错误 1正确 0错误
 * @param string $params 其他参数前面加? 例如?id=122&time=333
 */
if (!function_exists('showmessage')) {

    //跳转	$template模板以哪个模板进行跳转（2016年3月26日10:31:56）
    function showmessage($message = '', $url = '', $timeout = '3', $iserror = 1, $params = '', $template = '')
    {
        if ($iserror == 1) {//正确
            include APPPATH . '/errors/showmessage.php';
        } else {
            include APPPATH . "/errors/showmessage_error$template.php";
        }

        die();
    }

}

/**
 * 获取当前网址，含端口号
 * @author WEI
 * @param $array array(1,3,4,5,6,7)
 * @return String 1,3,4,5,6,7
 */
if (!function_exists("get_url")) {

    function get_url($isencode = true)
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
            if (isset($_SERVER['QUERY_STRING'])) {
                $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
            }
        }

        $url = ($_SERVER["SERVER_PORT"] == "443" ? "https://" : 'http://') . $_SERVER['SERVER_NAME']; //
        if ($_SERVER["SERVER_PORT"] != 80 && $_SERVER["SERVER_PORT"] != "443") {
            $url .= ":" . $_SERVER["SERVER_PORT"];
        }
        if (substr($_SERVER["REQUEST_URI"], 0, 1) == "/") {
            $url .= $_SERVER["REQUEST_URI"];
        } else {
            $url .= "/" . $_SERVER["REQUEST_URI"];
        }
        return ($isencode ? urlencode($url) : $url);
    }

}

if (!function_exists('cn_substr')) {

    function cn_substr($str, $slen, $startdd = 0)
    {
        global $cfg_soft_lang;
        if ($cfg_soft_lang == 'utf-8') {
            return cn_substr_utf8($str, $slen, $startdd);
        }
        $restr = '';
        $c = '';
        $str_len = strlen($str);
        if ($str_len < $startdd + 1) {
            return '';
        }
        if ($str_len < $startdd + $slen || $slen == 0) {
            $slen = $str_len - $startdd;
        }
        $enddd = $startdd + $slen - 1;
        for ($i = 0; $i < $str_len; $i++) {
            if ($startdd == 0) {
                $restr .= $c;
            } else if ($i > $startdd) {
                $restr .= $c;
            }

            if (ord($str[$i]) > 0x80) {
                if ($str_len > $i + 1) {
                    $c = $str[$i] . $str[$i + 1];
                }
                $i++;
            } else {
                $c = $str[$i];
            }

            if ($i >= $enddd) {
                if (strlen($restr) + strlen($c) > $slen) {
                    break;
                } else {
                    $restr .= $c;
                    break;
                }
            }
        }
        return $restr;
    }

}


if (!function_exists('getImgsFormEditor')) {
    /**
     * @param $content
     * @param string $order
     * @return string
     */
    function getImgsFormEditor($content, $order = 'ALL')
    {
        $pattern = "/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/";
        preg_match_all($pattern, $content, $match);
        if (isset($match[1]) && !empty($match[1])) {
            if ($order === 'ALL') {
                return $match[1];
            }
            if (is_numeric($order) && isset($match[1][$order])) {
                return $match[1][$order];
            }
        }
        return '';
    }
}
if (!function_exists('create_guid')) {
    /**
     * 创建GUID
     * @return string
     */
    function create_guid()
    {
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        //	$uuid = chr(123)// "{"
        $uuid =
            substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        //.chr(125);// "}"
        return $uuid;
    }
}

/**
 * 创建6位编号
 * @return int
 */
function create_id(){
    return mt_rand(100000,999999);
}

/**
 * 创建多级不存在目录
 * @param type $dir 相对目录也可以
 * @param type $mode
 * @return void
 */
if (!function_exists('mkdirs')) {
    function mkdirs($dir, $mode = 0777)
    {
        $dirArray = explode("/", $dir);
        $dirArray = array_filter($dirArray);
        $created = "";
        foreach ($dirArray as $key => $value) {
            if (!empty($created)) {
                $created .= "/" . $value;
                if (!is_dir($created)) {
                    @mkdir($created, $mode);
                }
            } else {
                if (!is_dir($value)) {
                    @mkdir($value, $mode);
                }
                $created .= $value;
            }
        }
    }
}

/**
 * 保存远程图片
 * @param $url
 * @param string $filename
 * @return bool|string
 */
function GrabImage($url, $filename = "")
{
    if ($url == ""):return false;endif;

    if ($filename == "") {
        $ext = strrchr($url, ".");
        if ($ext != ".gif" && $ext != ".jpg"):return false;endif;
        $filename = date("dMYHis") . $ext;
    }

    ob_start();
    readfile($url);
    $img = ob_get_contents();
    ob_end_clean();
    $size = strlen($img);

    $fp2 = @fopen($filename, "a");
    fwrite($fp2, $img);
    fclose($fp2);

    return $filename;
}

/**
 * 从内容中将远程图片下载到本地
 * @param $content
 * @return mixed
 */
function downloadpic_content($content)
{
    $isopen = true;
    if (!$isopen) {
        return $content;
    }
    $matches = array();
    preg_match_all("/<img([^>]*)\s*src=('|\")(http:\/\/)([^'\"]+)('|\")/",
        $content, $matches);//带引号
    //preg_match_all("/<img([^>]*)\ssrc=([^\s>]+)/",$string,$matches);//不带引号
    $new_arr = "";
    if (count($matches) > 0) {
        $new_arr = array_unique($matches[4]);//去除数组中重复的值
    }
    $new_file_arr = array();
    for ($i = 0; $i < count($new_arr); $i++) {
        $yuancheng = "http://" . $new_arr[$i];
        //判断是否为本站，如果不是，就保存
        if (strstr($yuancheng, "http://" . $_SERVER['HTTP_HOST']) !== false) {


        } else {
            $filetype = explode(".", $yuancheng);
            $filetype = $filetype[count($filetype) - 1];
            //新文 件名
            $newfilename = strtolower(create_guid()) . "." . $filetype;
            $path = "data/upload/news/" . date("Ym");
            if (!is_dir($path)) {
                @mkdir($path);
            }
            GrabImage($yuancheng, $path . "/" . $newfilename);
            $new_file_arr[$i] = "/" . $path . "/" . $newfilename;
            $content = str_replace($yuancheng, $new_file_arr[$i], $content);
        }
    }
    return $content;
}


/**
 * 创建多级不存在目录
 * @param type $dir 相对目录也可以
 * @param type $mode
 * @return void
 */
function mkdirs($dir, $mode = 0777)
{
    $dirArray = explode("/", $dir);
    $dirArray = array_filter($dirArray);
    $created = "";
    foreach ($dirArray as $key => $value) {
        if (!empty($created)) {
            $created .= "/" . $value;
            if (!is_dir($created)) {
                @mkdir($created, $mode);
            }
        } else {
            if (!is_dir($value)) {
                @mkdir($value, $mode);
            }
            $created .= $value;
        }
    }
}


if (!function_exists('create_guid')) {
    function create_guid()
    {
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        //	$uuid = chr(123)// "{"
        $uuid =
            substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        //.chr(125);// "}"
        return $uuid;
    }
}


/**
 * 发送电邮
 * @param $mail_to
 * @param $mail_subject
 * @param $mail_message
 */
function sendmail_help($mail_to, $mail_subject, $mail_message)
{


    $bfconfig = Array(
        'sitename' => '    '
    );

    $mail = Array(
        'state' => 1,
        'server' => 'smtp.163.com',
        'port' => 25,
        'auth' => 1,
        'username' => 'xxxx@163.com',
        'password' => 'xxxx',
        'charset' => 'utf-8',
        'mailfrom' => 'xxxxx@163.com'
    );

    date_default_timezone_set('PRC');

    $mail_subject = '=?' . $mail['charset'] . '?B?' . base64_encode($mail_subject) . '?=';
    $mail_message = chunk_split(base64_encode(preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $mail_message)));

    $headers = "";
    $headers .= "MIME-Version:1.0\r\n";
    $headers .= "Content-type:text/html\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";
    $headers .= "From: " . $bfconfig['sitename'] . "<" . $mail['mailfrom'] . ">\r\n";
    $headers .= "Date: " . date("r") . "\r\n";
    list($msec, $sec) = explode(" ", microtime());
    $headers .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail['mailfrom'] . ">\r\n";

    if (!$fp = fsockopen($mail['server'], $mail['port'], $errno, $errstr, 30)) {
        exit("CONNECT - Unable to connect to the SMTP server");
    }

    stream_set_blocking($fp, true);

    $lastmessage = fgets($fp, 512);
    if (substr($lastmessage, 0, 3) != '220') {
        exit("CONNECT - " . $lastmessage);
    }

    fputs($fp, ($mail['auth'] ? 'EHLO' : 'HELO') . " befen\r\n");
    $lastmessage = fgets($fp, 512);
    if (substr($lastmessage, 0, 3) != 220 && substr($lastmessage, 0, 3) != 250) {
        exit("HELO/EHLO - " . $lastmessage);
    }

    while (1) {
        if (substr($lastmessage, 3, 1) != '-' || empty($lastmessage)) {
            break;
        }
        $lastmessage = fgets($fp, 512);
    }

    if ($mail['auth']) {
        fputs($fp, "AUTH LOGIN\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 334) {
            exit($lastmessage);
        }

        fputs($fp, base64_encode($mail['username']) . "\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 334) {
            exit("AUTH LOGIN - " . $lastmessage);
        }

        fputs($fp, base64_encode($mail['password']) . "\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 235) {
            exit("AUTH LOGIN - " . $lastmessage);
        }

        $email_from = $mail['mailfrom'];
    }

    fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
    $lastmessage = fgets($fp, 512);
    if (substr($lastmessage, 0, 3) != 250) {
        fputs($fp, "MAIL FROM: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $email_from) . ">\r\n");
        $lastmessage = fgets($fp, 512);
        if (substr($lastmessage, 0, 3) != 250) {
            exit("MAIL FROM - " . $lastmessage);
        }
    }

    foreach (explode(',', $mail_to) as $touser) {
        $touser = trim($touser);
        if ($touser) {
            fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
            $lastmessage = fgets($fp, 512);
            if (substr($lastmessage, 0, 3) != 250) {
                fputs($fp, "RCPT TO: <" . preg_replace("/.*\<(.+?)\>.*/", "\\1", $touser) . ">\r\n");
                $lastmessage = fgets($fp, 512);
                exit("RCPT TO - " . $lastmessage);
            }
        }
    }

    fputs($fp, "DATA\r\n");
    $lastmessage = fgets($fp, 512);
    if (substr($lastmessage, 0, 3) != 354) {
        exit("DATA - " . $lastmessage);
    }

    fputs($fp, $headers);
    fputs($fp, "To: " . $mail_to . "\r\n");
    fputs($fp, "Subject: $mail_subject\r\n");
    fputs($fp, "\r\n\r\n");
    fputs($fp, "$mail_message\r\n.\r\n");
    $lastmessage = fgets($fp, 512);
    if (substr($lastmessage, 0, 3) != 250) {
        exit("END - " . $lastmessage);
    }

    fputs($fp, "QUIT\r\n");

}

/**
 * 用于权限菜单 返回处理后的链接
 * @param $controller 控制器
 * @param $method    方法名
 * @param $param ？以后的参数
 * @param $url 优先返回
 */
function create_module_url($controller, $method, $param, $url = "")
{
    if ($url != "") {
        return $url;
    }
    $url = site_url2($controller . "/" . $method) . ($param == "" ? "" : ("?" . $param));
    return $url;
}

/**
 * isset的调整版
 * @param $val
 */
function isset2($val)
{
    return isset($val) ? $val : "";
}

function helper_alert_success_msg($str)
{
    $html = '<div class="alert alert-success">';
    $html .= $str;
    $html .= "</div>";

    return $html;
}

function helper_alert_info_msg($str)
{
    $html = '<div class="alert alert-info">';
    $html .= $str;
    $html .= "</div>";
    return $html;
}

function helper_alert_warning_msg($str)
{
    $html = '<div class="alert alert-warning">';
    $html .= $str;
    $html .= "</div>";
    return $html;
}

function helper_alert_danger_msg($str)
{
    $html = '<div class="alert alert-danger">';
    $html .= $str;
    $html .= "</div>";
    return $html;
}

/**
 * 通用信息提示页
 * @param string $msg
 * @param string $url
 * @param string $miao 多少秒后返回 不返回设为0
 */
function helper_ok($msg="",$url="",$miao="0"){
    $gourl = site_url2("/iw/main/msg/ok")."?url=".urlencode($url)."&msg=".urlencode($msg)."&miao=".$miao;
    header("location:".$gourl);
}
/**
 * 通用信息提示页
 * @param string $msg
 * @param string $url
 * @param string $miao 多少秒后返回 不返回设为0
 */
function helper_err($msg="",$url="",$miao="0"){
    $gourl = site_url2("/iw/main/msg/err")."?url=".urlencode($url)."&msg=".urlencode($msg)."&miao=".$miao;
    header("location:".$gourl);
}

/**
 * 将CSS文件插入到页头
 * @param $data
 * @param $css_arr
 */
function helper_include_css(&$data,$css_arr){
    if(isset($data["header_include_css"])){
        foreach($css_arr as $v)
        array_push(
            $data["header_include_css"],
             "static/css/plugins/".$v
            );
    } else{
        $data["header_include_css"] = array();
        foreach($css_arr as $v)
            array_push(
                $data["header_include_css"],
                "static/css/plugins/".$v
            );
    }
}

/**
 * 将JS文件插入到页头
 * @param $data
 * @param $js_arr
 */
function helper_include_js(&$data,$js_arr){
    if(isset($data["header_include_js"])){
        foreach($js_arr as $v)
            array_push(
                $data["header_include_js"],
                "static/js/plugins/".$v
            );
    }
    else{
        $data["header_include_js"] = array();
        foreach($js_arr as $v)
            array_push(
                $data["header_include_js"],
                "static/js/plugins/".$v
            );
    }
}

function helper_get_json_header(){
    header("HTTP/1.0 200 OK");
    //header('Content-type: text/html; charset=utf-8');
    header('Content-type: application/json');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
}


/**
 * 用于AJAX请求后的统一返回格式
 * @param $msg 操作结果
 * @param $url 返回的地址
 * @param bool $isok 是否成功执行
 * @param int $miao 显示秒数
 */
function helper_return_json($msg="操作成功",$url="",$isok=false,$miao=3){
    $json["isok"] = $isok;
    $json["msg"] = $msg;
    $json["miao"] = $miao;
    $json["url"] = $url;
    return json_encode($json);
}

/**
 * @param $str 原始字符
 * @param $count 往后输出多少个STR2
 * @param $str2 输出的字符
 */
function helper_str_pad2($str,$count,$str2){
    $html = $str;
    for ($i=0;$i<$count;$i++){
        $html.= $str2;
    }
    return $html;
}

/**
 * 返回上一个网页
 * @param bool $isencode 是否编码
 */
function helper_pre_url($isencode=true){
    $url =  $_SERVER['HTTP_REFERER'];
    if($isencode){
        $url = urlencode($url);
    }
    return $url;
}

/**
 * 返回星期几
 */
function helper_week(){
    $val = date("w");
    switch ($val){
        case 1:
            $val = "一";
             break;
        case 2:
            $val = "二";
            break;
        case 3:
            $val = "三";
            break;
        case 4:
            $val = "四";
            break;
        case 5:
            $val = "五";
            break;
        case 6:
            $val = "六";
            break;
        case 7:
        case 0:
            $val = "日";
            break;
    }
    $val = "星期".$val;
    return $val;
}

/**
 * 判断字符串是否以第二个参数结尾
 * @param $str
 * @param $val
 */
function helper_endwith($str,$val){
    return substr($str,strlen($str)-1,1)==$val;
}
function helper_startwith($str,$val){
    return substr($str,0,1)==$val;
}
?>