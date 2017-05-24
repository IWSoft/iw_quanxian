<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
    <script>
        //alert(parent.curr_guid);
    </script>
    <div class="row">
        <form id="edit_form" class="text-center">
            <input type="hidden" name="form_module_type" value="<?php echo $model["module_type"];?>"/>
            <input type="hidden" name="form_parent_guid" value="<?php echo $model["parent_guid"];?>"/>
            <input type="hidden" name="form_parent_path" value="<?php echo $model["parent_path"];?>"/>
            <input type="hidden" name="form_curr_level" value="<?php echo $model["curr_level"];?>"/>
            <input type="hidden" name="form_guid" value="<?php echo $model["guid"];?>"/>
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
                                       value="<?php echo $model["title"];?>"
                                />


                                <label for="form_collapsed">
                                    是否展开
                                </label>
                                <input type="checkbox" name="form_collapsed" value="yes" <?php echo !$model["collapsed"]?"checked":"";?> class="js-switch"  />
                                <script>
                                    var elem = document.querySelector('.js-switch');
                                    var init = new Switchery(elem);
                                </script>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_sort">
                                    排序（小靠前）
                                </label>
                            </div>
                            <div class="form-group form-inline">
                                <input type="text" name="form_sort"  value="<?php echo $model["sort"];?>" class="dial m-r-sm" data-fgcolor="#1AB394"
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
                                    <input type="radio" <?php echo $model["module_type"]=="10"?"checked":"";?>  name="form_module_type" id="radio1" value="10"/>
                                    <label for="radio1">菜单</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" <?php echo $model["module_type"]=="20"?"checked":"";?>  id="radio2"  value="20"/>
                                    <label for="radio2">顶部按钮</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" <?php echo $model["module_type"]=="30"?"checked":"";?> id="radio3"  value="30"/>
                                    <label for="radio3">表单按钮</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" <?php echo $model["module_type"]=="40"?"checked":"";?> id="radio4"  value="40"/>
                                    <label for="radio4">字段</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" <?php echo $model["module_type"]=="50"?"checked":"";?> id="radio5" value="50"/>
                                    <label for="radio5">列表按钮</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_module_type" id="radio6" <?php echo $model["module_type"]=="60"?"checked":"";?>  value="60"/>
                                    <label for="radio6">控制类方法</label>
                                </div>
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    链　接
                                </label>
                                <input type="text"
                                       name="form_url"
                                       value="<?php echo $model["url"];?>"
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
                                    value="<?php echo $model["controller"];?>"
                                    class="form-control"
                                    maxlength="250"
                                    value="<?php echo $model["controller"];?>"
                                />
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    方法名
                                </label>
                                <input type="text" name="form_method" value="<?php echo $model["method"];?>" class="form-control" maxlength="250"

                                />
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_title">
                                    网址参数
                                </label>
                                <input type="text" name="form_param" placeholder="(不用填?号)" value="<?php echo $model["param"];?>"
                                       class="form-control" maxlength="250" />
                            </div>

                            <div class="form-group form-inline">
                                <label for="form_url_target">
                                   打开方式
                                </label>

                                <div class="radio radio-primary">
                                    <input type="radio" <?php echo $model["url_target"]=="_blank"?"checked":"";?>  name="form_url_target" id="radio_url_target1" value="_blank"/>
                                    <label for="radio_url_target1">新TAB窗口打开</label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_url_target" <?php echo $model["url_target"]=="_self"?"checked":"";?>  id="radio_url_target2"  value="_self"/>
                                    <label for="radio_url_target2">当前页面 </label>
                                </div>
                                <div class="radio radio-primary">
                                    <input type="radio"  name="form_url_target" <?php echo $model["url_target"]=="_layerbox"?"checked":"";?> id="radio_url_target3"  value="_layerbox"/>
                                    <label for="radio_url_target3">弹层</label>
                                </div>

                            </div>

                            <div class="form-group form-inline">

                                <label for="form_beizhu">
                                   备注　　
                                </label>
                                <input type="text" name="form_beizhu" value="<?php echo $model["beizhu"];?>" placeholder="备忘或解释类文字填入" class="form-control" maxlength="250" />
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
                            <script>


                                function selbtn(obj){
                                    $('#form_dd_icon').val($(obj).attr('val'));
                                    $(".onsel").removeClass("onsel btn-primary").addClass("btn-white");
                                    $(obj).removeClass("btn-white").addClass("btn-primary onsel");
                                }
                                </script>
                            <input type="hidden" name="form_dd_icon" id="form_dd_icon" value=""/>
                        <?php
                            foreach ($icon_list as $k => $v) {
                                //btn-primary
                                echo '<button id="icon_'.$v["guid"].'" type="button" val="'.$v["val"].'" onclick="selbtn(this)"   style="margin-right: 3px ;" class="btn  '.($v["val"]==$model["dd_icon"]?"btn-primary onsel":"btn-white").' btn-xs col-md-2">';
                                echo '<i class="' . $v["val"] . '"></i>';
                                echo " ";
                                echo $v["title"];
                                echo '</button>';
                                if($v["val"]==$model["dd_icon"]){
                                    echo '<script>selbtn($("#icon_'.$v["guid"].'"));</script>';
                                }
                            }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>按钮标识(开发人员使用)</h5>
                    </div>
                    <div class="ibox-content">
                        <input type="text"  value="<?php echo $model["guid"];?>" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-default" id="btn_post"
                        btn="66b1a531-910b-4740-bb55-f4b110dbf730">
                    <?php echo lang("btn_post"); ?>
                </button>
            </div>
        </form>
    </div>

    <script>




        function show_null_msg() {
            $("#err_box").show();
            $("#edit_form").hide();
        }

        function show_edit_box() {
            $("#err_box").hide();
            $("#edit_form").show();
        }

        function postform() {
            var option={
                rules: {
                    form_title: "required"
                }
            };
            $("#edit_form").validate(option);
            index = "";

            if(!$("#edit_form").valid()){
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
                    },
                    success: function (data) {
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
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>