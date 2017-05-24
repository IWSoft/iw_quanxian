/**
 * 依赖
 <script src="js/plugins/toastr/toastr.min.js"></script>
 **/

/**
 *
 * @param msg 提示信息
 * @param miao 几秒后隐藏
 */
function my_ok_alert(msg, miao) {
    //参数设置，若用默认值可以省略以下面代
    toastr.options = {
        "progressBar": true,//时间条
        "closeButton": true, //是否显示关闭按钮
        "debug": false, //是否使用debug模式
        "positionClass": "toast-top-full-width",//弹出窗的位置
        "showDuration": "300",//显示的动画时间
        "hideDuration": "500",//消失的动画时间
        "timeOut": (miao * 1000), //展现时间
        "extendedTimeOut": "0",//加长展示时间
        "showEasing": "swing",//显示时的动画缓冲方式
        "hideEasing": "linear",//消失时的动画缓冲方式
        "showMethod": "fadeIn",//显示时的动画方式
        "hideMethod": "fadeOut" //消失时的动画方式
    };
    toastr.success(msg, "<b>操作成功</b>");
}

function my_err_alert(msg, miao) {
    //参数设置，若用默认值可以省略以下面代
    toastr.options = {
        "progressBar": true,//时间条
        "closeButton": true, //是否显示关闭按钮
        "debug": false, //是否使用debug模式
        "positionClass": "toast-top-full-width",//弹出窗的位置
        "showDuration": "300",//显示的动画时间
        "hideDuration": "1000",//消失的动画时间
        "timeOut": (miao * 1000), //展现时间
        "extendedTimeOut": "1000",//加长展示时间
        "showEasing": "swing",//显示时的动画缓冲方式
        "hideEasing": "linear",//消失时的动画缓冲方式
        "showMethod": "fadeIn",//显示时的动画方式
        "hideMethod": "fadeOut" //消失时的动画方式
    };
    toastr.error(msg, "<b>操作错误</b>");
}

function my_info_alert(msg, miao) {
    //参数设置，若用默认值可以省略以下面代
    toastr.options = {
        "progressBar": true,//时间条
        "closeButton": true, //是否显示关闭按钮
        "debug": false, //是否使用debug模式
        "positionClass": "toast-top-full-width",//弹出窗的位置
        "showDuration": "300",//显示的动画时间
        "hideDuration": "1000",//消失的动画时间
        "timeOut": (miao * 1000), //展现时间
        "extendedTimeOut": "0",//加长展示时间
        "showEasing": "swing",//显示时的动画缓冲方式
        "hideEasing": "linear",//消失时的动画缓冲方式
        "showMethod": "fadeIn",//显示时的动画方式
        "hideMethod": "fadeOut" //消失时的动画方式
    };
    toastr.info(msg, "<b>提示信息</b>");
}

function my_warning_alert(msg, miao) {
    //参数设置，若用默认值可以省略以下面代
    toastr.options = {
        "progressBar": true,//时间条
        "closeButton": true, //是否显示关闭按钮
        "debug": false, //是否使用debug模式
        "positionClass": "toast-top-full-width",//弹出窗的位置
        "showDuration": "300",//显示的动画时间
        "hideDuration": "1000",//消失的动画时间
        "timeOut": (miao * 1000), //展现时间
        "extendedTimeOut": "1000",//加长展示时间
        "showEasing": "swing",//显示时的动画缓冲方式
        "hideEasing": "linear",//消失时的动画缓冲方式
        "showMethod": "fadeIn",//显示时的动画方式
        "hideMethod": "fadeOut" //消失时的动画方式
    };
    toastr.warning(msg, "<b>警告信息</b>");
}

function my_clear_alert() {
    //toastr.clear();
}

function my_open_box(options) {
    /*
     title标题,url,width默认为0,height默认为0,
     func前置函数用于做点击前判断,
     args前置函数的参数，通过数组传入如：['a','b']对应chk(a,b)
     */
    //alert(JSON.stringify(options));
    title = options.title;
    url = options.url;
    width = options.width;
    height = options.height;

    if (options.hasOwnProperty("func")) {
        if ('function' == typeof options.func) {
            if (options.hasOwnProperty("args")) {
                if (!options.func.apply(this, options.args)) {
                    return false;
                }
            }
            else {
                if (!options.func.apply(this)) {
                    return false;
                }
            }
        }
    }

    if (width == 0) {
        width = "60%";
    }
    if (height == 0) {
        height = "60%";
    }

    if($(window).width()<=480){
        //手机端
        width="99%";
        height = "99%";
    }
    parent.layer.open({
        type: 2,
        title: title,
        shadeClose: true,
        shade: 0.8,
        anim: 3,
        resize: true,
        maxmin: true,
        area: [width, height],
        content: url
    });
}

function my_close_box() {
    parent.layer.close();
}

function my_ok(msg, url, miao) {
    window.location.href = "index.php/iw/main/msg/ok" + "?url=" + encodeURI(url) + "&msg=" + encodeURI(msg) + "&miao=" + miao;
}

function my_err(msg, url, miao) {
    window.location.href = "index.php/iw/main/msg/err" + "?url=" + encodeURI(url) + "&msg=" + encodeURI(msg) + "&miao=" + miao;
}
/**
 * 通用返回处理，对应php的helper_return_json函数
 * @param json
 */
function my_ok_v2(json) {
    //alert(JSON.stringify(json));
    if (json["url"] == "") {
        json["url"] = window.location.href;
    }
    if (json["isok"]) {
        window.location.href = "index.php/iw/main/msg/ok" + "?url=" + encodeURI(json["url"]) + "&msg=" + encodeURI(json["msg"]) + "&miao=" + json["miao"];
    }
    else {
        window.location.href = "index.php/iw/main/msg/err" + "?url=" + encodeURI(json["url"]) + "&msg=" + encodeURI(json["msg"]) + "&miao=" + json["miao"];
    }
}

function my_layer_msg(msg,true_or_false) {
    if(true_or_false==undefined){
        true_or_false = false;
    }
    parent.layer.msg(msg,
        {
            icon: (true_or_false?6:5),
            shade: 0.3
        }
    );
}
/**
 * 调用确认框
 * 参数样例 'good job',hehe,['haha']
 * @param msg
 * @param func
 * @param args
 */
function my_layer_confirm(msg,func,args){

    parent.layer.confirm(msg, {
        title:'确认操作',
        icon: 3,
        btn: ['确认','取消'] //按钮
    }, function(index){
        parent.layer.close(index);
        if ('function' == typeof func) {
            if(args!=undefined) {
                func.apply(this, args);
            }
        }
    }, function(index){
        parent.layer.close(index);
        return false;
    });
}


