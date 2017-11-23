<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <title>注册会员-<?php echo $system_name;?></title>
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
            <form id="reg_form" method="post">
                <input type="hidden" name="backurl" id="backurl" value="<?php echo $url;?>"/>
                <h4 class="no-margins">注册</h4>
                <p class="m-t-md"></p>
                <input type="text" name="realname" id="realname" minlength="2" maxlength="10" type="text" required class="form-control uname" placeholder="输入姓名" />
                <p class="m-t-md"></p>
                <select name="company" id="company" class="form-control m-b" required style="color:#000;">
                    <option value="">选择所在单位</option>
                    <?php
                    foreach($company as $v){
                        echo "<option value='".$v["guid"]."'>".$v["name"]."</option>";
                    }
                    ?>
                </select>
                <p class="m-t-md"></p>
                <input type="text" style="color:#000;" name="tel" id="tel" maxlength="11" type="text" required class="form-control" placeholder="输入11位手机号" />
                <p class="m-t-md"></p>
                <input type="password" style="color:#000;" name="pwd" id="pwd" maxlength="20" type="text" required class="form-control" placeholder="密码(数字或字母)" />
                <p class="m-t-md"></p>
                <input type="password" style="color:#000;" name="pwd2" id="pwd2" maxlength="20" type="text" required class="form-control" placeholder="确认密码(数字或字母)" />
                <p class="m-t-md"></p>
                <p class=".col-sm-2 "><input type="number" pattern="\d" style="color:#000;"  maxlength="4" required name="yzm" id="yzm" style="width: 100%" class="form-control" value="" placeholder="请输入验证码"/></p>
                <p class=".col-sm-2 "><img style="cursor: pointer;" width="100" height="30" src="<?php echo site_url2("home/code");?>" id="yzm_img" onclick="$('#yzm_img').attr('src','<?php echo site_url2("home/code");?>?rnd='+Math.random());"/>
                点击图片刷新验证码
                </p>

                <!--a href=""><?php echo lang("home_forget_pwd");?></a-->
                <button class="btn btn-success btn-block" type="submit">提交注册</button>
                <button class="btn btn-warning btn-block" type="button" onclick="window.location.href='<?php echo site_url2('home/login/index');?>'">登录系统</button>
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
    var ischeck = 0;
    $("#reg_form").submit(function(e){
        if(!$(this).validate()){
            return false;
        }
        var index = "";
        realname = $("#realname").val();
        pwd = $("#pwd").val();
        pwd2 = $("#pwd2").val();
        tel = $("#tel").val();
        company = $("#company").val();
        yzm = $("#yzm").val();

        if(realname=="" || pwd=="" || pwd2=="" || tel=="" || company=="" || yzm==""){
            layer.msg("所有栏目都为必须填，请认真填写。");
            return false;
        }
        if(pwd!=pwd2){
            layer.msg("两次输入密码不同");
            return false;
        }

        chkcode();
        if(ischeck==0){
            //layer.msg("正在检查，等一下，再提交。");
            return false;
        }

        save();




        $(this).validate();
        return false;
    });

    function chkcode(){
        yzm = $("#yzm").val();
        if(yzm.length>0) {
            //检查手机号
            $.ajax({
                url: "<?php echo site_url2("/home/reg/chkcode");?>",
                type: "get",
                dataType: "text",
                data: {yzm: yzm},
                async:false,
                timeout: 6000,
                success: function (data) {
                    if (data == 'ok') {
                        ischeck = 1;
                    }
                    else {
                        ischeck = 0;
                        layer.msg(data);
                    }
                }
            });
        }
        else{
            ischeck=0;
        }
    }

    function save(){
        $.ajax({
            url:"<?php echo site_url2("/home/reg/save");?>",
            type:"post",
            dataType:"json",
            data:$("#reg_form").serialize(),
            timeout:60000,
            beforeSend:function(){
                index = layer.load(1, {
                    shade: [0.5,'#000'] //0.1透明度的白色背景
                });
            },
            success:function(data){
                layer.close(index);
                if(data["flag"]=='1'){
                    //url = "<?php echo site_url2("home/reg_next");?>";
                    //window.location.href=url;
                    layer.msg(data["msg"]);
                    window.setTimeout("window.location.href='<?php echo site_url2("home/login/index");?>';",2000)
                }
                else{
                    layer.msg(data["msg"]);
                }

            }

        });
    }
</script>
</html>
