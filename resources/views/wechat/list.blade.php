<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1.0 user-scalable=no">-->
    <meta name="format-detection" content="telephone=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>沟通中</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/css/themes.css?v=2017129') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/css/h5app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/fonts/iconfont.css?v=2016070717') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('wechat/css/chat_list.css') }}">
    <script src="{{ asset('wechat/js/jquery.min.js') }}"></script>
    <script src="{{ asset('wechat/js/dist/flexible/flexible_css.debug.js') }}"></script>
    <script src="{{ asset('wechat/js/dist/flexible/flexible.debug.js') }}"></script>

</head>
<body>
<div class='fui-page-group'>
    <div class="fui-statusbar"></div>
    <div class='fui-page chat-page'>
        <div class="fui-header">
            <div class="title">消息列表</div>
            <div class="fui-header-right"></div>
        </div>

        <div class="fui-content navbar chat-fui-content" style="padding-bottom: 2rem;">
{{--            <div class="chat-list flex" >--}}

{{--                <div class="chat-img"  style="background-image: url({{ asset('/wechat/img/132.jpg') }})">--}}
{{--                    <span class="badge" style="top: -0.1rem;left: 80%;">1</span>--}}
{{--                </div>--}}
{{--                <div class="chat-info">--}}
{{--                    <p class="chat-merch flex">--}}
{{--                        <span class="title t-28">魔力克</span>--}}
{{--                        <span class="time">2017-12-14</span>--}}
{{--                    </p>--}}
{{--                    <p class="chat-text singleflow-ellipsis">你好</p>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
</div>
</body>
<script>

    var fromid = {{ $fromid }};

    var ws =  new WebSocket("ws://127.0.0.1:9000");

    ws.onopen = function(e) {
        console.log(e);
    };

    ws.onmessage = function(e) {
        var message = eval("("+e.data+")");
        switch (message.type) {
            case 'init':
                messageList();
                var bind = '{"type":"bind", "fromid":"'+fromid+'"}';
                ws.send(bind);
                break;
            case 'text':
                console.log('文字信息'+message.data);
                $(".chat-fui-content").html(' ');
                // 延迟执行，避免数据未插入数据库中
                setTimeout(messageList, 500);
                break;
            case 'img':
                $(".chat-fui-content").html(' ');
                setTimeout(messageList, 500);
                break;
        }
    };


    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    // 消息列表
    function messageList() {
        $.ajax({
            url: "{{ url('/userMessage') }}",
            type: "post",
            dataType: "json",
            data: {
                'fromid': fromid
            },
            success:function (data) {
                $.each(data, function (index, value) {
                    var strPreffix = '<div class="chat-list flex" data-url="'+value.chatPage+'" onclick="chat(this)"><div class="chat-img"  style="background-image: url('+value.headUrl+')">\n' ;
                    if(value.unreadCount > 0) {
                        strPreffix +='<span class="badge" style="top: -0.1rem;left: 80%;">'+value.unreadCount+'</span>\n';
                    }
                    strPreffix +=  ' </div>\n' +
                        '                                        <div class="chat-info">\n' +
                        '                                            <p class="chat-merch flex">\n' +
                        '                                                <span class="title t-28">'+value.userName+'</span>\n' +
                        '                                                <span class="time">'+mydate(value.lastMessage.created_at)+'</span>\n' +
                        '                                            </p>\n' +
                        '                                            <p class="chat-text singleflow-ellipsis">';
                    var strEnd = '</p></div> </div>';
                    switch (value.lastMessage.type) {
                        case 1:
                            strPreffix += replace_em(value.lastMessage.content);
                            break;
                        case 2:
                            // strPreffix += '<img src="storage/'+value.lastMessage.content+'" width="120" height="120">'
                            strPreffix += '图片'; // 显示不了，太大
                            break;
                    }
                    $(".chat-fui-content").append(strPreffix+strEnd);

                })
            }
        });
    }

    //查看结果
    function replace_em(str){

        str = str.replace(/\</g,'&lt;');

        str = str.replace(/\>/g,'&gt;');

        str = str.replace(/\n/g,'<br/>');

        str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/qqFace/arclist/$1.gif" border="0" style="width: 51px;"/>');

        return str;

    }

    // 打开聊天页面
    function chat(obj) {
        var url = $(obj).data("url");
        window.location.href = url;
    }

    /**
     *根据时间戳格式化为日期形式
     */
    function mydate(nS){

        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }

</script>

</html>
