
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="format-detection" content="telephone=no" />
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

                <div class="chat-text section-left flex">
                <span class="char-img" style="background-image: url({{ asset('wechat/img/123.jpg') }})"></span>
                <span class="text"><i class="icon icon-sanjiao4 t-32"></i>你好</span>
                </div>

                <div class="chat-text section-right flex">
                <span class="text"><i class="icon icon-sanjiao3 t-32"></i>你好</span>
                <span class="char-img" style="background-image: url({{ asset('wechat/img/132.jpg') }})"></span>
                </div>

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
    var ws =  new WebSocket("ws://127.0.0.1:9000");

    ws.onmessage = function(e){
        console.log(e);
    }


</script>
</body>
</html>
