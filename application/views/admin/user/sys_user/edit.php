<?php
/**
 * Created by Hello,Web.
 * QQ: 4650566
 * 希望您能尊重劳动成果，把我留下^_^
 * Date: 2017-5-16
 * Time: 20:50
 */

$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
    <script>
        //alert(parent.curr_guid);
    </script>
    <div class="row">
        <form id="edit_form" class="text-center">
<input type="hidden" name="form_guid" id="form_guid" value="<?php echo $model["guid"];?>"/>
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>修改</h5>
                    </div>


                    <div class="ibox-content">


                        <div class="form-group form-inline">
                            <label>
                                *用 户 名
                            </label>
                            <input type="text" class="form-control" maxlength="50" id="form_username" name="form_username" value="<?php echo $model["username"];?>"  />
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                密　　码
                            </label>
                            <input placeholder="不修改密码请留空" type="password" class="form-control" maxlength="50" name="form_pwd" id="form_pwd"  />
                        </div>
                        <div class="form-group form-inline">
                            <label>
                                确认密码
                            </label>
                            <input placeholder="不修改密码请留空" type="password" class="form-control" maxlength="50" name="pwd2" />
                            </div>

                        <div class="form-group form-inline">
                            <label>
                                *姓　　名
                            </label>
                            <input type="text" class="form-control" maxlength="10" id="form_realname" name="form_realname" value="<?php echo $model["realname"];?>"/>
                        </div>
                        <div class="form-group form-inline">
                            <label>
                                *手 机 号
                            </label>
                            <input type="text" class="form-control" maxlength="11" id="form_tel" name="form_tel" value="<?php echo $model["tel"];?>"/>
                        </div>
                        <div class="form-group form-inline">
                            <label>
                                电子邮箱
                            </label>
                            <input type="text"  class="form-control" maxlength="100" id="form_email" name="form_email"  value="<?php echo $model["email"];?>" />
                        </div>
                        <div class="form-group form-inline">
                            <label>
                                卡号(有卡号代表已办卡)
                            </label>
                            <input type="text" class="form-control" maxlength="10" name="form_card_no" id="form_card_no" value="<?php echo $model["card_no"];?>" />
                        </div>
                        <div class="form-group form-inline">
                        <label>*角　　色</label>
                            <?php

                            foreach($role as $v){
                                echo '<div class="checkbox checkbox-primary">';
                                echo '<input type="checkbox" ';
                                echo "name='role_id[]' value='".$v["guid"]."'";
                                echo in_array($v["guid"],$role_user)?" checked ":"";
                                echo '/>';
                                echo '<label>';
                                echo $v["title"];
                                echo '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                所属单位
                            </label>
                            <?php
                            echo "<select name='company'>\n";
                            echo "<option value=''>选择一个单位</option>";
                            foreach($company_list as $v){
                                echo "<option value='".$v["guid"]."' ".($company_guid==$v["guid"]?" selected ":"").">".$v["name"]."</option>\n";
                            }
                            echo "</select>";
                            ?>
                        </div>

                    </div>


                </div>
            </div>

            <div class="col-md-12">
                <input type="submit" class="btn btn-default" id="btn_post"
                       btn="aff873f9-067a-265b-19fd-a0de56ac803e"/>
                <?php
                if($model["check_status"]=="0") {
                    ?>
                    <input type="button" class="btn btn-default" id="btn_post_check_yes"
                           btn="9aad9555-6bf1-2400-f8bb-11e48becaa8d"/>

                    <input type="button" class="btn btn-default" id="btn_post_check_no"
                           btn="756c7c6a-62f7-c161-bb7b-cec858ff5a9d"/>
                <div class="col-md-12">
                    <div class="checkbox checkbox-primary"><input type="checkbox" checked name="sendmsg" id="sendmsg" value="yes"/><label>同时发送短信通过注册会员</label></div>
            </div>
                    <div class="col-md-12">
<input type="text" name="check_content" id="check_content"  value="" placeholder="拒绝时，请输入审核意见！" />
                    </div>
                    <?php
                }
                ?>
            </div>
        </form>
    </div>

    <script>
        $("#btn_post_check_yes").on("click",function(){
            my_layer_confirm("确认操作？",check_yes,[]);
        });

        $("#btn_post_check_no").on("click",function(){
            if($("#check_content").val()==""){
                layer.msg("拒绝时需要输入意见");
                $("#check_content").focus();
            }
            else {
                my_layer_confirm("确认操作？", check_no, []);
            }
        });

        function check_yes(){
            $.ajax({
                url:"<?php echo site_url2("check_yes")?>",
                data:{guid:"<?php echo $model["guid"];?>",check_content:$("#check_content").val(),issend:$('input:checkbox[name="sendmsg"]').is(":checked")?"yes":""},
                type:"post",
                dataType:"json",
                success:function(data){
                    my_ok_v2(data);
                }
            });
        }
        function check_no(){
            $.ajax({
                url:"<?php echo site_url2("check_no")?>",
                data:{guid:"<?php echo $model["guid"];?>",check_content:$("#check_content").val(),issend:$('input:checkbox[name="sendmsg"]').is(":checked")?"yes":""},
                type:"post",
                dataType:"json",
                success:function(data){
                    my_ok_v2(data);
                }
            });
        }

        $.validator.setDefaults({
            submitHandler: function () {
                role_list = $("input[name='role_id[]']:checked");
                if(role_list.length==0){
                    parent.layer.msg("请选择至少一个角色");
                    return false;
                }

                 $.ajax({
                 type: "post",
                 dataType: "json",
                 url: "<?php echo site_url2("save");?>",
                 data: $("#edit_form").serialize(),
                 error: function (a, b, c) {
                 alert(JSON.stringify(b));
                 },
                 success: function (data) {
                 my_ok_v2(data);
                 $("#btn_post").removeAttr("disabled");

                 },
                 beforeSend: function () {
                 $("#btn_post").attr("disabled", "disabled");
                 },

                 });

            }
        });
        var option = {
            rules: {
                form_username:{required:true,minlength:3,maxlength:20,checkUsername:true,
                    remote: {
                        url: "<?php echo site_url2('check_username');?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            form_username: function() {
                                return $("#form_username").val();
                            },
                            form_guid: function() {
                                return $("#form_guid").val();
                            }
                        }
                    }
                },
                form_pwd: {required:false,minlength:6,maxlength:20,checkPwd:true},
                pwd2:{required:false,equalTo:"#form_pwd",checkPwd:true},
                form_realname:{required:true,maxlength:10},
                form_tel:{required:true,digits:true,maxlength:11,
                    remote: {
                        url: "<?php echo site_url2('check_tel');?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            form_username: function() {
                                return $("#form_tel").val();
                            },
                            form_guid: function() {
                                return $("#form_guid").val();
                            }
                        }
                    }
                },
                form_email:{required:false,email:true,
                    remote: {
                        url: "<?php echo site_url2('check_email');?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            form_username: function() {
                                return $("#form_email").val();
                            },
                            form_guid: function() {
                                return $("#form_guid").val();
                            }
                        }
                    }
                }
                ,
                form_card_no:{required:false,
                    remote: {
                        url: "<?php echo site_url2('check_card_no');?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            form_card_no: function() {
                                return $("#form_card_no").val();
                            },
                            form_guid: function() {
                                return $("#form_guid").val();
                            }
                        }
                    }
                }
            },

            messages: {
                form_username: {
                    remote: '用户名重复'
                },
                form_tel: {
                    remote: "手机号重复"
                }
                ,
                form_email:{
                    remote:"邮箱重复"
                }
                ,
                form_card_no:{
                    remote:"卡号重复"
                }
            },
            //是否在获取焦点时验证
            onfocusout:false,
            //是否在敲击键盘时验证
            onkeyup:false,
            //提交表单后，（第一个）未通过验证的表单获得焦点
            focusInvalid:true
            //当未通过验证的元素获得焦点时，移除错误提示
            //focusCleanup:true
        };
        $.validator.addMethod("checkUsername",function(value,element,params){
            var checkPwd2 = /^\w{3,20}$/g;
            return this.optional(element)||(checkPwd2.test(value));
        },"*只允许6-20位英文字母、数字或者下画线！");

        $.validator.addMethod("checkPwd",function(value,element,params){
            var checkPwd2 = /^\w{6,20}$/g;
            return this.optional(element)||(checkPwd2.test(value));
        },"*只允许6-20位英文字母、数字或者下画线！");

        $("#edit_form").validate(option);

        function postform() {

            index = "";
            if (!$("#edit_form").valid()) {
                return false;
            }
            else {
                return false;
            }
            return false;
        }


        $("#edit_form").submit(function () {
            //return postform();
        });


    </script>


<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>