<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?php echo base_url();?>"/>
    <title>登录-<?php echo $system_name;?></title>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="keywords" content="微信订餐平台">
    <link rel="stylesheet" href="static/theme/youbao/css/style.css">
</head>
<body>
<header>
    <h1><?php echo $system_name;//lang("home_softname");?></h1>
</header>
<div class="login_content page">
    <img src="static/theme/youbao/images/entry.png" alt="" class="entry">
    <form id="login_form" method="post" action="<?php echo site_url2("/login/dologin");?>">
        <input type="hidden" name="backurl" id="backurl" value="<?php echo $url;?>"/>
        <label for=""><input required type="text" minlength="3" placeholder="请输入手机号" name="user" class="login_name"></label>
        <label for=""><input required type="password" minlength="6" name="pwd" placeholder="请输入登录密码" class="login_name"></label>
        <input type="submit" class="btn" value="<?php echo lang("home_login_btn");?>"/>
        <input type="button" class="btn02" value="注册会员" onclick="window.location.href='<?php echo site_url2("reg/index");?>';">
    </form>
</div>
<script src="static/js/jquery.min.js?v=2.1.4"></script>
<script src="static/js/plugins/layer/layer.min.js"></script></div>
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
</body>
</html>
