<?php
if($this->router->class=="desktop" && $this->router->method=="index"){

}
else {
    ?>
    <div style="height:10px;"></div>
    <div class="col-md-12 col-sm-12 text-center">

        <input type="button" value="返回首页" onclick="parent.tab__open('6478c00f-f1e6-6b87-825c-4f5c330f5b79');"
               class="btn btn-danger" style="width: 100%"/>

    </div>
    <div style="clear:both;"></div>
    <?php
}
?>
</div>

</div>
</div>
</div>
</div>



</body>
</html>

<script>

    function reload_page(obj) {
        //alert(obj.val());
        obj.html("刷新中");
        window.setTimeout('window.location.reload();', 1000);
    }
    $("[btn]").css("display","none");
    <?php
        //检查表单按钮权限
    if(isset($this->curr_form_btn)){
    if(is_array($this->curr_form_btn)){
    foreach($this->curr_form_btn as $v){
    ?>
    $("[btn=<?php echo $v["guid"];?>]").css("display","");
    $("[btn=<?php echo $v["guid"];?>]").attr('class','<?php echo $v["btn_color_css"];?>');
    $("[btn=<?php echo $v["guid"];?>]").val('<?php echo $v["title"];?>');
    <?php
    }
    }
    }
    ?>
</script>
<script src="static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/js/content.js"></script>
<script src="static/js/contabs.js"></script>