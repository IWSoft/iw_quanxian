var $parentNode = window.parent.document;

function $childNode(name) {
    return window.frames[name]
}

// tooltips
$('.tooltip-demo').tooltip({
    selector: "[data-toggle=tooltip]",
    container: "body"
});

// 使用animation.css修改Bootstrap Modal
$('.modal').appendTo("body");

$("[data-toggle=popover]").popover();

//折叠ibox
$('.collapse-link').click(function () {
    var ibox = $(this).closest('div.ibox');
    var button = $(this).find('i');
    var content = ibox.find('div.ibox-content');
    content.slideToggle(200);
    button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
    ibox.toggleClass('').toggleClass('border-bottom');
    setTimeout(function () {
        ibox.resize();
        ibox.find('[id^=map-]').resize();
    }, 50);
});

//关闭ibox
$('.close-link').click(function () {
    var content = $(this).closest('div.ibox');
    content.remove();
});

//判断当前页面是否在iframe中
if (top == this) {
    var gohome = '<div class="gohome"><a class="animated bounceInUp" href="index.php" title="返回首页"><i class="fa fa-home"></i></a></div>';
    $('body').append(gohome);
}

//animation.css
function animationHover(element, animation) {
    element = $(element);
    element.hover(
        function () {
            element.addClass('animated ' + animation);
        },
        function () {
            //动画完成之前移除class
            window.setTimeout(function () {
                element.removeClass('animated ' + animation);
            }, 2000);
        });
}

//拖动面板
function WinMove() {
    var element = "[class*=col]";
    var handle = ".ibox-title";
    var connect = "[class*=col]";
    $(element).sortable({
        handle: handle,
        connectWith: connect,
        tolerance: 'pointer',
        forcePlaceholderSize: true,
        opacity: 0.8,
    })
        .disableSelection();
}

/**
 * 打开一个新tab显示iframe
 * @returns {boolean}
 */
function openFrame(url, index, name) {
    var func_open = top.window.func_openFrame;
    func_open(url, index, name);
}


$(document).ready(function () {

    $(document).on("click", "a.page-action", function (event) {
        event.preventDefault();
        var $this = $(this);
        var url = $this.data("href") || $this.attr("href");

        var index = $this.data("id") || $this.attr("id");
        var title = $this.attr("title") || "页面";

        var func_open = top.window.func_openFrame;

        if (func_open) {
            //处于iframe中
            func_open(url, index, title);
        } else {
            //不在iframe中
            window.location.href = url;
        }

        return false;
    });

    //点击按钮打开新页面
    $('a.page-action').click(function (event) {

    });

    $(document).on("click", "button.page-action", function (event) {
        event.preventDefault();
        var $this = $(this);
        var url = $this.data("href") || $this.attr("href");

        var index = $this.data("id") || $this.attr("id");
        var title = $this.attr("title") || "页面";

        var func_open = top.window.func_openFrame;

        if (func_open) {
            //处于iframe中
            func_open(url, index, title);
        } else {
            //不在iframe中
            window.location.href = url;
        }

        return false;
    });

    //点击按钮打开新页面
    $('button.page-action').click(function (event) {

    });

    changeIframeName();
});

function getLayer() {
    return top.window.layer;
}

/**
 * 改变iframe tab 的标题
 */
function changeIframeName() {
    var iframe = window.frameElement;
    if (iframe) {
        var id = $(iframe).attr("data-id");
        $(top.document).find("a.J_menuTab").each(function () {
            var $this = $(this);
            if ($this.attr("data-id") == id && document.title != "" && $this.text() == "") {
                var $btn_close = $this.children("i");
                $this.text(document.title).append($btn_close);
            }
        });
    }
}
