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

        <div id="toolbar" class="btn-group toolbar_my"
             style="<?php echo count($this->curr_form_btn) == 0 ? "display:none;" : ""; ?>">
            <?php echo $form_btn; ?>
        </div>
        <!--data-mobile-responsive="true"-->
        <table id="dlist" data-mobile-responsive="true" style="z-index:0;">
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

        $dlist.on("click-cell.bs.table",function(td_obj,field,value,row_obj,d){

            /**
            if(field=="tree"){
               console.log(td_obj);
            }
             **/
            //
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
                param.pid = 0;
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
                            disabled: false,//设置是否可用
                            checked: false//设置选中
                        };
                    }
                    //cellStyle:"cellStyle",
                },
                {
                    field: 'tree',
                    align: 'center',
                    valign: 'middle',
                    width: '1%',
                    events:operateEvents,
                    clickToSelect: false,
                    formatter: function (value, row, index) {
                        html = "<a href='javascript:void(0);' optid='"+row["id"]+"'  class='pid' style=\"cursor:pointer; color:blue;font-size:16px; font-family:'Arial Black'\">►</a>";
                        return html;
                    }
                }
                , {
                    field: 'guid',
                    title: ' ',
                    visible: false,
                    align: 'center',
                    valign: 'middle',
                    width: "1%"
                }
                , {
                    field: 'id',
                    title: '编号',
                    visible: true,
                    align: 'center',
                    valign: 'middle',
                    width: "5%"
                },
                {
                    field: 'pid',
                    title: '',
                    visible: false,
                }
                ,
                {
                    field:'title',
                    title:'栏目名称',
                    visible: true,
                    formatter:function(value,row,index){
                        html = row["tree_flag"]+row["title"];
                        return html;
                    }
                },
                {
                    field:'xxxxxx',
                    title:'opt',
                    width:'1%',
                    formatter:function(value,row,index){
                       return '';
                    }
                }
                <?php
                echo $form_list_btn;
                ?>

            ]


        });

    });



    function load_sub(event,value,row,index){
        console.log(value);
    }

    function insertRows(id,row_index) {
        url = "<?php echo site_url2("ajax");?>";
        if($("[optid='"+id+"']").html()=="▼"){
             return false;
        }
        var loading_box;
        $.ajax({
            url: url,
            type: "get",
            dataType: "json",
            data: {pid: id},
            success: function (data) {
                for (i = 0; i < data["rows"].length; i++) {
                    $dlist.bootstrapTable('insertRow',
                        {index: (row_index + 1 + i), row: data["rows"][i]}
                    );
                    //▼
                }
                if(data["rows"].length>0){
                    $("[optid='"+id+"']").html("▼");
                }
            },
            beforeSend:function(){
                loading_box = layer.load(1, {
                    shade: [0.1,'#fff'] //0.1透明度的白色背景
                });
            },
            complete:function(){
                //loading_box.closeAll();
                layer.closeAll();
            }
        });
    }

    function updateCell(index,filed,value){
        /*
         Update one cell, the params contains following properties:
         index: the row index.
         field: the field name.
         value: the new field value.
        */
        $dlist.bootstrapTable('updateCell', {index: index,field:'tree',value:value});
    }

    function hideRows(pid){
        //hideRow
        /**
         * remove	params	从表格中删除数据，包括两个参数： field: 需要删除的行的 field 名称。
         values: 需要删除的行的值，类型为数组。$table.bootstrapTable('remove', {field: 'id', values: ids});
         */
        if( $("[optid='"+pid+"']").html()=="▼") {
            $dlist.bootstrapTable('remove', {field: 'pid', values: pid});
            $("[optid='" + pid + "']").html("►");
        }
    }

    function selall() {
        return $.map($dlist.bootstrapTable('getSelections'), function (row) {
            return row.guid
        });
    }

    window.operateEvents={
        'click .pid': function(e, value, row, index) {
            if($("[optid='"+row["id"]+"']").html()=="▼") {
                hideRows(row["id"]);
            }
            else{
                insertRows(row["id"], index + 1);
            }
        }
    };

    $("#form_d0228cac-af9e-a8d7-032d-6fa100c0cd98").click(function () {
        selid = '';
        $.map($dlist.bootstrapTable('getSelections'), function (row) {
            selid += row.guid + ",";
        });
        if (selid == "") {
            my_layer_msg("未选中");
            return false;
        }
        if (selid.indexOf('9364547d-4572-4bd2-aaaa-6bc09680e68b') >= 0) {
            my_layer_msg("不能删除原始管理员");
            return false;
        }
        my_layer_confirm("确认删除？", ajax_save, [selid]);

        return false;
    })

    function ajax_save(guid) {
        /**
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
         **/
    }
</script>

<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>
