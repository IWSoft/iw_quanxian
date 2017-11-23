<?php
/**
 * Created by Hello,Web.
 * QQ: 4650566
 * 希望您能尊重劳动成果，把我留下^_^
 * Date: 2017-5-15
 * Time: 22:37
 */
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<div class="row">
    <?php //放内容 ?>
    <div class="col-md-12">



        <div class="col-md-12">
            <div id="toolbar" class="btn-group toolbar_my"
                 style="<?php echo count($this->curr_form_btn) == 0 ? "display:none;" : ""; ?>">
                <?php echo $form_btn; ?>
            </div>
        </div>

        <div class="col-md-6">
            <label class="col-sm-4 control-label">微信绑定：
                <span style="color:blue"><?php echo $isbind?"已绑定":"未绑定";?></span>
            </label>
            <div class="col-sm-8">

            </div>
        </div>
    </div>

    <script>



         $("#form_e3dd83d8-2124-de48-991e-749b2005950e").click(function(){
            $.ajax({
                url:"<?php echo site_url2("save");?>",
                type:"post",
                data:{opt:"<?php echo  !$isbind?"bind":"unbind";?>"},
                dataType:"json",
                success:function (data) {
                    if(data["isok"]){
                        //my_layer_msg("操作成功！",true);
                        my_ok_v2(data);
                    }
                    else{
                        //my_layer_msg("发送失败！"+data["msg"],false);
                        my_ok_v2(data);
                    }
                    $("#form_e3dd83d8-2124-de48-991e-749b2005950e").removeAttr("disabled");
                },
                beforeSend:function(){
                    $("#form_e3dd83d8-2124-de48-991e-749b2005950e").attr("disabled");
                }
            });
            return false;
        });

    </script>

    <?php
    $this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
    ?>
