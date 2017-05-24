<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
    <script>
        //alert(parent.curr_guid);
    </script>
    <div class="row">
        <form id="edit_form" class="text-center">
            <input type="hidden" name="form_module_type" value="10"/>
            <input type="hidden" name="form_parent_guid" value="<?php echo $parent_guid;?>"/>
            <input type="hidden" name="form_parent_path" value=""/>
            <input type="hidden" name="form_curr_level" value="0"/>
            <input type="hidden" name="form_curr_level" value="0"/>
            <input type="hidden" name="form_controller" value=""/>
            <input type="hidden" name="form_method" value=""/>
            <input type="hidden" name="form_url_target" value="_self"/>
            <div class="col-md-6" id="demo_box">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo lang("iw_main_sys_module_add"); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div id="edit_box">

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    *菜单名称
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_title"
                                />


                                <label for="form_collapsed">
                                    是否展开
                                </label>
                                <input type="checkbox" name="form_collapsed" value="yes" checked class="js-switch"/>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_sort">
                                    排序（小靠前）
                                </label>
                            </div>


                            <div class="form-group form-inline">
                                <input type="text" name="form_sort" value="100" class="dial m-r-sm"
                                       data-fgcolor="#1AB394"
                                       data-min="0" data-max="1000"
                                       data-width="85" data-height="85"/>
                                <script>
                                    $(".dial").knob();
                                </script>
                            </div>


                            <div class="form-group form-inline">
                                <label for="form_title">
                                    按钮类型
                                </label>
                                <div class="radio radio-primary">
                                    <input type="radio" checked   name="form_module_type" id="radio1" value="10"/>
                                    <label for="radio1">菜单</label>
                                </div>
                                <div class="radio radio-primary">
                                <input type="radio"  name="form_module_type"  id="radio2"  value="20"/>
                                    <label for="radio2">顶部按钮</label>
                                </div>
                                <div class="radio radio-primary">
                                <input type="radio"  name="form_module_type" id="radio3"  value="30"/>
                                    <label for="radio3">表单按钮</label>
                                    </div>
                                <div class="radio radio-primary">
                                <input type="radio"  name="form_module_type" id="radio4"  value="40"/>
                                    <label for="radio4">字段</label>
                                </div>
                                <div class="radio radio-primary">
                                <input type="radio"  name="form_module_type" id="radio5" value="50"/>
                                    <label for="radio5">列表按钮</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" id="radio6" value="60"/>
                                    <label for="radio6">控制类方法</label>
                                </div>
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    链　接
                                </label>
                                <input type="text"
                                       name="form_url"
                                       value=""
                                       class="form-control"
                                       maxlength="250"
                                       placeholder="链接比控制器优先处理"
                                />
                            </div>
                            <div class="form-group form-inline">
                                <label for="form_title">
                                    控制器
                                </label>
                                <input
                                    type="text"
                                    name="form_controller"
                                    value="<?php echo isset($parent_model["controller"])?$parent_model["controller"]:"";?>"
                                    class="form-control"
                                    maxlength="250"
                                />
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    方法名
                                </label>
                                <input type="text" name="form_method" value="" class="form-control" maxlength="250"/>
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    网址参数
                                </label>
                                <input type="text" name="form_param" placeholder="(不用填?号)" value=""
                                class="form-control" maxlength="250" />
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_url_target">
                                    打开方式
                                </label>

                                <div class="radio radio-primary">
                                    <input type="radio"  checked  name="form_url_target" id="radio_url_target1" value="_blank"/>
                                    <label for="radio_url_target1">新TAB窗口打开</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_url_target"   id="radio_url_target2"  value="_self"/>
                                    <label for="radio_url_target2">当前页面 </label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_url_target"  id="radio_url_target3"  value="_layerbox"/>
                                    <label for="radio_url_target3">弹层</label>
                                </div>

                            </div>

                            <div class="form-group form-inline">



                                <label for="form_beizhu">
                                    备注　　
                                </label>
                                <input type="text" name="form_beizhu" placeholder="备忘或解释类文字填入" class="form-control"
                                       maxlength="250"/>
                            </div>



                        </div>


                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo lang("iw_main_sys_module_sel_icon"); ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div style="height:231px; overflow: auto;">
                            <input type="hidden" name="form_dd_icon" id="form_dd_icon" value=""/>
                            <?php
                            foreach ($icon_list as $k => $v) {
                                //btn-primary
                                echo '<button type="button" val="' . $v["val"] . '" onclick="selbtn(this)" style="margin-right: 3px ;" class=" btn btn-white btn-xs col-md-2">';
                                echo '<i class="' . $v["val"] . '"></i>';
                                echo " ";
                                echo $v["title"];
                                echo '</button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-default" id="btn_post"
                        btn="f9b27904-626a-479c-b88c-707be137cd8a">
                    <?php echo lang("btn_post"); ?>
                </button>
            </div>
        </form>
    </div>

    <script>


        function selbtn(obj) {
            $('#form_dd_icon').val($(obj).attr('val'));
            $(".onsel").removeClass("onsel btn-default").addClass("btn-white");
            $(obj).removeClass("btn-white").addClass("btn-default onsel");
        }

        function show_null_msg() {
            $("#err_box").show();
            $("#edit_form").hide();
        }

        function show_edit_box() {
            $("#err_box").hide();
            $("#edit_form").show();
        }

        function postform() {
            var option = {
                rules: {
                    form_title: "required"
                }
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
                        //alert(a);
                        alert(JSON.stringify(b));
                    },
                    success: function (data) {
                        //alert(":aaaa="+data);
                        my_ok_v2(data);
                        $("#btn_post").removeAttr("disabled");

                    },
                    beforeSend: function () {
                        $("#btn_post").attr("disabled", "disabled");
                        /*
                         index = layer.load(1, {
                         shade: [0.5,'#000'] //0.1透明度的白色背景
                         });
                         */
                    },

                });
            }
            return false;
        }


        $("#edit_form").submit(function () {
            return postform();
        });


    </script>

    <script>
        var elem = document.querySelector('.js-switch');
        var init = new Switchery(elem);

        $(document).ready(function(){
            $('input').iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square',
                increaseArea: '20%' // optional
            });
        });
    </script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>