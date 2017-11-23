<!DOCTYPE html>
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"
    <
    <meta name="renderer" content="webkit">
    <title><?php echo $system_name;?></title>
    <?php
    $this->load->view(__ADMIN_TEMPLATE__ . '/header.inc.php');
    ?>
<style>
    .system_message i{
        width:16px;
        height: 15px;
        padding-top: 1px;
        margin-right: 5px;
        text-align: center;
        border: 1px solid #000000;
    }
</style>
</head>


<body class="fixed-sidebar full-height-layout gray-bg">
<div id="wrapper">
    <!--左侧导航开始-->
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="nav-close"><i class="fa fa-times-circle"></i></div>
        <div class="sidebar-collapse">
            <ul class="nav" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <span><a onclick="$('#btn_edit_logo').click();"  href="javascript:void(0);" ><img alt="image" width="60"  class="img-circle" id="home_user_logo" src="<?php echo $userlogo;?>" /></a></span>
                        <a  data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0);">
                                <span class="clear">
                               <span class="block m-t-xs"><strong class="font-bold"><?php echo $session["model"]["username"]; ?></strong></span>
                                <span class="text-muted text-xs block"><?php if (isset($session["roles"])) {
                                        if (count($session["roles"]) > 0) {
                                            echo $session["roles"][0]["title"];
                                        }
                                    } ?><b class="caret"></b></span>
                                </span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li><a class="J_menuItem" onclick="$('body').click();" id="btn_edit_logo" href="<?php echo site_url2("/iw/user/touxiang/edit");?>" data-index="10000">修改头像</a></li>
                            <li><a class="J_menuItem" onclick="$('body').click();" href="<?php echo site_url2("/iw/user/pwd/index");?>" data-index="10001">修改密码</a></li>
                            <li><a class="J_menuItem" onclick="$('body').click();" href="<?php echo site_url2("/iw/user/sys_user/edit_mine");?>" data-index="10002">个人资料</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo site_url2("/home/login/logout");?>">安全退出</a>
                            </li>
                        </ul>
                    </div>
                    <div class="logo-element"><img alt="image" id="home_user_logo2" class="img-circle" width="60" src="<?php echo $userlogo;?>"/>
                    </div>
                </li>


                <?php
                if (count($module_list) > 0) {
                    $tmp = $module_list;
                    $menu = array();
                    $menu2 = array();//第二层
                    $menu3 = array();//第三层
                    foreach ($tmp as $k => $v) {
                        if ($v["curr_level"] == 0) {
                            $menu[] = $v;
                        }
                        if ($v["curr_level"] == 1) {
                            $menu2[] = $v;
                        }
                        if ($v["curr_level"] == 2) {
                            $menu3[] = $v;
                        }
                    }


                    for ($i = 0; $i < count($menu); $i++) {
                        $menu[$i]["sub_menu"] = array();
                        $j_index = 0;
                        for ($j = 0; $j < count($menu2); $j++) {
                            if ($menu[$i]["guid"] == $menu2[$j]["parent_guid"]) {
                                //$menu[$i]["sub_menu"][$j_index] = $menu2[$j];
                                array_push($menu[$i]["sub_menu"],$menu2[$j]);
                                $j_index2 = 0;
                                for ($k = 0; $k < count($menu3); $k++) {
                                    if ($menu2[$j]["guid"] == $menu3[$k]["parent_guid"]) {
                                        //echo $menu2[$j]["title"]."|".$menu3[$k]["title"]."<br/>";
                                        $menu[$i]["sub_menu"][$j_index]["sub_menu"][$j_index2] = $menu3[$k];
                                        //print_r($menu3[$k]);
                                        //array_push($menu[$i]["sub_menu"][$j_index]["sub_menu"],$menu3[$k]);
                                        $j_index2++;
                                    }
                                }
                                $j_index++;
                            }
                        }
                    }


                    //三层合一
                    $is_desktop = false;//标识是不是已设定主页位置
                    $deskto_url = "";
                    foreach ($menu as $k => $v) {
                        echo '<li>';
                        //if($v["urltype"]==0){
                        //一级菜单
                        if ($v["url_target"] != "_layerbox") {
                            echo '<a id="'.$v["guid"].'" href="' . ($v["urltype"] == 0 && $v["url"] != "" ? site_url2($v["url"]) : $v["url"]) . '" target="' . ($v["url_target"]) . '"';
                        } else {
                            //弹层
                            echo '<a  id="'.$v["guid"].'" href="javascript:void(0);" onclick="my_open_box({title:\''.str_replace("'","",$v["title"]).'\',url:\'' . ($v["urltype"] == 0 && $v["url"] != "" ? site_url2($v["url"]) : $v["url"]) . '\',width:0,height:0})" ';
                        }
                        if ($v["url"] != "" && $v["url"]!=base_url()."index.php/" && !$is_desktop ) {
                            echo 'data-index="0"';
                            $is_desktop = true;
                            $deskto_url = $v["urltype"] == 0 && $v["url"] != "" ? site_url2($v["url"]) : $v["url"];
                        }
                        //
                        echo '>';
                        echo '<i class="' . $v["dd_icon"] . '" ></i>';
                        echo '<span class="nav-label">' . $v["title"] . '</span>';
                        echo '<span class="fa arrow"></span>';
                        echo '</a>' . "\n";

                        //二级（模块）菜单
                        if (isset($v["sub_menu"])) {
                            echo '<ul class="nav nav-second-level ' . ($v["collapsed"] == 0 ? "collapse in" : "") . '">' . "\n";

                            foreach ($v["sub_menu"] as $k2 => $v2) {
                                $url_level2 = create_module_url($v2["controller"], $v2["method"], $v2["param"], $v2["url"]);
                                if($url_level2==base_url()."index.php/"){
                                    $url_level2="";
                                }
                                echo '<li>';
                                if ($v2["url_target"] != "_layerbox") {
                                    echo '<a  id="'.$v2["guid"].'" class="J_menuItem" href="' . ($url_level2) . '" target="' . ($v2["url_target"]) . '"';//$v2["urltype"]==0 && $v2["url"]!=""?site_url2($v2["url"]):$v2["url"]
                                }
                                else{
                                    //弹层
                                    echo '<a id="'.$v2["guid"].'" class="J_menuItem" href="javascript:void(0);" onclick="my_open_box({title:\''.str_replace("'","",$v2["title"]).'\',url:\'' . ($url_level2) . '\',width:0,height:0})" ';
                                }
                                if ($url_level2 != ""  && !$is_desktop) {
                                    echo ' data-index="0"';
                                    $is_desktop = true;
                                    $deskto_url = $url_level2;//$v2["urltype"]==0 && $v2["url"]!=""?site_url2($v2["url"]):$v2["url"];

                                }
                                echo '>';
                                echo '<i class="' . $v2["dd_icon"] . '" ></i>';
                                echo '<span class="nav-label">' . $v2["title"] . '</span>' . "\n";
                                if (isset($v2["sub_menu"])) {
                                    //是否存在三级
                                    echo '<span class="fa arrow"></span>';
                                }
                                echo '</a>' . "\n";//二级结束
                                if (isset($v2["sub_menu"])) {
                                    echo '<span class="fa arrow"></span>' . "\n";
                                    echo '<ul class="nav nav-third-level ' . ($v["collapsed"] == 0 ? "collapse in" : "") . '">' . "\n";
                                    foreach ($v2["sub_menu"] as $k3 => $v3) {
                                        $url_level3 = create_module_url($v3["controller"], $v3["method"], $v3["param"], $v3["url"]);
                                        if($url_level3==base_url()."index.php/"){
                                            $url_level3="";
                                        }
                                        echo '<li>';
                                        if ($v3["url_target"] != "_layerbox") {
                                            echo '<a id="'.$v3["guid"].'" class="J_menuItem" href="' . ($url_level3) . '" target="' . ($v3["url_target"]) . '" ';
                                        }
                                        else{
                                            //弹层
                                            echo '<a id="'.$v3["guid"].'" class="J_menuItem" href="javascript:void(0);" onclick="my_open_box({title:\''.str_replace("'","",$v3["title"]).'\',url:\''.$url_level3.'\',width:0,height:0})" ';
                                        }
                                        if ($url_level3 != ""   && !$is_desktop) {
                                            echo 'data-index="0"';
                                            $is_desktop = true;
                                            $deskto_url = $url_level3;//$v3["urltype"]==0 && $v3["url"]!=""?site_url2($v3["url"]):$v3["url"];
                                        }
                                        echo '>';
                                        echo '<i class="' . $v3["dd_icon"] . '" ></i>';
                                        echo '<span class="nav-label">' . $v3["title"] . '</span>';
                                        echo '</a>';
                                        echo '</li>' . "\n";
                                    }
                                    echo '</ul>' . "\n";
                                }

                                echo '</li>' . "\n";

                            }
                            echo '</ul>' . "\n";
                        }


                        // }
                        echo '</li>';
                        ?>


                        <?php
                    }
                }
                ?>

            </ul>

        </div>
    </nav>
    <!--左侧导航结束-->
    <!--右侧部分开始-->
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <div class="row border-bottom">
            <nav class="navbar  navbar-static-top" role="navigation" style="margin-bottom: 0; ">
                <!--微型菜单按钮-->
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0);"><i class="fa fa-bars"></i> </a>

                <ul class=" nav navbar-top-links navbar-right ">
                    <li class="dropdown">
                        <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell"></i> <span class="label label-primary" id="system_message_count"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts" id="system_message" >


                            <li class="divider"></li>
                            <!--
                            <li>
                                <a href="profile.html">
                                    <div>
                                        <i class="fa fa-qq fa-fw"></i> 3条新回复 <span class="pull-right text-muted small">12分钟钱</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            -->
                            <li id="system_message_more">
                                <div class="text-center link-block  "  >
                                    <?php //echo site_url2("list_system_msg");暂时用提醒语句?>
                                    <a class="J_menuItem" href="javascript:tab__open('b54375b4-6c48-c5aa-ae52-a3cbeb33eef2')" data-index="10003">
                                        <strong>查看所有 </strong> <i class="fa fa-angle-right"></i>
                                    </a>
                                </div>
                            </li>

                        </ul>
                    </li>
                    </ul>
                <script>
                    function get_message(){
                        $.ajax({
                           url:"<?php echo site_url2("get_system_msg");?>",
                           type:"get",
                            dataType:"json",
                            error:function(a,b){
//                                console.log("aaa="+b);
                            },
                            success:function(list){
                               //判断有多少条，如超出5条，则先清空旧

                                if($(".system_message").length>=5){
                                    $(".system_message").remove();
                                }
                               html="";
                                read=0;
                                for(i=0;i<list.length;i++) {
                                    if(i<5) {
                                        if (list[i]["isread"] == 0) {
                                            read++;
                                        }
                                        //判断有无重复
                                        ischongfu = false;
                                        $(".system_message").each(function () {
                                            if ($(this).attr("guid") == list[i]["guid"]) {
                                                ischongfu = true;
                                            }
                                        });
                                        if (!ischongfu) {
                                            html += '<li guid="' + list[i]["guid"] + '" class="system_message"><a href="javascript:void(0);" title="'+list[i]["fulltitle"]+'" onclick=\'my_open_box({ title:"查看消息",url:"<?php echo site_url2("view_system_msg") . "?guid=";?>' + list[i]["guid"] + '",width:"30%",height:"40%"});\'><div><i style="color:' + list[i]["flag_font_color"] + ';background: ' + list[i]["flag_color"] + '" class="' + list[i]["flag"] + '"></i>' + (list[i]["isread"] == 0 ? "<b>" : "") + list[i]["title"] + (list[i]["isread"] == 0 ? "</b>" : "") + '<span class="pull-right text-muted small">' + list[i]["createdate"] + '</span></div></a></li>';
                                        }
                                    }

                                }


                                if(read==0){
                                    $("#system_message_count").html('');
                                }
                                else {
                                    $("#system_message_count").html(read);
                                }
                                if(list.length==0){
                                    //$("#system_message_count").html('');
                                    if($("#system_message").html()=='') {
                                        html = "<li>暂无消息</li>";
                                        $("#system_message").prepend(html);
                                    }
                                }
                                else {
                                    $("#system_message").prepend(html);
                                }
                                if(list.length>0){
                                    $("#system_message_more").css("display","");
                                }
                                else{
                                    $("#system_message_more").css("display","none");
                                }
                            }
                        });
                    }
                    get_message();
                    window.setInterval("get_message()",(1000*60*5));
                </script>
            </nav>
        </div>
        <div class="row content-tabs">
            <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-angle-double-left"></i>
            </button>
            <nav class="page-tabs J_menuTabs">
                <div class="page-tabs-content">
                    <a href="javascript:;" class="active J_menuTab"
                       data-id="<?php echo isset($deskto_url) ? $deskto_url : "nodata"; ?>">桌面</a>
                    <!--默认主页需在对应的选项卡a元素上添加data-id="默认主页的url"-->
                </div>
            </nav>
            <button class="roll-nav roll-right J_tabRight">
                <i class="fa fa-angle-double-right"></i>
                <!--右边导航滚动-->
            </button>


            <div class="btn-group roll-nav roll-right">
                <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span>

                </button>
                <ul role="menu" class="dropdown-menu dropdown-menu-right">
                    <li class="J_tabShowActive"><a>定位当前选项卡</a>
                    </li>
                    <li class="divider"></li>
                    <li class="J_tabCloseAll"><a>关闭全部选项卡</a>
                    </li>
                    <li class="J_tabCloseOther"><a>关闭其他选项卡</a>
                    </li>
                </ul>
            </div>
            <a href="<?php echo site_url2("/home/login/logout"); ?>" class="roll-nav roll-right J_tabExit"><i
                    class="fa fa fa-sign-out"></i> 退出</a>

        </div>
        <?php
        if($deskto_url==base_url()."index.php/"){
            //代表无值  预防默认排第一个的菜单，导致系统无法识别桌面
            $deskto_url = "";
        }

        ?>
        <div class="row J_mainContent" id="content-main">
            <iframe class="J_iframe" name="iframe0" width="100%" height="100%"
                    src="<?php echo isset($deskto_url) ? $deskto_url : site_url2("/iw/main/home/page_404"); ?>"
                    frameborder="0" data-id="<?php echo isset($deskto_url) ? $deskto_url : "nodata"; ?>"
                    seamless></iframe>
            <!--默认主页需在对应的页面显示iframe元素上添加name="iframe0"和data-id="默认主页的url"-->
        </div>
        <?php
        $this->load->view(__ADMIN_TEMPLATE__ . '/footer.php');
        ?>
    </div>
    <!--右侧部分结束-->
</div>





<script>
    //辅助点击！
    function tab__open(id){
        $("#"+id).click();
        if($(document).width()<1000) {
            $('.navbar-minimalize').trigger('click');
        }
        layer.closeAll();
    }
</script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . '/footer.inc.php');
?>
</body>
</html>