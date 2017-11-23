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
                        <h5>新增</h5>
                    </div>


                    <div class="ibox-content">


                        <div class="form-group form-inline">
                            <label>
                                *用 户 名
                            </label>
                            <input type="text" class="form-control" maxlength="50" name="form_username"  />
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                *密　　码
                            </label>
                            <input type="password" class="form-control" maxlength="50" name="form_pwd" id="form_pwd" />
                         </div>
                        <div class="form-group form-inline">
                            <label>
                                *确认密码
                            </label>
                            <input type="password" class="form-control" maxlength="50" name="pwd2" />
                            </div>

                        <div class="form-group form-inline">
                            <label>
                                *姓　　名
                            </label>
                            <input type="text" class="form-control" maxlength="10" id="form_realname" name="form_realname" value=""/>
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                *手 机 号
                            </label>
                            <input type="text" class="form-control" maxlength="11" name="form_tel"/>
                        </div>
                        <div class="form-group form-inline">
                            <label>
                                电子邮箱
                            </label>
                            <input type="text" class="form-control" maxlength="100" name="form_email" />
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                卡号(有卡号代表已办卡)
                            </label>
                            <input type="text" class="form-control" maxlength="10" name="form_card_no" />
                        </div>

                        <div class="form-group form-inline">
                        <label>*角　　色</label>
                            <?php
                            foreach($role as $v){
                                echo '<div class="checkbox checkbox-primary">';
                                echo '<input type="checkbox" ';
                                echo "name='role_id[]' value='".$v["guid"]."'";
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
                                echo "<option value='".$v["guid"]."'>".$v["name"]."</option>\n";
                            }
                            echo "</select>";
                            ?>
                        </div>

                    </div>


                </div>
            </div>

            <div class="col-md-12">
                <input type="submit" class="btn btn-default" id="btn_post"
                       btn="100c210a-6721-9b57-8a5c-80bacef8a865"/>
            </div>
        </form>
    </div>

    <script>
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
                form_username:{required:true,minlength:6,maxlength:20,checkPwd:true,remote: "<?php echo site_url2('check_username');?>"},
                form_pwd: {required:true,minlength:6,maxlength:20,checkPwd:true},
                pwd2:{required:true,equalTo:"#form_pwd",checkPwd:true},
                form_realname:{required:true,maxlength:10},
                form_tel:{required:true,digits:true,maxlength:11,remote: "<?php echo site_url2('check_tel');?>"},
                form_email:{required:false,email:true,remote: "<?php echo site_url2('check_email');?>"},
                form_card_no:{required:false,remote: "<?php echo site_url2('check_card_no');?>"}
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