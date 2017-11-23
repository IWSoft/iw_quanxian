<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>


    <style>
        .ibox-title {
            display: none;
        }

        .ibox-content {
            display: none;
        }
    </style>
<?php

?>
    <script>
        <?php
        if(!isset($miao)){
            $miao = 9999999;
        }
        else{
            if($miao==0){
                $miao = 9999999;
            }
        }
        ?>
       // swal("", "You clicked the button!", "success");
        swal({
            title: "<?php echo lang('err_msg');?>",
            text: "<?php echo $msg;?>",
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            <?php echo $miao>0?("timer: ".($miao).","):"";?>
            closeOnConfirm: false
        }, function () {
            $url = "<?php echo $url;?>";
            if($url!="") {
                window.location.href = $url;
            }
        });
    </script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>