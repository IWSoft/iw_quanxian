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
        <div class="col-md-6">
            <label class="col-sm-4 control-label">手机号<br/>（一行一个号码，自动去掉重复号码）<span style="color:blue" id="amount_tel"></span> </label>
            <div class="col-sm-8">
                <textarea id="tel" placeholder="一行一个输入11位手机号"  onkeyup="count_tel($('#tel').val())" style="width: 100%"></textarea>
            </div>
        </div>
        <div class="col-md-6">
            <label class="col-sm-4 control-label">短信内容
                <span style="color:blue" id="amount_content"></span>
            </label>
            <div class="col-sm-8">
                <textarea id="content" maxlength="200" placeholder="短信内容注意字数60字以内为佳"  onkeyup="count_content($('#content').val())" style="width: 100%"></textarea>
            </div>
        </div>

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
                url: "<?php echo site_url2("add_ajax");?>",
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
                        width: "1%"
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
                    },
                    {
                        field: 'check_status_name',
                        title: '审核状态'
                    }, {
                        field: 'username',
                        title: '用户名'
                    }
                    , {
                        field: 'title',
                        title: ' ',
                        visible: false
                    }
                    , {
                        field: 'realname',
                        title: '姓名'
                    }
                    , {
                        field: 'company_name',
                        title: '单位'
                    }
                    ,
                    {
                        field: 'tel',
                        title: '手机号'
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

        $("#form_8b42d406-f649-b08a-58ab-8e9bb367fd39").click(function () {

            tels = "";
            $.map($dlist.bootstrapTable('getSelections'), function (row) {
                tel = row.tel;
                if(tel!=null) {
                    if (tel.length == 11) {
                        if (tels == "") {
                            tels = tel;
                        }
                        else {
                            tels += "," + tel;
                        }
                    }
                }
            });
            if (tels == "") {
                parent.layer.msg("选择中会员记录才能添加手机号");
                return false;
            }
            else {
                tels = tels.replace(/\,/g, "\n");
                $("#tel").val(tels);
                count_tel(tels);
            }
            return false;
        })

        function count_tel(text){
            arr = text.split("\n");
            telcount = 0;
            for(i=0;i<arr.length;i++){
                txt = arr[i];
                txt = txt.replace(/\s/ig,"");

                if(txt.length==11){
                    telcount++;
                }
            }
            $("#amount_tel").html(telcount+"个号码");
        }
        function count_content(text){

            $("#amount_content").html(text.length+"个字");
        }

        $("#form_e434fcc7-833b-3ca9-3b21-470ab8785e9e").click(function(){
            var content = $("#content").val();
            var tel = $("#tel").val();
            if(tel.replace(/\s/ig,"")==""){
                parent.layer.msg("请输入手机号码");
                return false;
            }
            if(content.replace(/\s/ig,"")==""){
                parent.layer.msg("请输入短信内容");
                return false;
            }

            $.ajax({
                url:"<?php echo site_url2("save");?>",
                type:"post",
                data:{tel:tel,content:content},
                dataType:"json",
                success:function (data) {
                    if(data["flag"]=="1"){
                        my_layer_msg("发送成功！",true);
                    }
                    else{
                        my_layer_msg("发送失败！"+data["msg"],false);
                    }
                    $("#form_e434fcc7-833b-3ca9-3b21-470ab8785e9e").removeAttr("disabled");
                },
                beforeSend:function(){
                    $("#form_e434fcc7-833b-3ca9-3b21-470ab8785e9e").attr("disabled");
                }
            });
            return false;
        });

    </script>

    <?php
    $this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
    ?>
