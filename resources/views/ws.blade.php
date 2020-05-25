
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>沟通中</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/css/themes.css?v=2017129') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/css/h5app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/fonts/iconfont.css?v=2016070717') }}">
{{--    <script src="{{ asset('wechat/js/jquery.min.js') }}"></script>--}}
    <script src="{{ asset('wechat/js/dist/flexible/flexible_css.debug.js') }}"></script>
    <script src="{{ asset('wechat/js/dist/flexible/flexible.debug.js') }}"></script>
    <script src="{{ asset('qqFace/js/jquery.min.js') }}"></script>
    <script src="{{ asset('qqFace/js/jquery.qqFace.js') }}"></script>
    <style>
        .qqFace { margin-top: -401px; background: #fff; padding: 2px; border: 1px #dfe6f6 solid; }
        .qqFace table td { padding: 0px; }
        .qqFace table td img { cursor: pointer; border: 1px #fff solid; width: 100%}
        .qqFace table td img:hover { border: 1px #0066cc solid; }
    </style>
</head>
<body ontouchstart>
<div class='fui-page-group'>
    <div class='fui-page chatDetail-page'>
        <div class="chat-header flex">
            <i class="icon icon-toleft t-48"></i>
            <span class="shop-titlte t-30">商店</span>
            <span class="shop-online t-26"></span>
            <span class="into-shop">进店</span>
        </div>
        <div class="fui-content navbar" style="padding:1.2rem 0 1.35rem 0;">
            <div class="chat-content">
                <p style="display: none;text-align: center;padding-top: 0.5rem" id="more"><a>加载更多</a></p>
                <p class="chat-time"><span class="time">2017-11-12</span></p>

{{--                <div class="chat-text section-left flex">--}}
{{--                <span class="char-img" style="background-image: url({{ asset('wechat/img/123.jpg') }})"></span>--}}
{{--                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>你好</span>--}}
{{--                </div>--}}

{{--                <div class="chat-text section-right flex">--}}
{{--                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>你好</span>--}}
{{--                <span class="char-img" style="background-image: url({{ asset('wechat/img/132.jpg') }})"></span>--}}
{{--                </div>--}}

            </div>
        </div>
        <div class="fix-send flex footer-bar">
            <i class="icon icon-emoji1 t-50"></i>
            <input class="send-input t-28" maxlength="200" id="saytext">
            <input type="file" name="file" id="file" style="display: none">
            <i class="icon icon-add t-50" style="color: #888;"></i>
            <span class="send-btn">发送</span>
        </div>
    </div>
</div>

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // qq表情
        $('.icon-emoji1').qqFace({

            assign:'saytext',

            path:'/qqFace/arclist/'	//表情存放的路径

        });

        // $(".sub_btn").click(function(){
        //
        //     var str = $("#saytext").val();
        //
        //     $("#show").html(replace_em(str));
        //
        // });
    });

    //查看结果
    function replace_em(str){

        str = str.replace(/\</g,'&lt;');

        str = str.replace(/\>/g,'&gt;');

        str = str.replace(/\n/g,'<br/>');

        str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/qqFace/arclist/$1.gif" border="0" style="width: 51px;"/>');

        return str;

    }
    var fromid = {{ $fromid }};
    var toid = {{ $toid }};
    var from_head = '';
    var to_head = '';
    var from_name = '';
    var to_name = '';
    var online = 0;

    var ws =  new WebSocket("ws://127.0.0.1:9000");

    ws.onopen = function(e) {
        console.log(e);
    };



    ws.onmessage = function(e){
        var message = eval("("+e.data+")");
        switch (message.type) {
            // 将用户id与生成的client_id绑定
            case 'init':
                var bind = '{"type":"bind", "fromid":"'+fromid+'"}';
                ws.send(bind);
                getHeadImg(fromid, toid);
                messageInit(fromid, toid);
                // 初始化判断用户是否在线
                var online = '{"type": "online", "toid":"'+toid+'", "fromid":"'+fromid+'"}';
                ws.send(online);
                break;
            case 'online':
                if (message.status ==1) {
                    online = 1;
                    $(".shop-online").text("在线");
                } else {
                    online = 0;
                    $(".shop-online").text("离线");
                }
                break;
            case 'text':
                if (message.fromid == toid) {
                    $(".chat-content").append('    <div class="chat-text section-left flex">\n' +
                        '                <span class="char-img" style="background-image: url('+to_head+')"></span>\n' +
                        '                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>'+replace_em(message.data)+'</span>\n' +
                        '                </div>');
                    $(".chat-content").scrollTop(3000);
                }
                break;
            case 'img':
                if(message.fromid == toid) {
                    $(".chat-content").append('  <div class="chat-text section-left flex">\n' +
                        '                <span class="char-img" style="background-image: url('+to_head+')"></span>\n' +
                        '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i><img src="'+message.data+'" width="120" height="120"></span>\n' +
                        '                </div>');
                    $(".chat-content").scrollTop(3000);
                }
                break;
            // 消息持久化存储
            case 'save':
                saveMessage(message);
                if(message.isread == 1) {
                    online = 1;
                    $(".shop-online").text("在线");
                } else {
                    online = 0;
                    $(".shop-online").text("离线");
                }
                break;
        }
    };

    // 发送文字信息
    $(".send-btn").click(function () {
        var text = $(".send-input").val();
        var message = '{"type":"text", "data":"'+text+'", "fromid":"'+fromid+'", "toid":"'+toid+'"}';
        $(".chat-content").append('  <div class="chat-text section-right flex">\n' +
            '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>'+replace_em(text)+'</span>\n' +
            '                <span class="char-img" style="background-image: url('+from_head+')"></span>\n' +
            '                </div>');
        $(".chat-content").scrollTop(3000);
        ws.send(message);
        $(".send-input").val('');
    });

    // 发送图片信息
    $(".icon-add").click(function () {
        $("#file").click();
    });

    $("#file").change(function () {
        var fileLists = $("#file")[0].files[0];
        var formData = new FormData();
        formData.append('fromid', fromid);
        formData.append('toid', toid);
        formData.append('online', online);
        formData.append('file', fileLists);

        $.ajax({
            url: "{{ url('/file') }}",
            type: "post",
            cache: false,
            dataType: "json",
            data: formData,
            processData: false, // 禁止转对象
            contentType: false, // 默认urlencode
            success: function (data, status, xhr) {
                if(data.status == 1) {
                    $(".chat-content").append('  <div class="chat-text section-right flex">\n' +
                        '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i><img src="'+data.path+'" width="120" height="120"></span>\n' +
                        '                <span class="char-img" style="background-image: url('+from_head+')"></span>\n' +
                        '                </div>');
                    $(".chat-content").scrollTop(3000);
                    // 解决不能发送同一张图片
                    $("#file").val('');
                    message = '{"fromid":"'+fromid+'", "toid":"'+toid+'", "type":"img", "data":"'+data.path+'"}';
                    ws.send(message);
                } else {
                    console.log(data.msg);
                }
            }

        });
    });

    // 持久化信息
    function saveMessage(data) {
        $.ajax({
            url:"{{url('/save')}}",
            type:"post",
            data: data,
            dataType: "json",
            success: function (data) {
                console.log(data);
            }
        });
    }

    // 获取头像昵称
    function getHeadImg(fromid, toid) {
        $.ajax({
            url: "{{ url('/avatar') }}",
            type: "post",
            dataType: "json",
            data: {
                "fromid": fromid,
                "toid": toid
            },
            success: function (data) {
                from_head = data.from_head;
                to_head = data.to_head;
                from_name = data.from_name;
                to_name = data.to_name;
                $(".shop-titlte").text(to_name);
            }
        });
    }

    // 聊天记录初始化
    function messageInit(fromid, toid) {
        $.ajax({
            url: "{{ url('/message') }}",
            type: "post",
            dataType: "json",
            data: {
                'fromid': fromid,
                'toid': toid
            },
            success: function (data) {
                $.each(data, function (index, content) {
                    if(fromid == content.fromid) {
                        var strPreffix = '  <div class="chat-text section-right flex">\n' +
                            '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>';
                        var strEnd = '</span>\n' +
                            '                <span class="char-img" style="background-image: url('+from_head+')"></span>\n' +
                            '                </div>';
                        switch (content.type) {
                            case 1:
                                strPreffix += replace_em(content.content);
                                break;
                            case 2:
                                strPreffix += '<img src="storage/'+content.content+'" width="120" height="120">'
                                break;
                        }
                        $(".chat-content").append(strPreffix+strEnd);
                    } else {
                        var strPreffix = '<div class="chat-text section-left flex">\n' +
                            '                <span class="char-img" style="background-image: url('+to_head+')"></span>\n' +
                            '                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>';
                        var strEnd = '</span>\n' +
                            '                </div>';
                        switch (content.type) {
                            case 1:
                                strPreffix += replace_em(content.content);
                                break;
                            case 2:
                                strPreffix += '<img src="storage/'+content.content+'" width="120" height="120">'
                                break;
                        }
                        $(".chat-content").append(strPreffix+strEnd);
                    }
                });
                $(".chat-content").scrollTop(3000);
            }
        });
    }

    // 心跳检测,线上开启
    // setInterval(checHeart, 20000);
    // function checHeart() {
    //     var check = '{"type":"ping","data":"heart beat\n"}';
    //     ws.send(check);
    // }

</script>
</body>
</html>
