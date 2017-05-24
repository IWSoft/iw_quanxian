<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<div class="row">
    <?php //放内容 ?>
    <div class="col-md-12">

        <div id="toolbar" class="btn-group toolbar_my"
             style="<?php echo count($this->curr_form_btn) == 0 ? "display:none;" : ""; ?>">

            <?php echo $form_btn; ?>
            <?php
            if (isset($model["parent_guid"])) {
                ?>
                <button class="btn btn-default btn-sm"
                        parent_guid="<?php echo isset($model["parent_guid"]) ? $model["parent_guid"] : ""; ?>"
                        onclick="return goback(this);"><i
                        class="fa fa-arrow-circle-up"></i>返回上级 <?php echo $model["title"]; ?>
                </button>
                <?php
            } else {
                ?>
                <button class="btn btn-default btn-sm"
                        parent_guid=""
                        onclick="return goback(this);"><i class="fa fa-arrow-circle-up"></i>返回顶级
                </button>
                <?php
            }
            ?>
        </div>
        <!--data-mobile-responsive="true"-->
        <table id="dlist" data-mobile-responsive="true">
        </table>


    </div>
</div>

<script>
    function goback(obj) {
        guid = $(obj).attr("parent_guid");
        window.location.href = "<?php echo site_url2("list2");?>?guid=" + guid;
    }
    var $dlist;
    var add_btn_text = "";
    var edit_btn_text = "";
    var get_sub_btn_text = "";
    $(document).ready(function () {


        $dlist = $("#dlist");


        $dlist.on("load-success.bs.table", function () {

        });

        $dlist.on("check.bs.table", function (e, row) {

            if (row.guid != "") {
                //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "<?php echo site_url2("edit");?>?guid=" + row.guid);
                //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text + "(ID" + row.id + ")");
                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href", "<?php echo site_url2("get_sub_menu");?>?parent_guid=" + row.guid);
                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text + "(ID" + row.id + ")");
                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title", get_sub_btn_text + "(ID" + row.id + ")");
            }
        });
        $dlist.on("load-success.bs.table", function () {
            if (add_btn_text == "") {
                //记录新增按钮的HTML（首次）
                //add_btn_text = $("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").html();
                //edit_btn_text = $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html();
                get_sub_btn_text = $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html();
            }
        });
        $dlist.on("uncheck-all.bs.table", function (e, row) {

            //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "");
            //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text);
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href", "");
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text);
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title", get_sub_btn_text);
        });
        $dlist.on("uncheck.bs.table", function (e, row) {
            //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "");
            //$("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text);
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href", "");
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text);
            //$("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title", get_sub_btn_text);
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
            url: "<?php echo site_url2("ajax2");?>",
            queryParams: function (param) {
                param.mt = '';//新增网址参数
                param.guid = '<?php echo isset($model["guid"]) ? $model["guid"] : "";?>';
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
                            disabled: (row['can_del'] == '1' ? false : true),//设置是否可用
                            checked: false//设置选中
                        };
                    }
                    //cellStyle:"cellStyle",
                    //formatter:"cellStyle"
                }, {
                    field: 'id',
                    title: '编号',
                    visible: true,
                    width: "5%"
                },
                {
                    field: 'can_del',
                    title: '是否能删',
                    visible: false
                },
                {
                    field: 'title',
                    title: '菜单名称',
                    formatter: function (value, row, index) {
                        return value;
                    }
                }
                , {
                    field: 'fulltitle',
                    title: '全称',
                    formatter: function (value, row, index) {
                        return value;
                    }
                }

                , {
                    field: 'parent_title',
                    title: '上级菜单',
                    width: '30%'
                }
                , {
                    field: 'sort',
                    title: '排序',
                    align: 'center',
                    valign: 'middle',
                    width: "10%"
                }


                <?php
                echo $form_list_btn;
                ?>

            ]


        });

    });


    function selall() {
        //$dlist.bootstrapTable('checkAll');
        return $.map($dlist.bootstrapTable('getSelections'), function (row) {
            return row.guid
        });

    }
    function cellStyle(value, row, index) {
        var classes = ['iCheck-helper'];
        return {
            class: 'checkbox checkbox-primary',
            disabled: true,
            checked: true
        };
    }

    $("#form_e4d2462b-ad1f-6491-b0ee-5c4880bafbc7").click(function(){
        selid = '';
        $.map($dlist.bootstrapTable('getSelections'), function (row) {
            selid += row.guid + ",";
        });
        if (selid == "") {
            my_layer_msg("未选中");
            return false;
        }
        if (selid.indexOf('f0761c0f98c1') >= 0) {
            my_layer_msg("不能删除创始角色");
            return false;
        }
        my_layer_confirm("确认删除？", ajax_save, [selid]);
        return false;
    });


    function ajax_save(guid) {
        $.post(
            "<?php echo site_url2("del");?>",
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

