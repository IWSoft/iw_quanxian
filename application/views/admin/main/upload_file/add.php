<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_header");
?>

<div class="row">

    <div class="col-sm-12">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">上传附件</a>
            </li>
            <li class="" onclick="$dlist.bootstrapTable('refresh');"><a  data-toggle="tab" href="#tab-2" aria-expanded="false">查看上传</a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="tab-1" class="tab-pane active">
                <div class="panel-body">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo $model["title"];?></h5>
                    </div>
                    <div class="ibox-content">

                        <div class="page-container">
                            <p>您可以尝试文件拖拽，使用QQ截屏工具，在本窗口按"Ctrl"+"v"进行粘贴，或者点击添加附件按钮。</p>
                            <div id="uploader" class="wu-example">
                                <div class="queueList">
                                    <div id="dndArea" class="placeholder">
                                        <div id="filePicker"></div>
                                        <p><?php echo "格式：".$filetype."，一次最多上传".$model["upload_count"]."个，每个附件容量在：".$model["filesize"]."M以下";?></p>
                                    </div>
                                </div>
                                <div class="statusBar" style="display:none;">
                                    <div class="progress">
                                        <span class="text">0%</span>
                                        <span class="percentage"></span>
                                    </div>
                                    <div class="info"></div>
                                    <div class="btns">
                                        <div id="filePicker2"></div>
                                        <div class="uploadBtn">开始上传</div>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" btn="d3b5a180-18c3-4c7c-648f-e2a06810f0ab" id="btn_post" value="保存" />
                        </div>
                    </div>
                </div>
            </div>
                </div>
            <div id="tab-2" class="tab-pane">
                <div class="panel-body">
                    <div id="toolbar" class="btn-group toolbar_my"  style="">
                        <a id="form_insert"  class="btn btn-sm btn btn-danger" ><i class="fa fa-level-down"></i> &nbsp;批量插入</a>
                    </div>

                    <table id="dlist" data-mobile-responsive="true">
                    </table>
                </div>
            </div>
        </div>


    </div>

</div>

