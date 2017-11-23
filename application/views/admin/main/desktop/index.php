<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>
<link href="/static/theme/youbao/css/style.css" rel="stylesheet">

<div class="row">
    <?php //放内容 ?>
    <div class="col-md-12">
        <div class="col-sm-12 col-md-12" style="display: none;">
            <div class="selected page">
                <div class="time">
                    <ul class="week fix">
                        <?php
                        for($i=0;$i<7;$i++) {
                            $fir = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                            echo "<li>";

                            $week = date("w",strtotime("+$i day",strtotime($fir)));
                            switch ($week) {
                                case 0:
                                    echo "周日";
                                    break;
                                case 1:
                                    echo "周一";
                                    break;
                                case 2:
                                    echo "周二";
                                    break;
                                case 3:
                                    echo "周三";
                                    break;
                                case 4:
                                    echo "周四";
                                    break;
                                case 5:
                                    echo "周五";
                                    break;
                                case 6:
                                    echo "周六";
                                    break;
                            }
                            echo "</li>";
                        }
                        ?>

                        <div class="clear"></div>
                    </ul>
                    <ul class="day fix">
                        <?php
                        for($i=0;$i<7;$i++) {
                            $fir = date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600));
                            echo "<li";
                            if(date("Y-m-d")==date("Y-m-d",strtotime("+$i day",strtotime($fir)))){
                                echo " class='on01' ";
                            }
                            echo ">";
                            echo date("d",strtotime("+$i day",strtotime($fir)));
                            echo "</li>";
                        }
                        ?>
                        <div class="clear"></div>
                    </ul>
                </div>
            </div>
        </div>
        <div class="clear"></div>

   <div class="well">
       <h2 id="show_dingcan_riqi"></h2>
       临时订餐(截止时间：<span style="color:red;" id="dingcan_shijian_jiezhi_txt"></span> )
       <hr/>
       <div id="today_list">

       </div>
       <input type="hidden" name="dingcan_riqi" id="dingcan_riqi" value=""/>
       <label id="shiduan" style="display: none;"><input type="radio" value="5f81d52e-a48b-dc1f-f8a2-dca543c55463" name="shiduan" /> 早餐</label>
       <label id="shiduan2" style="display: none;"><input type="radio" value="3d99f59f-3b6b-965b-2065-3423326dc20d" name="shiduan" /> 午餐</label>
    </div>


        <div class="col-sm-6 col-md-4" style="padding-left: 0px; padding-right: 0px; margin-bottom: 10px;">
            <!--a href="javascript:parent.tab__open('edaa9e9d-7521-9629-3881-1c768a9dfad1');"-->
        <img id="btn_post" src="static/theme/youbao/images/book_dinner_no.png" style="width: 100%; cursor: pointer;margin-bottom: 10px;"/>
            <!--/a-->
        </div>
        <div class="col-sm-6 col-md-4" style="padding-left: 0px; padding-right: 0px;margin-bottom: 10px;">
        <a href="javascript:parent.tab__open('49e59ac9-de7b-5dac-14a9-efc725d531a0');"><img src="static/theme/youbao/images/cancel.png"  style="width: 100%"/></a>
        </div>
        <div class="col-sm-6 col-md-4" style="padding-left: 0px; padding-right: 0px;margin-bottom: 10px;">
            <a href="javascript:parent.tab__open('cf31bd6e-5dad-d0c7-76ad-8a41202ef596');">
            <img src="static/theme/youbao/images/out.png" style="width: 100%"/>
                </a>
        </div>

    </div>
</div>

<?php
echo helper_alert_info_msg("<div id='zhuyi'></div>");
?>

