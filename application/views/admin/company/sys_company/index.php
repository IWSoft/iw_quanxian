<?php

$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<div class="row">
    <?php //放内容 ?>
    <div class="col-md-12">

        <div id="toolbar" class="btn-group toolbar_my"
             style="<?php echo count($this->curr_form_btn) == 0 ? "display:none;" : ""; ?>">
            <?php echo $form_btn; ?>
        </div>
        <!--data-mobile-responsive="true"-->
        <table id="dlist" data-mobile-responsive="true">
        </table>
    </div>
</div>

<script>

    var $dlist;
    var add_btn_text = "";
    var edit_btn_text = "";
    var get_sub_btn_text = "";
    $(document).ready(function () {


        $dlist = $("#dlist");


        $dlist.on("load-success.bs.table", function () {


        });

        $dlist.on("check.bs.table", function (e, row) {
            //选中
        });
        $dlist.on("load-success.bs.table", function () {
            //成功显示
        });
        $dlist.on("uncheck-all.bs.table", function (e, row) {
            //反选全选
        });
        $dlist.on("uncheck.bs.table", function (e, row) {
            //反选
        });




        $dlist.bootstrapTable({
            undefinedText: "-",
            striped: false,
            sidePagination: "server",
            pagination: true,
            search: true,
            toolbar: "#toolbar",
            showRefresh: true,
            pageList: "[<?php echo implode(",", $this->config->item("def_pagesize_arr"));?>]",
            clickToSelect: true,
            classes: "",
            pageSize: "<?php echo $pagesize;?>",
            queryParamsType: "",//必须为空，用于指定默认网址参数
            rowStyle: function (row, index) {
                var classes = ['bootstrap-table_row_color bootstrap-table_row_padding', 'bootstrap-table_row_color2 bootstrap-table_row_padding'];
                return {
                    classes: classes[index % 2]
                };
            },
            url: "<?php echo site_url2("ajax");?>",
            queryParams: function (param) {
                param.mt = '';//新增网址参数
                return param;
            },
            columns: [
                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle',
                    width: "1%",
                    formatter: function (value, row, index) {
                        return {
                            //disabled: (row['guid'] != '9364547d-4572-4bd2-aaaa-6bc09680e68b' ? false : true),//设置是否可用
                            //checked: false//设置选中
                        };
                    }
                    //cellStyle:"cellStyle",

                },
                {
                    field: 'guid',
                    title: '-',
                    visible: false,
                    width: "1%"
                }
                , {
                    field: 'id',
                    title: '编号',
                    visible: true,
                    width: "5%"
                }, {
                    field: 'fullname',
                    title: '单位全称'
                }
                ,{
                    field:'name',
                    title:'单位简称',
                    visible: true
                }
                <?php
                echo $form_list_btn;
                ?>

            ]


        });

    });

    function selall() {
        return $.map($dlist.bootstrapTable('getSelections'), function (row) {
            return row.guid
        });
    }

    $("#form_10637e7e-2879-c37c-21a2-03624a261abe").click(function () {
        selid = '';
        $.map($dlist.bootstrapTable('getSelections'), function (row) {
            selid += row.guid + ",";
        });
        if (selid == "") {
            my_layer_msg("未选中");
            return false;
        }
        my_layer_confirm("确认删除？", ajax_save, [selid]);

        return false;
    })

    function ajax_save(guid) {
        $.post(
            "<?php echo site_url2('del');?>",
            {guid: guid},
            function (result) {
                json = result;
                if (json.ok == 1) {
                    my_layer_msg(json.msg, true);
                    //刷新
                    $dlist.bootstrapTable('refresh');
                }
                else {
                    my_layer_msg(json.msg);
                }
            },
            "json"
        );
    }
</script>

<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>
