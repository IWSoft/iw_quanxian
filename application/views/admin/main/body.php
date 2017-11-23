<?php
$this->load->view(__ADMIN_TEMPLATE__."/main/common_header");
?>
<div class="row m-b-sm m-t-sm">
    <div class="col-md-1">
        <button type="button" id="loading-example-btn" class="btn btn-white btn-sm"><i class="fa fa-refresh"></i> 刷新</button>
    </div>
    <div class="col-md-11">
        <div class="input-group">
            <input type="text" placeholder="请输入项目名称" class="input-sm form-control" value=""> <span class="input-group-btn">
                                        <button type="button" class="btn btn-sm btn-primary"> 搜索</button> </span>
        </div>
    </div>
</div>

<div>
    <?php //放内容 ?>
</div>
<?php
$this->load->view(__ADMIN_TEMPLATE__."/main/common_footer");
?>