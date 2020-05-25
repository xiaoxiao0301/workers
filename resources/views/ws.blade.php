
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
    <script src="{{ asset('wechat/js/jquery.min.js') }}"></script>
    <script src="{{ asset('wechat/js/dist/flexible/flexible_css.debug.js') }}"></script>
    <script src="{{ asset('wechat/js/dist/flexible/flexible.debug.js') }}"></script>
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
            <input class="send-input t-28" maxlength="200">
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
    })
    var fromid = {{ $fromid }};
    var toid = {{ $toid }};
    var from_head = '';
    var to_head = '';
    var from_name = '';
    var to_name = '';

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
                    $(".shop-online").text("在线");
                } else {
                    $(".shop-online").text("离线");
                }
                break;
            case 'text':
                if (message.fromid == toid) {
                    $(".chat-content").append('    <div class="chat-text section-left flex">\n' +
                        '                <span class="char-img" style="background-image: url('+to_head+')"></span>\n' +
                        '                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>'+message.data+'</span>\n' +
                        '                </div>');
                    $(".chat-content").scrollTop(3000);
                }
                break;
            // 消息持久化存储
            case 'save':
                saveMessage(message);
                if(message.isread == 1) {
                    $(".shop-online").text("在线");
                } else {
                    $(".shop-online").text("离线");
                }
                break;
        }
    };

    $(".send-btn").click(function () {
        var text = $(".send-input").val();
        var message = '{"type":"text", "data":"'+text+'", "fromid":"'+fromid+'", "toid":"'+toid+'"}';
        $(".chat-content").append('  <div class="chat-text section-right flex">\n' +
            '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>'+text+'</span>\n' +
            '                <span class="char-img" style="background-image: url('+from_head+')"></span>\n' +
            '                </div>');
        $(".chat-content").scrollTop(3000);
        ws.send(message);
        $(".send-input").val('');
    });

    // 持久化信息
    function saveMessage(data) {
        $.ajax({
            url:"{{url('/save')}}",
            type:"post",
            data: data,
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
            data: {
                'fromid': fromid,
                'toid': toid
            },
            success: function (data) {
                $.each(data, function (index, content) {
                    if(fromid == content.fromid) {
                        $(".chat-content").append('  <div class="chat-text section-right flex">\n' +
                            '                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>'+content.content+'</span>\n' +
                            '                <span class="char-img" style="background-image: url('+from_head+')"></span>\n' +
                            '                </div>');
                    } else {
                        $(".chat-content").append('    <div class="chat-text section-left flex">\n' +
                            '                <span class="char-img" style="background-image: url('+to_head+')"></span>\n' +
                            '                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>'+content.content+'</span>\n' +
                            '                </div>');
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
