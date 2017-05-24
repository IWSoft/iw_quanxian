<!DOCTYPE html><?php // 内页的头 ?>
<html>
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <title>admin</title>
    <?php
    $this->load->view(__ADMIN_TEMPLATE__ . '/header.inc.php');
    ?>

    <script src="static/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="static/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <?php
    //在控制器指定包含的JS
    if (isset($header_include_js)) {
        if (is_array($header_include_js)) {
            foreach ($header_include_js as $v) {
                echo '<script src="' . $v . '"></script>' . "\n";
            }
        }
    }
    //
    if (isset($header_include_css)) {
        if (is_array($header_include_css)) {
            foreach ($header_include_css as $v) {
                echo '<link href="' . $v . '" rel="stylesheet">' . "\n";
            }
        }
    }
    ?>


</head>


<body class="fixed-sidebar full-height-layout gray-bg">
<div class="wrapper wrapper-content animated fadeInUp">
    <div class="row">
        <div class="col-sm-12">

            <div class="ibox">
                <div class="ibox-title">
                    <h5><?php echo isset($this->curr_path) ? $this->curr_path : ""; ?></h5>
                    <div class="ibox-tools">
                        <?php
                        if (isset($this->curr_top_btn)) {
                            if (is_array($this->curr_top_btn)) {
                                foreach ($this->curr_top_btn as $v) {
                                    if ($v["url_target"] != "_layerbox") {
                                        echo '<a title="'.$v["title"].$this->curr_curr_module_name.'" id="top_menu_'.$v["guid"].'" data-href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . '" href="javascript:void(0);"  class="page-action " ><i class="btn btn-ms ' . $v["btn_icon_style"] . " " . $v["btn_color_css"] . '">&nbsp;' . $v["title"] . '</i></a>';
                                        //echo '<a title="'.$v["title"].$this->curr_curr_module_name.'" id="top_menu_'.$v["guid"].'" data-href="' . create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]) . '" href="javascript:void(0);"  class="page-action btn btn-ms ' . $v["btn_icon_style"] . " " . $v["btn_color_css"] . '" >&nbsp;' . $v["title"] . '</a>';
                                    }
                                    else{
                                        echo '<a  id="top_menu_'.$v["guid"].'" url="'.create_module_url($v["controller"], $v["method"], $v["param"], $v["url"]).'" href="javascript:void(0);" onclick="my_open_box({title:\''.$v["title"].'\',url:$(\'#'.('top_menu_'.$v["guid"]).'\').attr(\'url\'),width:0,height:0})"  class=" " >' ."<i class='btn btn-ms " . $v["btn_icon_style"] . " " . $v["btn_color_css"] . '\'>&nbsp;'. $v["title"] . '</i></a>';
                                    }
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="ibox-content">