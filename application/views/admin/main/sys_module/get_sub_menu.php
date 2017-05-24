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

            /*
             $("input[type='checkbox']").addClass("iCheck-helper");
             $("input[type='checkbox']").iCheck({
             checkboxClass: "icheckbox_square-green",
             radioClass: "iradio_square-green"
             });
             */
            //$('.iCheck-helper').css('position', 'relative');
            //alert($("input[type='checkbox']").length);
        });

        $dlist.on("check.bs.table", function (e, row) {
            //alert(index[0].guid);
            //alert(index[0]['guid']);
            //list = JSON.stringify($dlist.bootstrapTable('getSelections'));
            //alert(JSON.stringify($dlist.bootstrapTable('getSelections')));
            //alert(JSON.stringify(row));
            if (row.guid != "") {
                //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").attr("url", "<?php echo site_url2("add");?>?parent_guid=" + row.guid);
                //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").html(add_btn_text + "(<?php echo lang("iw_main_sys_module_sub");?>ID" + row.id + ")");

                $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "<?php echo site_url2("edit");?>?guid=" + row.guid);
                $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text + "(ID" + row.id + ")");

                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href", "<?php echo site_url2("get_sub_menu");?>?parent_guid=" + row.guid);
                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text + "(ID" + row.id + ")");
                $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title",get_sub_btn_text + "(ID" + row.id + ")");
            }
        });
        $dlist.on("load-success.bs.table", function () {
            if (add_btn_text == "") {
                //记录新增按钮的HTML（首次）
                add_btn_text = $("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").html();
                edit_btn_text = $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html();
                get_sub_btn_text = $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html();
            }
        });
        $dlist.on("uncheck-all.bs.table", function (e, row) {
            //alert(JSON.stringify(row));
            //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").attr("url", "<?php echo site_url2("add");?>?parent_guid=");
            //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").html(add_btn_text);
            $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "");
            $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text);
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href","");
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text);
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title",get_sub_btn_text);
        });
        $dlist.on("uncheck.bs.table", function (e, row) {
            //alert(JSON.stringify(row));
            //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").attr("url", "<?php echo site_url2("add");?>?parent_guid=");
            //$("#form_a927bb94-5e91-422f-a69d-34fd108c1efe").html(add_btn_text);
            $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").attr("url", "");
            $("#form_b52bec96-cbb7-4ebd-8984-3c1285506a8f").html(edit_btn_text);
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("href","");
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").html(get_sub_btn_text);
            $("#form_eb32b301-d258-46ed-938f-75ffddced9d1").attr("title",get_sub_btn_text);
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
                param.mt = '10';//新增网址参数
                param.parent_guid = '';
                return param;
            },
            columns: [
                {
                    field: 'state',
                    checkbox: true,
                    align: 'center',
                    valign: 'middle',
                    width: "1%"
                    //cellStyle:"cellStyle",
                    //formatter:"cellStyle"
                }, {
                    field: 'id',
                    title: '编号',
                    visible: true,
                    width: "5%"
                }, {
                    field: 'dd_icon_style',
                    visible: false
                }, {
                    field: 'dd_icon_color',
                    visible: false
                }
                , {
                    field: 'title',
                    title: '菜单名称',
                    formatter: function (value, row, index) {
                        if (row.dd_icon_style != "") {
                            value = '<i class="' + row.dd_icon_style + '"></i> ' + value;
                        }
                        if (row.dd_icon_color != "") {
                            value = '<button class=" ' + row.dd_icon_color + '">' + value + '</button>';
                        }
                        return value;
                    }
                },{
                    field:'url',
                    title:'控制器或链接',
                    width:"20%"
                }
                ,{
                    field:'module_type',
                    title:'按钮类型',
                    width:"10%",
                    formatter: function (value, row, index) {
                        //10：菜单 20：顶部按钮 30：表单按钮 40：字段 50：列表按钮
                        switch (value){
                            case "10":
                                value = "菜单";
                                break;
                            case "20":
                                value = "顶部按钮";
                                break;
                            case "30":
                                value = "表单按钮";
                                break;
                            case "40":
                                value = "字段";
                                break;
                            case "50":
                                value = "列表按钮";
                                break;
                            default:"-"
                                break;
                        }
                        return value;
                    }
                },
                {
                    field:'url_target',
                    title:'打开方式',
                    width: "10%",
                    formatter: function (value, row, index) {
                        //_blank 新TAB窗口打开 _self 当前页面 _layerbox 弹层
                        switch (value){
                            case "_blank":
                                value = "新窗口打开";
                                break;
                            case "_self":
                                value = "当前页面";
                                 break;
                            case "_layerbox":
                                value = "弹层";
                                break;

                            default:"-"
                                break;
                        }
                        return value;
                    }
                }
                , {
                    field: 'sort',
                    title: '排序',
                    align: 'center',
                    valign: 'middle',
                    width: "10%"
                }

            ]


        });


    });

    function aa() {
        alert(JSON.stringify($dlist.bootstrapTable('getSelections')));
    }

    function selall() {
        //$dlist.bootstrapTable('checkAll');
        return $.map($dlist.bootstrapTable('getSelections'), function (row) {
            return row.guid
        });

    }
    function cellStyle(value, row, index) {
        var classes = ['iCheck-helper'];
        return {
            class: 'iCheck-helper',
            disabled: true,
            checked: true
        };
    }

    function chk_form_btn(val) {
        ok = true;
        if (typeof($(val).attr("url")) != "undefined") {
            url = $(val).attr("url");
            if(url.indexOf("add")>=0){
                return ok;
            }
            if ((url == "" || url.indexOf("guid") < 0) &&  url.indexOf("edit")< 0  ) {//
                ok = false;
                parent.layer.msg("<?php echo lang("err_sel_line");?>");
            }
        }
        else {
            ok = false;
            parent.layer.msg("<?php echo lang("err_msg_yichang");?>" + ("(1bc846e5edc047d2826be94b1f80df41)"));
        }
        return ok;
        //return (val!="");
    }


</script>

<!--button type="button" title="下级菜单(ID100001)" id="form_eb32b301-d258-46ed-938f-75ffddced9d1" data-href="http://www.baidu.com"
        class="page-action btn btn-sm btn btn-default"
        data-id="asdfasdfasdfasdf"
>
    <i class=""></i> &nbsp;下级菜单(ID100001)
</button-->

<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>

