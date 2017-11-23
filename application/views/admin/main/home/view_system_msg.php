<?php
/**
 * Created by Hello,Web.
 * QQ: 4650566
 * 希望您能尊重劳动成果，把我留下^_^
 * Date: 2017-7-14
 * Time: 22:11
 */
?>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>

<div class="row">


                    <div class="form-group col-sm-12">
                        <label>通知时间</label>
                        <?php echo date("Y-m-d H:i",$model["createdate"]);?>
                    </div>


                    <div class="form-group col-sm-12">
                        <label>详情</label>
                        <?php echo $model["content"];?>
                    </div>


<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>

