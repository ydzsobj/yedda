<head>
<link href="/css/mui.min.css" rel="stylesheet">
<link href="/css/iconfont.css" rel="stylesheet">
<link href="/css/base.css" rel="stylesheet">
<link href="/css/component.css" rel="stylesheet">
<link href="/css/detail.css" rel="stylesheet">
<link href="/css/new.css" rel="stylesheet">
<link href="/css/shop.css" rel="stylesheet">
<link href="/css/total.css" rel="stylesheet">
<link href="/css/temporary.css" rel="stylesheet">
<link href="/css/pay.css" rel="stylesheet">
<link href="/css/JS5.css" rel="stylesheet" type="text/css">
@if($order['pix_event'])
<!-- Facebook Pixel Code -->
@if($goods->goods_pix!=null&&$goods->goods_pix!='')    
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
         fbq('init', '{{$goods->goods_pix}}'); 
        fbq('track', 'PageView');
        fbq('track', 'Purchase', {value:'{{$order->order_price}}', currency:'TWD'});//购买
        </script>
        <noscript>
         <img height="1" width="1" 
        src="https://www.facebook.com/tr?id={{$goods->goods_pix}}&ev=PageView
        &noscript=1"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endif
          <!-- YaHoo Pixel Code -->
        @if($goods->goods_yahoo_pix!=null&&$goods->goods_yahoo_pix!='')
        <script type="application/javascript">(function(w,d,t,r,u){w[u]=w[u]||[];w[u].push({'projectId':'10000','properties':{'pixelId':'{{$goods->goods_yahoo_pix}}'}});var s=d.createElement(t);s.src=r;s.async=true;s.onload=s.onreadystatechange=function(){var y,rs=this.readyState,c=w[u];if(rs&&rs!="complete"&&rs!="loaded"){return}try{y=YAHOO.ywa.I13N.fireBeacon;w[u]=[];w[u].push=function(p){y([p])};y(c)}catch(e){}};var scr=d.getElementsByTagName(t)[0],par=scr.parentNode;par.insertBefore(s,scr)})(window,document,"script","https://s.yimg.com/wi/ytc.js","dotq");</script>
        <script>
            window.dotq = window.dotq || [];
            window.dotq.push(
            {
            'projectId':'10000',
            'properties':{
                'pixelId':'{{$goods->goods_yahoo_pix}}',
                'qstrings':{
                'et':'custom',
                'ea':'Purchase',
                'product_id': '{{$goods->goods_id}}',
                }
            }});
        </script>
        @endif
        <!-- End YaHoo Pixel Code -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
          @if($goods->goods_google_pix!=null&&$goods->goods_google_pix!='')
        <script async src="https://www.googletagmanager.com/gtag/js?id={{$goods->goods_google_pix}}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', '{{$goods->goods_google_pix}}');
        </script>
        @endif 
        <!-- End Google Pixel Code -->
@endif
</head>
<body style="max-width: 1190px;">

<header class="mui-bar mui-bar-nav" style="background:#fff;    max-width: 1190px;">
        <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left" style="color:#333" onclick="url_href()"></a>
        <h1 class="mui-title">Firm order</h1>
</header>
    <div class="mui-content">
    <div class="pay_image"><span class="sico"><i class="mui-icon mui-icon-checkmarkempty"></i></span></div>
    <div class="pay_success">
            <h2 style="padding:16px 0px 10px 0px; text-align:center; color:#3cba92">Success!</h2>
                            <div style="padding:15px;user-select: text;">
                        Your order number :<font color="red"style="user-select: text;">{{$order->order_single_id}}</font><br>
                        Payable :<font color="red">{{\App\currency_type::where('currency_type_id',$goods->goods_currency_id)->first()['currency_type_name']}} {{$order->order_price}}</font>
                            </div>
                                <div style="text-align:left;padding:10px 15px 20px">
                                            <!--同一个币种不同团队的邮箱不一样-->
                                            Please keep your mobile phone unblocked, so that the courier can contact you in time. If you have any questions, please contact our online customer service in time. Have fun shopping!             </div><a href="mailto:yejforlh@gmail.com" style="color:#F8770E">yejforlh@gmail.com</a>            </div>
            <div align="center" style="padding:0px 15px">
                <button type="button" class="succuss_center_a" style="" onclick="goHome()">Return to the home page&gt;&gt;</button>
            </div>
    </div>
</div>
<script language="javascript">
function url_href()
{
    window.location.href = '/pay';
}
    function goHome(){
        var u = 'http://{{$url}}';
    location.href=u;
        //window.location.href=u;
    }
</script>