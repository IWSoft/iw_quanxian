<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>登录-<?php echo $system_name;?></title>
    <?php
    $this->load->view(__HOME_TEMPLATE__ . '/header.inc.php');
    ?>
    <link href="static/css/login.css" rel="stylesheet">


    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;<?php echo base_url();?>static/ie.html" />
    <![endif]-->
    <script>
        if (window.top !== window.self) {
            window.top.location = window.location;
        }
    </script>
    <style>
        <?php
        if($login_bk_img!=""){
        ?>
        body {
            background: url("<?php echo $login_bk_img;?>") no-repeat center fixed !important;
        }

        <?php
}
 ?>
    </style>
</head>

<body class="signin">
<div class="signinpanel">
    <div class="row">
        <div class="col-sm-7">
            <div class="signin-info">
                <div class="logopanel m-b">
                    <h1><?php //echo lang("home_softname");?><?php echo $system_name;//lang("home_softname");?></h1>
                </div>
                <div class="m-b"></div>
                <h4><?php echo lang("home_login_welcome");?> <strong><?php echo lang("home_softname");?></strong></h4>
                <ul class="m-b" style="display: none;">
                    <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 优势一</li>
                    <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 优势二</li>
                    <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 优势三</li>
                    <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 优势四</li>
                    <li><i class="fa fa-arrow-circle-o-right m-r-xs"></i> 优势五</li>
                </ul>
                <strong style="display: none;">还没有账号？ <a href="#">立即注册&raquo;</a></strong>
            </div>
        </div>
        <div class="col-sm-5">
            <form id="login_form" method="post" action="<?php echo site_url2("/login/dologin");?>">
                <input type="hidden" name="backurl" id="backurl" value="<?php echo $url;?>"/>
                <h4 class="no-margins"><?php echo lang("home_login");?></h4>
                <p class="m-t-md"></p>
                <input type="text" name="user" minlength="3" type="text" required class="form-control uname" placeholder="<?php echo lang('home_login_user');?>" />
                <input type="password" name="pwd" minlength="6" required class="form-control pword m-b" placeholder="<?php echo lang('home_login_pwd');?>" />
                <!--a href=""><?php echo lang("home_forget_pwd");?></a-->
                <button class="btn btn-success btn-block" type="submit"><?php echo lang("home_login_btn");?></button>
                <button class="btn btn-warning btn-block" type="button" onclick="window.location.href='<?php echo site_url2("reg/index");?>';">注册会员</button>
            </form>
        </div>
    </div>
    <?php
    $this->load->view(__HOME_TEMPLATE__ . '/footer.inc.php');
    ?>
</div>
</body>
<!-- jQuery Validation plugin javascript-->
<script src="static/js/plugins/validate/jquery.validate.min.js"></script>
<script src="static/js/plugins/validate/messages_zh.min.js"></script>
<script src="static/js/plugins/layer/layer.min.js"></script>
<script>
    $("#login_form").submit(function(e){
        if(!$(this).validate()){
            return false;
        }
        var index = "";
        $.ajax({
            url:"<?php echo site_url2("/home/login/dologin");?>",
            type:"post",
            dataType:"json",
            data:$(this).serialize(),
            timeout:60000,
            beforeSend:function(){
                index = layer.load(1, {
                    shade: [0.5,'#000'] //0.1透明度的白色背景
                });
            },
            success:function(data){
                layer.close(index);
                if(data.isok=='1'){
                    layer.msg('<?php echo lang('home_login_success');?>');
                    url = $("#backurl").val()!=""?$("#backurl").val():"<?php echo site_url('/iw/main/home/index');?>";
                    window.location.href=url;
                }
                else{
                    layer.msg(data.err);
                }

            }

        });


       $(this).validate();
        return false;
    });
</script>
</html>
