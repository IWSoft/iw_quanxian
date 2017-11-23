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
        <form id="edit_form" onsubmit="return postform();" class="text-center">

            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>新增</h5>
                    </div>


                    <div class="ibox-content">


                        <div class="form-group form-inline">
                            <label>
                                *单位简称
                            </label>
                            <input type="text" class="form-control" maxlength="50" name="form_name"  />
                        </div>

                        <div class="form-group form-inline">
                            <label>
                                单位全称
                            </label>
                            <input type="text" class="form-control" maxlength="50" name="form_fullname"  />
                         </div>
                        <div class="form-group form-inline">
                            <label>
                                *订餐权限
                            </label>
                            <?php
                            foreach($quanxian_list as $v){
                                echo '<div class="checkbox checkbox-primary">';
                                echo '<input type="checkbox" ';
                                echo "name='quanxian_guid[]' value='".$v["guid"]."'";
                                echo '/>';
                                echo '<label>';
                                echo $v["title"];
                                echo '</label>';
                                echo '</div>';
                            }
                            ?>
                            </div>


                    </div>


                </div>
            </div>

            <div class="col-md-12">
                <input type="submit" class="btn btn-default" id="btn_post"
                       btn="d5f8c94a-e870-b0da-1b24-ecc3d9e5ead9"/>
            </div>
        </form>
    </div>

    <script>
        $.validator.setDefaults({
            submitHandler: function () {


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
                form_name:{required:true,minlength:2,maxlength:60,remote: "<?php echo site_url2('check_name');?>"},
                form_fullname:{required:false,minlength:2,maxlength:60,remote: "<?php echo site_url2('check_fullname');?>"},
                dingcan_start:{required:true,date:true,maxlength:10},
                dingcan_end:{required:true,date:true,maxlength:10}
            },

            messages: {
                form_name: {
                    remote: '简称重复'
                },
                form_fullname: {
                    remote: "全称重复"
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

        $(".input-daterange").datepicker({keyboardNavigation:!1,forceParse:!1,autoclose:!0});
    </script>


<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>