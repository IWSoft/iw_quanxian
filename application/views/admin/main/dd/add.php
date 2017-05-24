<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<script>
    //alert(parent.curr_guid);
</script>
    <div class="row">
        <?php echo  helper_alert_warning_msg(lang("iw_main_dd_add_warning"));?>
        <form id="edit_form" class="text-center">
            <div class="col-md-3" id="demo_box">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo lang("iw_main_dd_edit"); ?></h5>
                    </div>
                    <div class="ibox-content">

                        <div id="edit_box">

                            <div class="form-group form-inline">

                                <label for="parent_id">
                                    上级编号
                                </label>
                                <input type="text" class="form-control" onkeyup="setid(this)" maxlength="6"
                                       name="parent_id[]"
                                       value="<?php echo $parent_id==""?"N/A":$parent_id;?>"/>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_title">
                                    名　　称
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_title[]"
                                />
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_fulltitle">
                                    全　　称
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_fulltitle[]"
                                />
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_beizhu">
                                    说　　明
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu[]"
                                />
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_can_del">
                                    能删除
                                </label>
                                <select name="form_can_del[]">
                                    <option value="1">可删</option>
                                    <option value="0">禁删</option>
                                </select>
                            </div>
                            <div class="form-group form-inline tooltip-demo">

                                <label for="form_val" >
                                    值　　
                                </label>
                                <input type="text" class="form-control" maxlength="250" id="form_val1" name="form_val[]"/>
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-windows" data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$(this).parent().prev().prev().prev().val('".$v["val"]."');$(this).parent().toggle('normal')\" style='float:left;width:80px;height:25px;position:static; cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$(this).parent().prev().prev().prev().prev().val('".$v."');$(this).parent().toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_beizhu2">
                                    说明(二)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu2[]"
                                />
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_val2">
                                    值(二)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_val2[]"/>
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-windows" data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$(this).parent().prev().prev().prev().val('".$v["val"]."');$(this).parent().toggle('normal')\" style='float:left;width:80px;height:25px;position:static;cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$(this).parent().prev().prev().prev().prev().val('".$v."');$(this).parent().toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>

                            </div>
                            <div class="form-group form-inline">

                                <label for="form_beizhu2">
                                    说明(三)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu3[]"
                                />
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_val3">
                                    值(三)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_val3[]"
                                />
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$(this).next().next().toggle('normal');" class="fa fa-windows" data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$(this).parent().prev().prev().prev().val('".$v["val"]."');$(this).parent().toggle('normal')\" style='float:left;width:80px;height:25px;position:static;cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                                <div  style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$(this).parent().prev().prev().prev().prev().val('".$v."');$(this).parent().toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <button type="submit" class="btn btn-default" id="btn_post"
                        btn="0be51846-d36f-43c3-9eae-585344d7fb39">
                    <?php echo lang("btn_post"); ?>
                </button>
            </div>
        </form>
    </div>

    <script>
        function add_box() {
            demo = $("#demo_box").html();
            //html = $("#edit_form").html();
            html = '<div class="col-md-3">' + demo + '</div>';
            $("#demo_box").after(html);
            //$("#edit_form").html(html);
        }

        for (i = 0; i < 6; i++) {
            add_box();
        }

        function setid(obj) {
            i = 0;
            $("input[name='parent_id[]']").each(function () {
                //$(this).val(obj.val());
                //$(this).attr("id",$(this).attr("name")+(i++));
                //alert(i);
                if ($(this) != obj) {
                    $(this).val(obj.value);
                }
            });
        }

        function show_null_msg() {
            $("#err_box").show();
            $("#edit_form").hide();
        }

        function show_edit_box() {
            $("#err_box").hide();
            $("#edit_form").show();
        }
        var field = new Array(
            "title",
            "fulltitle",
            "val",
            "beizhu",
            "val2",
            "beizhu2",
            "val3",
            "beizhu3",
            "guid"
        );


        function postform() {

            //alert($("#edit_form").valid());
            i = 0;
            $("input[name='form_title[]']").each(function () {
                title = $(this).val();
                title = title.replace(/\s/ig, "");
                if (title != "") {
                    i++;
                }
            });
            if (i == 0) {
                my_err_alert("<?php echo lang("err_name_null");?>", 3);
                return false;
            }

            index = "";
            $.ajax({
                type: "post",
                dataType: "json",
                url: "<?php echo site_url2("batch_save");?>",
                data: $("#edit_form").serialize(),
                error: function (a, b, c) {
                    //alert(a);
                },
                success: function (data) {
                    err = data["err"];

                    if (err == "ok") {
                        //my_ok_alert(data["msg"], 5);
                        //window.setTimeout("my_close_box()",6000);
                        my_ok(data["msg"],"<?php echo site_url2("add");?>","3");
                    }
                    else {
                        my_err_alert(data["msg"], 15);
                    }
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


            return false;
        }


        $("#edit_form").submit(function () {
            return postform();
        });


    </script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>