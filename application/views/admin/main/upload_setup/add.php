<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>

<div class="row">
    <form class="form-inline" id="edit_form">

        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>上传附件设置</h5>
                </div>
                <div class="ibox-content">

                    <input type="hidden" name="form_guid" value=""/>

                    <div class="form-group form-inline">
                        <label>编号</label>
                        <input type="text" style="width: 80px;" name="form_id" value="<?php echo $newid; ?>"
                               maxlength="6" class="form-control"/>
                    </div>

                    <div class="form-group form-inline">
                        <label>上传主题</label>
                        <input type="text" name="form_title" class="form-control" value="" maxlength="250"/>
                    </div>

                    <div class="form-group form-inline">
                        <label>
                            每次上传数量
                        </label>
                        <input type="text" name="form_upload_count" class="form-control" style="width: 60px;" value="1"
                               maxlength="4"/>
                    </div>
                    <div class="form-group form-inline">
                        <label>
                            是否能删
                        </label>
                        <input type="checkbox" name="form_can_del" value="yes" class="js-switch2"/>

                    </div>


                    <div class="form-group  form-inline">
                        <label>上传文件大小</label>
                        <input type="text" name="form_filesize" value="<?php echo $filesize; ?>" maxlength="3"
                               style="width: 60px;" class="form-control"/>M
                    </div>

                    <div class="form-group  form-inline tooltip-demo">
                        <div type="button" data-toggle="tooltip" data-placement="top" class="fa fa-question-circle"
                             title="图处预处理：用于图片上传之前压缩成指定尺寸再上传，优化上传速度。"></div>
                        <label>图处预处理</label>
                        <div class="radio radio-primary">
                            <input type="radio" name="form_yuchuli" value="1"/><label>是</label>
                        </div>
                        <div class="radio radio-primary">
                            <input type="radio" name="form_yuchuli" value="0" checked /><label>否</label>
                        </div>
                    </div>
                    <div class="form-group  form-inline">
                        <label>预处理图片尺寸</label>
                        <input type="text" maxlength="4" name="form_yuchuli_width" value="1600" style="width:60px;" placeholder="宽度"/>
                        <input type="text" maxlength="4" name="form_yuchuli_height" value="1200" style="width:60px;" placeholder="高度"/>
                    </div>


                    <div class="form-group form-inline tooltip-demo ">
                        <div type="button" data-toggle="tooltip" data-placement="top" class="fa fa-question-circle"
                             title="处理方案：用于上传附件时的附加处理，如：上传图片，会生成缩略图。"></div>
                        <div class="form-group form-inline">
                            <label>处理方案</label>
                            <div class="radio radio-primary">
                                <input type="radio" name="form_dd_fangan" checked value=""/>
                                <label>不处理</label>
                            </div>
                            <?php
                            foreach ($dd_fangan as $v) {
                                echo '<div class="radio radio-primary">';
                                echo '<input type="radio"  name="form_dd_fangan" value="' . $v["guid"] . '"/>';
                                echo '<label>';
                                echo $v["title"];
                                echo '</label>';
                                echo '</div>';

                            }
                            ?>
                        </div>


                        <div class="form-group form-inline">
                            <input type="submit" btn="c4d49bec-1847-b120-1c7b-6d74dc2b86e9" id="btn_post"/>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>允许上传的文件类型</h5>
                    </div>
                    <div class="ibox-content tooltip-demo">
                        <div style="height:231px; overflow: auto;">
                            <script>
                                function selbtn(obj) {
                                    //$('#form_dd_icon').val($(obj).attr('val'));

                                    //$(".onsel").removeClass("onsel btn-primary").addClass("btn-white");

                                    if ($(obj).hasClass("btn-white")) {
                                        $(obj).removeClass("btn-white").addClass("btn-primary onsel");
                                    }
                                    else {
                                        $(obj).removeClass("btn-primary").removeClass("onsel").addClass("btn-white");

                                    }

                                    getfiletype();
                                }

                                function getfiletype() {
                                    val = "";
                                    $(".onsel").each(function () {
                                        if (val == "") {
                                            val = $(this).attr("val");
                                        }
                                        else {
                                            val += "|" + $(this).attr("val");
                                        }
                                    });
                                    $("#form_filetype").val(val);
                                }
                            </script>
                            <input type="hidden" name="form_filetype" id="form_filetype" value=""/>
                            <?php
                            foreach ($filetype as $k => $v) {
                                //btn-primary
                                echo '<button id="icon_' . $v["guid"] . '" type="button" val="' . $v["val"] . '" onclick="selbtn(this)"   style="margin-right: 3px ;" class="btn btn-white btn-xs col-md-1">';
                                echo $v["title"];
                                echo '</button>';
                            }
                            ?>
                        </div>

                    </div>
                </div>
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
                    //alert(a);
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
            form_id: {digits: true, required: true, remote: "<?php echo site_url2('checkid');?>"},
            form_title: "required",
            form_upload_count: {digits: true, maxlength: 3},
            form_filesize: {digits: true, maxlength: 3},
            form_width:{digits: true},
            form_height:{digits: true}
        },
        messages: {
            form_id: {
                remote: 'ID已存在，请填其他'
            }
        }
    };
    $("#edit_form").validate(option);


    /*
     var elem = document.querySelector('.js-switch');
     var init = new Switchery(elem);
     */
    var elem2 = document.querySelector('.js-switch2');
    var init2 = new Switchery(elem2);
</script>
<script src="static/js/plugins/iCheck/icheck.min.js"></script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>

