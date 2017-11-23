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
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>修改资料</h5>
                    </div>


                    <div class="ibox-content">


                        <div class="form-group form-inline">
                            <label>
                                用 户 名
                            </label>
                            <input type="text" value="<?php echo $model["username"];?>"  class="form-control" readonly/>
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
                                *电子邮箱
                            </label>
                            <input type="text"  class="form-control" maxlength="100" id="form_email" name="form_email"  value="<?php echo $model["email"];?>" />
                        </div>



                    </div>


                </div>
            </div>

            <div class="col-md-12">
                <input type="submit" class="btn btn-default" id="btn_post"
                       btn="46746fe7-6917-f542-5fdd-90775b7be801"/>
            </div>
        </form>
    </div>

    <script>
        $.validator.setDefaults({
            submitHandler: function () {


                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "<?php echo site_url2("edit_mine");?>",
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
                                return '<?php echo $guid;?>';
                            }
                        }
                    }
                },
                form_email:{required:true,email:true,
                    remote: {
                        url: "<?php echo site_url2('check_email');?>",     //后台处理程序
                        type: "get",               //数据发送方式
                        dataType: "json",           //接受数据格式
                        data: {                     //要传递的数据
                            form_username: function() {
                                return $("#form_email").val();
                            },
                            form_guid: function() {
                                return '<?php echo $guid;?>';
                            }
                        }
                    }
                }
            },

            messages: {
                form_tel: {
                    remote: "手机号重复"
                }
                ,
                form_email:{
                    remote:"邮箱重复"
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