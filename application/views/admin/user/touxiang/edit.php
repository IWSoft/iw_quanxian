<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<style>
    .curr_sel_logo{
        border: 3px solid #00CC00;
    }
</style>
<div class="row">
    <form class="form-inline" id="edit_form">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>更换头像</h5>
                </div>
                <div class="ibox-content">


                    <?php
                        foreach ($list as $v) {
                            ?>
                            <div class="col-sm-1" style="text-align: center; margin-bottom: 3px;">
<img onclick="setcurr(this)" style="cursor: pointer;" src="<?php echo $v["filepath"];?>"  logoguid="<?php echo $v["guid"];?>" width="80" height="80" <?php echo $v["guid"]==$curr_logo_guid?"class='curr_sel_logo'":"";?> />
                            </div>
                            <?php
                        }
                    ?>
                    <input type="hidden" name="form_logo" id="form_logo" value="<?php echo $curr_logo_guid;?>"/>
                    <div class="col-md-12" style="text-align: center;">
                        <input type="button" btn="f7528f00-6a62-0802-446e-23590ed33dd7" id="btn_save"/>
                    </div>
                </div>
            </div>
        </div>



    </form>
</div>

<script>
    function setcurr(obj) {
        curr_guid = $(obj).attr("logoguid");
        $("img[logoguid]").each(function () {
            $(this).removeClass("curr_sel_logo");
        });
        $(obj).attr("class","curr_sel_logo");
        $("#form_logo").val(curr_guid);
    }

  $("#btn_save").click(function(){
        guid = $("#form_logo").val();
        if(guid==""){
            parent.layer.msg("请选择一个头像");
            return false;
        }
        $.ajax({
            type:"post",
            dataType:"json",
            url:"<?php echo site_url2("edit");?>",
            data:{"logoguid":guid},
            success:function(data){
                err = data.err;
                filepath = data.filepath;
                if(err=="ok") {
                    parent.layer.msg("保存成功");
                    window.setTimeout('parent.document.getElementById("home_user_logo").src=filepath;parent.document.getElementById("home_user_logo2").src=filepath',"500");
                }
                else{
                    parent.layer.msg(data);
                }
            }
        });

  });
</script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>

