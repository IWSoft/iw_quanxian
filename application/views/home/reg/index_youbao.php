<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?php echo base_url();?>"/>
    <title>注册-<?php echo $system_name;?></title>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="keywords" content="微信订餐平台">
    <link rel="stylesheet" href="static/theme/youbao/css/style.css">
</head>
<body>
<header>
    <h1>注册会员</h1>
</header>
<div class="login_content page">
    <img src="static/theme/youbao/images/entry.png" alt="" class="entry">
            <form id="reg_form" method="post">
                <input type="hidden" name="backurl" id="backurl" value="<?php echo $url;?>"/>

                <label for="">
                    <input type="text" name="realname"  id="realname" minlength="2" maxlength="10" type="text" required class="login_name" placeholder="输入姓名" />
                </label>
                <label for="">
                <select name="company" id="company" class="login_name m-b" required style=" height: 40px;color:#000;">
                    <option value="">选择所在单位</option>
                    <?php
                    foreach($company as $v){
                        echo "<option value='".$v["guid"]."'>".$v["name"]."</option>";
                    }
                    ?>
                </select>
                </label>
                <label for="">
                <input type="text" style="color:#000;" name="tel" id="tel" maxlength="11" type="text" required class="login_name" placeholder="输入11位手机号" />
                </label>
                <label for="">
                <input type="password" style="color:#000;" name="pwd" id="pwd" maxlength="20" type="text" required class="login_name" placeholder="密码(数字或字母)" />
                </label>
                <label for="">
                <input type="password" style="color:#000;" name="pwd2" id="pwd2" maxlength="20" type="text" required class="login_name" placeholder="确认密码(数字或字母)" />
                </label>
                <label>
                <input type="text" style="color:#000;"   maxlength="4" required name="yzm" id="yzm" class="login_name" value="" placeholder="请输入验证码"/>
                </label>
                <label>
                <img style="float:left;cursor: pointer;" width="100" height="30" src="<?php echo site_url2("home/code");?>" id="yzm_img" onclick="$('#yzm_img').attr('src','<?php echo site_url2("home/code");?>?rnd='+Math.random());"/>点击图片刷新验证码
                </label>
                <button class="btn" type="submit">提交注册</button>
                <button class="btn02" type="button" onclick="window.location.href='<?php echo site_url2('home/login/index');?>'">登录系统</button>
            </form>
</div>
<script src="static/js/jquery.min.js?v=2.1.4"></script>
<script src="static/js/plugins/layer/layer.min.js"></script></div>
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
</body>
</html>

