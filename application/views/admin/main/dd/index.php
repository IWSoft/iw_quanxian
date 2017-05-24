<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>

    <div class="row m-b-sm m-t-sm">
        <div class="col-md-1">
            <button type="button" onclick="return reload_page($(this))" id="loading-example-btn"
                    class="btn btn-white btn-sm"><i class="fa fa-refresh"></i> 刷新
            </button>
        </div>
        <div class="col-md-11">
            <div class="input-group">
                <input type="text" placeholder="请输入项目名称" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-primary"> 搜索</button> </span>
            </div>
        </div>
    </div>

    <div class="row">
        <?php //放内容 ?>
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo lang("iw_main_dd_tree"); ?></h5>
                </div>
                <div class="ibox-content">
                    <div id="left_menu_dd">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo lang("iw_main_dd_edit"); ?></h5>
                </div>
                <div class="ibox-content">
                    <div id="edit_box">
                        <p class="text-center" id="err_box"><?php echo lang("err_no_select"); ?></p>
                        <form id="edit_form" class="text-center" style="display: none;">
                            <input type="hidden" name="form_guid" id="form_guid" value=""/>
                            <div class="form-group form-inline">

                                <label for="parent_id">
                                    上级编号
                                </label>
                                <input type="text" class="form-control" maxlength="6" id="parent_id" name="parent_id"
                                       value="N/A"/>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_title">
                                    名　　称
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_title"
                                       id="form_title"/>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_fulltitle">
                                    全　　称
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_fulltitle"
                                       id="form_fulltitle"/>
                            </div>
                            <div class="form-group form-inline">

                                <label for="form_beizhu">
                                    说　　明
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu"
                                       id="form_beizhu"/>
                            </div>

                            <div class="form-group form-inline tooltip-demo">

                                <label for="form_val">
                                    值　　　
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_val" id="form_val"/>
                                <a href="javascript:void(0);" onclick="$('#showicon').toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$('#showcolor').toggle('normal');"  class="fa fa-windows"   data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <a href="javascript:void(0);" onclick="my_open_box({title:'上传',width:'80%',height:'80%',url:'<?php echo site_url2("upload_file/add")."?boxid=form_val&guid=b71f952b-a33b-2f15-befa-d2c0e84d9d3e";?>'});" class="fa fa-upload" data-toggle="tooltip" data-placement="top" title="上传附件"></a>
                                <div id="showicon" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$('#form_val').val('".$v["val"]."');$('#showicon').toggle('normal')\" style='float:left;width:80px;height:25px;position:static;cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                                <div id="showcolor" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$('#form_val').val('".$v."');$('#showcolor').toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="form-group form-inline ">

                                <label for="form_beizhu2">
                                    说明(二)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu2" id="form_beizhu2"/>
                            </div>

                            <div class="form-group form-inline tooltip-demo">

                                <label for="form_val2">
                                    值(二)　
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_val2"
                                       id="form_val2"/>

                                <a href="javascript:void(0);" onclick="$('#showicon2').toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$('#showcolor2').toggle('normal');"  class="fa fa-windows"   data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <a href="javascript:void(0);" onclick="my_open_box({title:'上传',width:'80%',height:'80%',url:'<?php echo site_url2("upload_file/add")."?boxid=form_val2&guid=b71f952b-a33b-2f15-befa-d2c0e84d9d3e";?>'});" class="fa fa-upload" data-toggle="tooltip" data-placement="top" title="上传附件"></a>
                                <div id="showicon2" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$('#form_val2').val('".$v["val"]."');$('#showicon2').toggle('normal')\" style='float:left;width:80px;height:25px;position:static;cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>

                                <div id="showcolor2" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$('#form_val2').val('".$v."');$('#showcolor2').toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>
                            </div>


                            <div class="form-group form-inline ">

                                <label for="form_beizhu2">
                                    说明(三)
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_beizhu3"
                                       id="form_beizhu3"/>
                            </div>

                            <div class="form-group form-inline tooltip-demo">

                                <label for="form_val2">
                                    值(三)　
                                </label>
                                <input type="text" class="form-control" maxlength="250" name="form_val3"
                                       id="form_val3"/>
                                <a href="javascript:void(0);" onclick="$('#showicon3').toggle('normal');" class="fa fa-mouse-pointer" data-toggle="tooltip" data-placement="top" title="选择图标"></a>
                                <a href="javascript:void(0);" onclick="$('#showcolor3').toggle('normal');"  class="fa fa-windows"   data-toggle="tooltip" data-placement="top" title="选择颜色"></a>
                                <a href="javascript:void(0);" onclick="my_open_box({title:'上传',width:'80%',height:'80%',url:'<?php echo site_url2("upload_file/add")."?boxid=form_val3&guid=b71f952b-a33b-2f15-befa-d2c0e84d9d3e";?>'});" class="fa fa-upload" data-toggle="tooltip" data-placement="top" title="上传附件"></a>
                                <div id="showicon3" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($icon as $v){
                                        echo "<div onclick=\"$('#form_val3').val('".$v["val"]."');$('#showicon3').toggle('normal')\" style='float:left;width:80px;height:25px;position:static;cursor:pointer; padding:3px; margin:3px;border: 1px solid #c1e2b3'>";
                                        echo "<i class='";
                                        echo $v["val"];
                                        echo "'>";
                                        echo "</i>";
                                        echo $v["title"];
                                        echo "</div>";
                                    }
                                    ?>
                                </div>

                                <div id="showcolor3" style="padding:3px;margin:3px;border:1px solid #ff0000; height: 100px; overflow: auto; display: none;">
                                    <?php
                                    foreach($this->config->item("def_btn_color_arr") as $v){
                                        echo "<button style='margin:3px;' type='button' onclick=\"$('#form_val3').val('".$v."');$('#showcolor3').toggle('normal')\" class='btn ".$v."'>";
                                        echo $v;
                                        echo "</button>";
                                    }
                                    ?>
                                </div>
                            </div>


                            <button type="submit" class="btn btn-default" id="btn_post"
                                    btn="0222c779-c607-4f91-9222-065ab968b6b7">
                                <?php echo lang("btn_post"); ?>
                            </button>

                        </form>

                    </div>

                </div>
            </div>


        </div>
    </div>

    <script>
        var curr_guid = "";
        $('#left_menu_dd').jstree({
            'core': {
                'data': {
                    'url': '<?php echo site_url2("getnode");?>',
                    'data': function (node) {
                        return {'id': node.id};
                    }
                },
                'check_callback': true
                , 'themes': {
                    'responsive': false,
                    'variant': 'small',
                    'stripes': true
                }
            },

            "types": {"default": {"icon": "fa fa-folder"}}
            ,
            "plugins": ["types"]

        }).on('changed.jstree', function (e, data) {
            if (data && data.selected && data.selected.length) {
                guid = data.selected.join(':');
                curr_guid = guid;
                //将GUID传给顶部按钮
                $("#top_menu_b5a4b24b09b84b17b40bb7d57ca30ed3").attr("url","<?php echo site_url2("add")."?parent_guid=";?>"+curr_guid);
                //$("#top_menu_b5a4b24b09b84b17b40bb7d57ca30ed3").attr("url","");
                getform(guid);
            }
        });

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

        var opt = {
            errorPlacement: function (error, element) {
                error.appendTo(element.parent());
            },
            rules: {
                form_id: {
                    required: true,
                    minlength: 1,
                    maxlength: 6
                },
                form_title: {
                    required: true,
                    minlength: 1,
                    maxlength: 250
                },
                form_fulltitle: {
                    required: false,
                    minlength: 1,
                    maxlength: 250
                },
                form_beizhu: {
                    required: false,
                    maxlength: 250
                },
                form_val: {
                    required: false,
                    maxlength: 250
                },
                form_beizhu2: {
                    required: false,
                    maxlength: 250
                },
                form_val2: {
                    required: false,
                    maxlength: 250
                },
                form_beizhu3: {
                    required: false,
                    maxlength: 250
                },
                form_val3: {
                    required: false,
                    maxlength: 250
                }
            }
        };
        var form_validate = $("#edit_form").validate(opt);

        function resetform() {
            form_validate.resetForm();
        }
        function postform() {
            //alert($("#edit_form").valid());
            if ($("#edit_form").valid()) {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "<?php echo site_url2("save");?>",
                    data: $("#edit_form").serialize(),
                    error: function (a, b, c) {
                        //alert(a);
                    },
                    success: function (data) {
                        err = data["err"];
                        if (err == "ok") {
                            my_ok_alert(data["msg"], 5);
                        }
                        else {
                            my_err_alert(data["msg"], 15);
                        }
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
            postform();
            return false;
        });


        function getform(guid) {

            $.ajax({
                type: "get",
                url: "<?php echo site_url2("getmodel");?>",
                data: {guid: guid},
                dataType: "json",
                error: function (a, b, c) {
                    alert(b);
                },
                beforeSend: function () {
                    resetform();
                    show_edit_box();
                    $("#btn_post").attr("disabled", "disabled");

                },
                success: function (data) {
                    err = data["err"];
                    model = data["model"];
                    if (err == "ok") {

                        for (i in field) {
                            $("#form_" + field[i]).val(eval('model.' + field[i]));
                        }
                        //特殊处理
                        $("#parent_id").val(model["parent_id"]);
                        $("#btn_post").removeAttr("disabled");
                    }
                    else {
                        parent.layer.alert(err);
                    }
                }
            });
        }


    </script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>