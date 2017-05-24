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
    <script>
       // swal("", "You clicked the button!", "success");
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
        swal({
            title: "<?php echo lang('ok_msg');?>",
            text: "<?php echo $msg;?>",
            type: "success",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            <?php echo $miao>0?("timer: ".($miao).","):"";?>
            closeOnConfirm: false
        }, function () {
            window.location.href="<?php echo $url;?>";
        });
    </script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>