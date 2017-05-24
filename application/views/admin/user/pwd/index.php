<?php
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
                        <h5>修改密码</h5>
                    </div>


                    <div class="ibox-content">


                            <div class="form-group form-inline">
                                <label for="form_title">
                                    *新 密 码
                                </label>
                                <input type="password" class="form-control" maxlength="50" name="form_pwd"  id="form_pwd" />

                                <label for="form_title">
                                    *确认密码
                                </label>
                                <input type="password" class="form-control" maxlength="50" name="form_pwd2" />
                            </div>
                    </div>


                    </div>
                </div>

            <div class="col-md-12">
                <input type="submit" class="btn btn-default" id="btn_post"
                        btn="412cde88-7fa0-3600-fc9b-9cb7046ab8b8"/>
            </div>
        </form>
    </div>

    <script>


        function postform() {
            $.validator.addMethod("checkPwd",function(value,element,params){
                var checkPwd2 = /^\w{6,16}$/g;
                return this.optional(element)||(checkPwd2.test(value));
            },"*只允许6-16位英文字母、数字或者下画线！");

            var option = {
                rules: {
                    form_pwd: {required:true,minlength:6,maxlength:20,checkPwd:true},
                    form_pwd2:{required:true,equalTo:"#form_pwd",checkPwd:true}
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

            $("#edit_form").validate(option);
            index = "";
            if (!$("#edit_form").valid()) {
                return false;
            }
            else {
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
            return false;
        }


        $("#edit_form").submit(function () {
            return postform();
        });


    </script>


<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>