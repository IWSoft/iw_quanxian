<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>

<div class="row">
    <form class="form-inline" id="edit_form">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>基础信息</h5>
            </div>
            <div class="ibox-content">

                    <input type="hidden" name="form_guid" value="<?php echo $model["guid"];?>" />
                    <div class="form-group">
                        <label>编号</label>
                        <span class="form-control"><?php echo $model["id"]; ?></span>
                    </div>

                    <div class="form-group">
                        <label>角色名称</label>
                        <input type="text" name="form_title" class="form-control" value="<?php echo $model["title"]; ?>"
                               maxlength="250"/>
                    </div>

                    <div class="form-group">
                        <label>备忘</label>
                        <input type="text" name="form_beizhu" class="form-control"
                               value="<?php echo $model["beizhu"]; ?>" maxlength="250"/>
                    </div>

                    <div class="form-group">
                        <input type="submit" btn="2382b080-f21d-ac3e-84c5-f85bd01aa422" id="btn_post" />
                        <input type="button" name="btn_sel_all" value="全选权限" class="btn btn-default" onclick="sel_sub_guid('')"/>
                    </div>

            </div>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>权限</h5>
            </div>
            <div class="ibox-content  tooltip-demo">

                <?php
                for ($i = 0; $i < count($modules); $i++) {
                    if ($modules[$i]["curr_level"] == 0) {
                        echo '<div class="row">'."\n";
                        echo '<div class="col-sm-12">'."\n";
                        echo '<div class="checkbox checkbox-primary">'."\n";
                        echo '<input type="checkbox"';
                        echo ' parent_path="'.$modules[$i]["parent_path"].'"';
                        echo 'name="modules[]" id="module_'.$modules[$i]["guid"].'" value="'.$modules[$i]["guid"].'" '.(in_array($modules[$i]["guid"],$role_modules)?"checked":"").'>
                                        <label for="module_'.$modules[$i]["guid"].'" data-toggle="tooltip" data-placement="top" title="'.$modules[$i]["guid"].'">';
                        echo $modules[$i]["title"];
                        if($modules[$i]["module_type"]=='10'){
                            echo '<span style="color:red;">';
                        }
                        echo isset($modules_type[$modules[$i]["module_type"]])?"【{$modules_type[$modules[$i]["module_type"]]}】":"";
                        if($modules[$i]["module_type"]=='10'){
                            echo '</span>'."\n";
                        }
                        echo '</label>
                                    </div>'."\n";
                        echo "</div>\n";
                        echo "</div>\n";
                        //下级
                        for($j=0;$j<count($modules);$j++){
                            if($modules[$i]["guid"] == $modules[$j]["parent_guid"] &&  $modules[$j]["curr_level"]==1){
                                echo '<div class="row" style=" border-bottom: 1px solid #cccccc">'."\n";
                                echo '<div class="col-sm-12">'."\n";
                                echo '<div class="checkbox checkbox-primary">'."\n";
                                echo helper_str_pad2("　",$modules[$j]["curr_level"],"　");
                                echo '<input type="checkbox" ';
                                echo ' parent_path="'.$modules[$j]["parent_path"].'"';
                                echo ' name="modules[]" id="module_'.$modules[$j]["guid"].'" value="'.$modules[$j]["guid"].'" '.(in_array($modules[$j]["guid"],$role_modules)?"checked":"").'>
                                        <label for="module_'.$modules[$j]["guid"].'" data-toggle="tooltip" data-placement="top" title="'.$modules[$j]["guid"].'">';
                                echo $modules[$j]["title"];
                                if($modules[$j]["module_type"]=='10'){
                                    echo '<span style="color:red;">';
                                }
                                echo isset($modules_type[$modules[$j]["module_type"]])?"【{$modules_type[$modules[$j]["module_type"]]}】":"";
                                if($modules[$j]["module_type"]=='10'){
                                    echo '</span>';
                                }
                                echo '</label>
                                    </div>'."\n";
                                echo "</div>\n";
                                echo "</div>\n";

                                //下级
                                echo '<div class="row">';
                                for($k=0;$k<count($modules);$k++) {

                                    if ($modules[$j]["guid"] == $modules[$k]["parent_guid"] && $modules[$k]["curr_level"] == 2) {
                                        echo '<div class="row">'."\n";
                                        echo '<div class="col-sm-3">'."\n";
                                        echo '<div class="checkbox checkbox-primary">'."\n";
                                        echo helper_str_pad2("　",$modules[$k]["curr_level"],"　");
                                        echo '<input type="checkbox" ';
                                        echo ' parent_path="'.$modules[$k]["parent_path"].'"';
                                        echo 'name="modules[]" id="module_' . $modules[$k]["guid"] . '" value="' . $modules[$k]["guid"] . '" ' . (in_array($modules[$k]["guid"], $role_modules) ? "checked" : "") . '>
                                        <label for="module_' . $modules[$k]["guid"] .'" data-toggle="tooltip" data-placement="top" title="'.$modules[$k]["guid"].'">';
                                        echo $modules[$k]["title"];
                                        echo isset($modules_type[$modules[$k]["module_type"]]) ? "【{$modules_type[$modules[$k]["module_type"]]}】" : "";
                                        echo '</label>
                                                </div>'."\n";
                                        echo "</div>\n";
                                        echo "</div>\n";
                                       // echo '<div class="row">';
                                        //下级
                                        for($l=0;$l<count($modules);$l++) {
                                            if ($modules[$k]["guid"] == $modules[$l]["parent_guid"] && $modules[$l]["curr_level"] == 3) {
                                                echo '<div class="row">'."\n";
                                                echo '<div class="col-sm-3 ">'."\n";
                                                echo '<div class="checkbox checkbox-primary " >'."\n";
                                                echo helper_str_pad2("　",$modules[$l]["curr_level"],"　");
                                                echo '<input type="checkbox"';
                                                echo ' parent_path="'.$modules[$l]["parent_path"].'"';
                                                echo 'name="modules[]" id="module_' . $modules[$l]["guid"] . '" value="' . $modules[$l]["guid"] . '" ' . (in_array($modules[$l]["guid"], $role_modules) ? "checked" : "") . '>
                                        <label for="module_' . $modules[$l]["guid"] .'" data-toggle="tooltip" data-placement="top" title="'.$modules[$l]["guid"].'">';
                                                echo $modules[$l]["title"];
                                                echo isset($modules_type[$modules[$l]["module_type"]]) ? "【{$modules_type[$modules[$l]["module_type"]]}】" : "";
                                                echo '</label>
                                                </div>'."\n";
                                                echo "</div>\n";
                                                echo "</div>\n";


                                                //下级
                                                for($m=0;$m<count($modules);$m++) {
                                                    if ($modules[$l]["guid"] == $modules[$m]["parent_guid"] && $modules[$m]["curr_level"] == 4) {
                                                        //echo '<div class="row">';
                                                        echo '<div class="col-sm-3 ">'."\n";
                                                        echo '<div class="checkbox checkbox-primary " >'."\n";
                                                        echo helper_str_pad2("　",$modules[$m]["curr_level"],"　");
                                                        echo '<input type="checkbox"';
                                                        echo ' parent_path="'.$modules[$m]["parent_path"].'"';
                                                        echo ' name="modules[]" id="module_' . $modules[$m]["guid"] . '" value="' . $modules[$m]["guid"] . '" ' . (in_array($modules[$m]["guid"], $role_modules) ? "checked" : "") . '>
                                        <label for="module_' . $modules[$m]["guid"] .'" data-toggle="tooltip" data-placement="top" title="'.$modules[$m]["guid"].'">';
                                                        echo $modules[$m]["title"];
                                                        echo isset($modules_type[$modules[$m]["module_type"]]) ? "【{$modules_type[$modules[$m]["module_type"]]}】" : "";
                                                        echo '</label>
                                                             </div>'."\n";
                                                        echo "</div>\n";
                                                        //echo "</div>";



                                                    }
                                                }
                                            }
                                        }
                                        echo '<div style="clear:both;"></div>'."\n";


                                    }

                                }
                                echo "</div>";

                            }
                        }


                    }
                }

                ?>

            </div>
        </div>
    </div>

    </form>
</div>

<script>
    $("input[parent_path]").click(function (){
        sel_sub_guid($(this));
    });
    function sel_sub_guid(obj) {
        if (obj == '') {
            //全选
            $("input[parent_path]").each(function(){
                $(this)[0].checked = true;
            });
        }
        else {
            //console.log(obj[0].checked);
            //console.log($(obj)[0].checked);
            $("input[parent_path]").each(function () {
                tmp = ',' + $(this).attr("parent_path") + ',';
                if (tmp.indexOf(',' + obj.val() + ',') >= 0) {
                    if ($(obj)[0].checked) {
                        $(this)[0].checked = true;
                    }
                    else {
                        //$(this).removeAttr("checked");
                        $(this)[0].checked = false;
                    }
                }
            });
        }

    }

    function postform(){
        var option={
            rules: {
                form_title: "required"
            }
        };
        $("#edit_form").validate(option);

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