<script>
    /**
     * 以下是订餐
     */
    function get_dingcan(){
        $("#today_list").html('');
        $.ajax({
            url: "<?php echo site_url2("/iw/dingcan/wodingcan/ajax_dingcan_list");?>",
            dataType: "json",
            success: function (data) {
                if(data.length==0){
                    //$("#show_yiding").css("display","none");
                    $("#today_list").html("<span style='line-height: 150%; color:blueviolet;'>暂无订餐</span>");
                }
                else{
                    html = "";
                    //$("#show_yiding").css("display","");
                    for(i=0;i<data.length;i++){
                        if(i>0){
                            html+="、";
                        }
                        html += "<span style='line-height: 150%; color:blueviolet;'>";
                        html += data[i]["dingcan_type_name"];
                        html += data[i]["can_amount"];
                        html += "份";
                        html += "(";
                        if(data[i]["is_tmp"]=='1'){
                            html+= "临时订餐";
                        }
                        else{
                            html+= "固定订餐";
                        }
                        html += ")";
                        html +=  "</span>";
                    }
                    if(html!=""){
                        $("#today_list").html(html);
                    }
                }
            }
        });
    }
    get_dingcan();
    function get_zhuyi(config){
        if(config["linshi_amount_zaocan"]==config["linshi_amount_wucan"]){
            html = "每天早、午餐各有<span style='font-size:15px;'>±</span>"+config["linshi_amount_zaocan"]+"份机动调整，超出或少于"+config["linshi_amount_zaocan"]+"份请通过OA或手工走审批程序";
        }
        else {
            html = "每人最多订" + config["linshi_amount"] + "份";
            html += ",每天早餐总量" + config["linshi_amount_zaocan"] + "份,午餐总量" + config["linshi_amount_wucan"] + "份";
            html += ",超出份数，请通过OA或手工走审批程序";
        }
        $("#zhuyi").html("注意："+html);
    }

    //提取各类配置
    var config = "";
    var today_time = "<?php echo time();?>";
    function get_config(){
        $.ajax({
            url: "<?php echo site_url2("/iw/dingcan/wodingcan/ajax_config");?>",
            dataType: "json",
            success: function (data) {
                config = data;
                //流程 1.总开关 2.单位有效期 3.单位每天是否可订 4.当天总量和已订是否超出
                get_zhuyi(config);
                get_can_radio();
            }
        });
    }
    get_config();

    function get_can_radio(){
        $("#shiduan").css("display","none");
        $("#shiduan2").css("display","none");
        //$("#btn_post").css("display","none");
        $("#btn_post").attr("src","static/theme/youbao/images/book_dinner_no.png");
        $("#beizhu").css("display","none");
        $.ajax({
            url: "<?php echo site_url2("/iw/dingcan/wodingcan/ajax_dingcan");?>",
            dataType: "json",
            success: function (data) {
                dingcan_config = data;
                $("#dingcan_riqi").val(dingcan_config["dingcan_day"]);
                $("#dingcan_shijian_jiezhi_txt").html(dingcan_config["jiezhi_linshi_jintian_wucan"]);
                $("#show_dingcan_riqi").html("所订日期："+dingcan_config["dingcan_day"]+" "+dingcan_config["dingcan_week"]);
                if(dingcan_config["flag"]=='1'){
                    count = dingcan_config["radio"].length;
                    if(count>0){
                        //$("#btn_post").css("display","");
                        $("#beizhu").css("display","");
                        $("#btn_post").attr("src","static/theme/youbao/images/book_dinner.png");
                    }
                    for(i=0;i<count;i++){
                        if(i==0) {
                            $("input[type=radio][name='shiduan'][value='" + dingcan_config["radio"][i] + "']").parent().css("display", "");
                            $("input[type=radio][name='shiduan'][value='" + dingcan_config["radio"][i] + "']").attr("checked",'checked');
                        }
                        else{
                            $("input[type=radio][name='shiduan'][value='" + dingcan_config["radio"][i] + "']").parent().css("display", "");
                        }
                    }
                }
                else{
                    $("#dingcan_msg").html(dingcan_config["msg"]);
                    $("#dingcan_msg").css("display","");

                }
            }
        });
    }

    function show_zaocan() {
        $("#dingcan_shijian_txt").html(config["jintian_zhuanchu_zaocan"]);
        //$("#btn_post").val("订一份早餐");
    }
    function show_wucan(){
        $("#dingcan_shijian_txt").html(config["jintian_zhuanchu_zaocan"]);
        //$("#btn_post").val("订一份午餐");
    }


    function shijian_duibi(date1,date2){
        var oDate1 = new Date(date1);
        var oDate2 = new Date(date2);
        if(oDate1.getTime() > oDate2.getTime()){
            return true;
        } else {
            //第二个时间大
            return false;
        }
    }

    $("#btn_post").on("click",function(){
        if($("#btn_post").attr("src")=="static/theme/youbao/images/book_dinner_no.png"){
            return false;
        }
        return confirm_save();
    });

    function confirm_save(){
        riqi = $("#dingcan_riqi").val();
        shiduan = $('input:radio[name="shiduan"]:checked').val();
        beizhu = $("#beizhu").val();

        if(riqi==""){
            parent.layer.msg("订餐日期异常");
            return false;
        }
        if(shiduan==""){
            parent.layer.msg("请选择早餐或午餐");
            return false;
        }
        my_layer_confirm("确认提交？", save,[riqi,shiduan,beizhu]);
    }
    function save(riqi,shiduan,beizhu){
        $.ajax({
            url:"<?php echo site_url2("/iw/dingcan/wodingcan/save");?>",
            type:"post",
            data:{riqi:riqi,shiduan:shiduan,beizhu:beizhu},
            dataType:"json",
            error: function (a, b, c) {
                //alert(a);
            },
            success: function (data) {
                err = data["isok"];
                if (err == true) {
                    my_ok_v2(data);
                }
                else {
                    my_err_alert(data["msg"], 15);
                }
                $("#btn_post").removeAttr("disabled");


            },
            beforeSend: function () {
                $("#btn_post").attr("disabled", "disabled");
                /*
                 index = layer.load(1, {
                 shade: [0.5,'#000'] //0.1透明度的白色背景
                 });
                 */
            }

        });
    }
</script>


    <?php
    $this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
    ?>