<script>
    var BASE_URL = 'static/js/plugins/webuploader';
    jQuery(function() {
        var $ = jQuery,    // just in case. Make sure it's not an other libaray.

            $wrap = $('#uploader'),

            // 图片容器
            $queue = $('<ul class="filelist"></ul>')
                .appendTo( $wrap.find('.queueList') ),

            // 状态栏，包括进度和控制按钮
            $statusBar = $wrap.find('.statusBar'),

            // 文件总体选择信息。
            $info = $statusBar.find('.info'),

            // 上传按钮
            $upload = $wrap.find('.uploadBtn'),

            // 没选择文件之前的内容。
            $placeHolder = $wrap.find('.placeholder'),

            // 总体进度条
            $progress = $statusBar.find('.progress').hide(),

            // 添加的文件数量
            fileCount = 0,

            // 添加的文件总大小
            fileSize = 0,

            // 优化retina, 在retina下这个值是2
            ratio = window.devicePixelRatio || 1,

            // 缩略图大小
            thumbnailWidth = 110 * ratio,
            thumbnailHeight = 110 * ratio,

            // 可能有pedding, ready, uploading, confirm, done.
            state = 'pedding',

            // 所有文件的进度信息，key为file id
            percentages = {},

            supportTransition = (function(){
                var s = document.createElement('p').style,
                    r = 'transition' in s ||
                        'WebkitTransition' in s ||
                        'MozTransition' in s ||
                        'msTransition' in s ||
                        'OTransition' in s;
                s = null;
                return r;
            })(),

            // WebUploader实例
            uploader;

        if ( !WebUploader.Uploader.support() ) {
            alert( '上传功能不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
            throw new Error( 'WebUploader does not support the browser you are using.' );
        }

        // 实例化
        uploader = WebUploader.create({
                pick: {
                    id: '#filePicker',
                    multiple:<?php echo $model["upload_count"]>1?"true":"false";?>,
                    label: '点击选择附件'
                },
                dnd: '#uploader .queueList',
                paste: document.body,
                fileVal:"shangchuan",//文件域
                accept: {
                    title: '<?php echo str_replace("-","、",$filetype);?>',
                    extensions: '<?php echo str_replace("-",",",$filetype);?>',
                    mimeTypes: '<?php echo str_replace("|",",",$model["filetype"]);?>'
                },
                // swf文件路径
                swf: BASE_URL + '/Uploader.swf',
                disableGlobalDnd: true,
            <?php
            if($model["yuchuli"]>0){
                ?>
                compress:{
                    width: <?php echo $model["yuchuli_width"]>0?$model["yuchuli_width"]:1600;?>,
                    height: <?php echo $model["yuchuli_height"]>0?$model["yuchuli_height"]:1200;?>,
                    // 图片质量，只有type为`image/jpeg`的时候才有效。
                    quality: 100,
                    // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                    allowMagnify: false,
                    // 是否允许裁剪。
                    crop: false,
                    // 是否保留头部meta信息。
                    preserveHeaders: true,
                    // 如果发现压缩后文件大小比原来还大，则使用原来图片
                    // 此属性可能会影响图片自动纠正功能
                    noCompressIfLarger: false,
                    // 单位字节，如果图片大小小于此值，不会采用压缩。
                    compressSize: 0
                },
            <?php
            }
            else{
                echo "compress:false,";
            }
            ?>

                chunked: true,//是否要分片处理大文件上传
                // server: 'http://webuploader.duapp.com/server/fileupload.php',
                server: '<?php echo site_url2("common_upload");?>?guid=<?php echo $model["guid"];?>',
                fileNumLimit: <?php echo $model["upload_count"];?>,
                fileSizeLimit: <?php echo $model["filesize"]*$model["upload_count"];?> * 1024 * 1024,    // 200 M  {int} [可选] [默认值：undefined] 验证文件总大小是否超出限制, 超出则不允许加入队列。
            fileSingleSizeLimit: <?php echo $model["filesize"];?> * 1024 * 1024    // 50 M {int} [可选] [默认值：undefined] 验证单个文件大小是否超出限制, 超出则不允许加入队列。
    });

        // 添加“添加文件”的按钮，
        uploader.addButton({
            id: '#filePicker2',
            label: '继续添加'
        });

        // 当有文件添加进来时执行，负责view的创建
        function addFile( file ) {
            var $li = $( '<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>'+
                    '<p class="progress"><span></span></p>' +
                    '</li>' ),

                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight">向右旋转</span>' +
                    '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                $prgress = $li.find('p.progress span'),
                $wrap = $li.find( 'p.imgWrap' ),
                $info = $('<p class="error"></p>'),

                showError = function( code ) {
                    switch( code ) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text( text ).appendTo( $li );
                };

            if ( file.getStatus() === 'invalid' ) {
                showError( file.statusText );
            } else {
                // @todo lazyload
                $wrap.text( '预览中' );
                uploader.makeThumb( file, function( error, src ) {
                    if ( error ) {
                        $wrap.text( '不能预览' );
                        return;
                    }

                    var img = $('<img src="'+src+'">');
                    $wrap.empty().append( img );
                }, thumbnailWidth, thumbnailHeight );

                percentages[ file.id ] = [ file.size, 0 ];
                file.rotation = 0;
            }

            file.on('statuschange', function( cur, prev ) {
                if ( prev === 'progress' ) {
                    $prgress.hide().width(0);
                } else if ( prev === 'queued' ) {
                    $li.off( 'mouseenter mouseleave' );
                    $btns.remove();
                }

                // 成功
                if ( cur === 'error' || cur === 'invalid' ) {
                    console.log( file.statusText );
                    showError( file.statusText );
                    percentages[ file.id ][ 1 ] = 1;
                } else if ( cur === 'interrupt' ) {
                    showError( 'interrupt' );
                } else if ( cur === 'queued' ) {
                    percentages[ file.id ][ 1 ] = 0;
                } else if ( cur === 'progress' ) {
                    $info.remove();
                    $prgress.css('display', 'block');
                } else if ( cur === 'complete' ) {
                    $li.append( '<span class="success"></span>' );
                }

                $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
            });

            $li.on( 'mouseenter', function() {
                $btns.stop().animate({height: 30});
            });

            $li.on( 'mouseleave', function() {
                $btns.stop().animate({height: 0});
            });

            $btns.on( 'click', 'span', function() {
                var index = $(this).index(),
                    deg;

                switch ( index ) {
                    case 0:
                        uploader.removeFile( file );
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if ( supportTransition ) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                    // use jquery animate to rotation
                    // $({
                    //     rotation: rotation
                    // }).animate({
                    //     rotation: file.rotation
                    // }, {
                    //     easing: 'linear',
                    //     step: function( now ) {
                    //         now = now * Math.PI / 180;

                    //         var cos = Math.cos( now ),
                    //             sin = Math.sin( now );

                    //         $wrap.css( 'filter', "progid:DXImageTransform.Microsoft.Matrix(M11=" + cos + ",M12=" + (-sin) + ",M21=" + sin + ",M22=" + cos + ",SizingMethod='auto expand')");
                    //     }
                    // });
                }


            });

            $li.appendTo( $queue );
        }

        // 负责view的销毁
        function removeFile( file ) {
            var $li = $('#'+file.id);

            delete percentages[ file.id ];
            updateTotalProgress();
            $li.off().find('.file-panel').off().end().remove();
        }

        function updateTotalProgress() {
            var loaded = 0,
                total = 0,
                spans = $progress.children(),
                percent;

            $.each( percentages, function( k, v ) {
                total += v[ 0 ];
                loaded += v[ 0 ] * v[ 1 ];
            } );

            percent = total ? loaded / total : 0;

            spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
            spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
            updateStatus();
        }

        function updateStatus() {
            var text = '', stats;

            if ( state === 'ready' ) {
                text = '选中' + fileCount + '个附件，共' +
                    WebUploader.formatSize( fileSize ) + '。';
            } else if ( state === 'confirm' ) {
                stats = uploader.getStats();
                if ( stats.uploadFailNum ) {
                    text = '已成功上传' + stats.successNum+ '个附件，'+
                        stats.uploadFailNum + '个附件上传失败，<a class="retry" href="#">重新上传</a>失败附件或<a class="ignore" href="#">忽略</a>'
                }

            } else {
                stats = uploader.getStats();
                text = '共' + fileCount + '个（' +
                    WebUploader.formatSize( fileSize )  +
                    '），已上传' + stats.successNum + '个';

                if ( stats.uploadFailNum ) {
                    text += '，失败' + stats.uploadFailNum + '个';
                }
            }

            $info.html( text );
        }

        function setState( val ) {
            var file, stats;

            if ( val === state ) {
                return;
            }

            $upload.removeClass( 'state-' + state );
            $upload.addClass( 'state-' + val );
            state = val;

            switch ( state ) {
                case 'pedding':
                    $placeHolder.removeClass( 'element-invisible' );
                    $queue.parent().removeClass('filled');
                    $queue.hide();
                    $statusBar.addClass( 'element-invisible' );
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass( 'element-invisible' );
                    $( '#filePicker2' ).removeClass( 'element-invisible');
                    $queue.parent().addClass('filled');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $( '#filePicker2' ).addClass( 'element-invisible' );
                    $progress.show();
                    $upload.text( '暂停上传' );
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text( '继续上传' );
                    break;

                case 'confirm':
                    $progress.hide();
                    $upload.text( '开始上传' ).addClass( 'disabled' );

                    stats = uploader.getStats();
                    if ( stats.successNum && !stats.uploadFailNum ) {
                        setState( 'finish' );
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if ( stats.successNum ) {
                        //alert( '上传成功' );
                        parent.layer.msg("上传成功");
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        location.reload();
                    }
                    break;
            }

            updateStatus();
        }

        uploader.onUploadProgress = function( file, percentage ) {
            var $li = $('#'+file.id),
                $percent = $li.find('.progress span');

            $percent.css( 'width', percentage * 100 + '%' );
            percentages[ file.id ][ 1 ] = percentage;
            updateTotalProgress();
        };

        uploader.onFileQueued = function( file ) {
            fileCount++;
            fileSize += file.size;

            if ( fileCount === 1 ) {
                $placeHolder.addClass( 'element-invisible' );
                $statusBar.show();
            }

            addFile( file );
            setState( 'ready' );
            updateTotalProgress();
        };

        uploader.onFileDequeued = function( file ) {
            fileCount--;
            fileSize -= file.size;

            if ( !fileCount ) {
                setState( 'pedding' );
            }

            removeFile( file );
            updateTotalProgress();

        };

        uploader.on( 'all', function( type ) {
            var stats;
            switch( type ) {
                case 'uploadFinished':
                    setState( 'confirm' );
                    break;

                case 'startUpload':
                    setState( 'uploading' );
                    break;

                case 'stopUpload':
                    setState( 'paused' );
                    break;

            }
        });
        uploader.on( 'uploadBeforeSend', function( block, data ) {
            // block为分块数据。

            // file为分块对应的file对象。
            var file = block.file;
            //console.log(file.rotation);
            //将图片的旋转信息当参数发送
            data.rotation = file.rotation;

            // 将存在file对象中的md5数据携带发送过去。
            // data.fileMd5 = file.md5;

            // 删除其他数据
            // delete data.key;
        });

        uploader.onError = function( code ) {
            alert( 'Eroor: ' + code );
        };

        $upload.on('click', function() {
            if ( $(this).hasClass( 'disabled' ) ) {
                return false;
            }

            if ( state === 'ready' ) {
                uploader.upload();
            } else if ( state === 'paused' ) {
                uploader.upload();
            } else if ( state === 'uploading' ) {
                uploader.stop();
            }
        });

        $info.on( 'click', '.retry', function() {
            uploader.retry();
        } );

        $info.on( 'click', '.ignore', function() {
            alert( 'todo' );
            return false;
        } );

        $upload.addClass( 'state-' + state );
        updateTotalProgress();
    });




    /*
    列表
     */


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
                param.sortOrder = 'desc';
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
                }
                ,
                {
                    field: 'guid',
                    title: 'guid',
                    visible: false,
                    width: "1%"
                },
                {
                    field: 'title',
                    title: '文件名',
                    visible: true,
                    width: "20%"
                },
                {
                    field: 'create_date',
                    title: '上传时间',
                    visible: true,
                    width: "20%"
                }
                , {
                    field: 'sys_module_guid_title',
                    title: '模块'
                },
                {
                    field:"mime",
                    title:"文件类型"
                }
                ,{
                    field: 'filesize',
                    title: '容量(M)'
                }
                ,{
                    field:'btn_list_0'
                    ,width: "5%"
                    ,title:' '
                    ,formatter: function (value, row, index) {return "<button type=\"button\" id=\"form_list_0_"+row.guid+"\" url=\""+row.filepath+"\"  onclick=\"window.open('"+row.filepath+"');\"  class=\"btn btn-sm btn-warning\" ><i class=\"fa fa-eye\"></i> &nbsp;查看</button>"}
                }
                ,{
                    field:'btn_list_1'
                    ,width: "5%"
                    ,title:' '
                    ,formatter: function (value, row, index) {return "<button type=\"button\" id=\"form_list_1_"+row.guid+"\" url=\""+row.filepath+"\"  onclick=\"insert_guid('"+row.guid+"')\"  class=\"btn btn-sm btn-primary\" ><i class=\"fa fa-level-down\"></i> &nbsp;插入</button>"}
                }


            ]


        });

    });

    function selall() {
        return $.map($dlist.bootstrapTable('getSelections'), function (row) {
            return row.guid
        });
    }

    $("#form_insert").click(function () {
        selid = '';
        $.map($dlist.bootstrapTable('getSelections'), function (row) {
            if(selid==""){
                selid = row.guid;
            }
            else{
                selid += ","+row.guid;
            }

        });
        if (selid == "") {
            my_layer_msg("未选中");
            return false;
        }
        if(selid!="") {
            insert_guid(selid);
        }
        return false;
    })



    function chk_form_list_btn(){
        return true;
    }




    function insert_guid($guid){
        $(".J_iframe",window.parent.document).each(function(){
            if($(this).css("display")=="inline"){
                <?php if($boxid!=""){?>
                //console.log("aaaaa=" + $(this).contents().find("#<?php echo $boxid;?>").html());
                $(this).contents().find("#<?php echo $boxid;?>").val($guid);//.change();
                <?php
                }
                ?>
                //关闭窗口
                //$(this).layer.close();
                parent.layer.closeAll('iframe');
            }
        });

    }

</script>
<?php
$this->load->view(__ADMIN_TEMPLATE__ . "/common_footer");
?>

