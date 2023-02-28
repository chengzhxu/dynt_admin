<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>寻找春季女神</title>
    <meta name="viewport" content="width=750,user-scalable=no">
    <meta name="format-detection" content="telephone=no,address=no,email=no">
    <meta name="mobileOptimized" content="width">
    <meta name="handheldFriendly" content="true">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <link rel="stylesheet" href="/Public/Home/home/master/css/daren_index.css"/>
    <script type="text/javascript" src="/Public/Home/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>
    <div class="line_bg">
        <div class="index_pic">
            <img src="/Public/Home/home//master/img/index_pic.png?v=1"/>
        </div>
        <img class="logo_pic" src="/Public/Home/home//master/img/logo.png"/>
        <div class="apply_for"><p>申请</p><a class="a_href" href="http://box.careof.me/master/research"></a></div>
    </div>
    <script>
        var height = $(window).height();
        $(".line_bg").css("height",height);
    </script>
<script>
wx.config({
	debug:false,
	appId: "<?php echo ($ticket[appid]); ?>", // 必填，公众号的唯一标识
	timestamp: "<?php echo ($ticket[timestamp]); ?>", // 必填，生成签名的时间戳
	nonceStr: "<?php echo ($ticket[noncestr]); ?>", // 必填，生成签名的随机串
	signature: "<?php echo ($ticket[signature]); ?>",// 必填，签名，见附录1
	jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','chooseImage','uploadImage']
});
wx.onMenuShareTimeline({
	title: '<?php echo ($desc); ?>', // 分享标题
	link: '<?php echo ($shareurl); ?>', // 分享链接
	imgUrl: '<?php echo ($shareimg); ?>', // 分享图标
	success: function () { 
		// 用户确认分享后执行的回调函数
	},
	cancel: function () { 
		// 用户取消分享后执行的回调函数
	}
});

wx.onMenuShareAppMessage({
	title: '<?php echo ($title); ?>', // 分享标题
	desc: '<?php echo ($desc); ?>', // 分享描述
	link: '<?php echo ($shareurl); ?>', // 分享链接
	imgUrl: '<?php echo ($shareimg); ?>', // 分享图标
	type: '', // 分享类型,music、video或link，不填默认为link
	dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
	success: function () { 
		// 用户确认分享后执行的回调函数
	},
	cancel: function () { 
		// 用户取消分享后执行的回调函数
	}
});

wx.onMenuShareQQ({
	title: '<?php echo ($title); ?>', // 分享标题
	desc: '<?php echo ($desc); ?>', // 分享描述
	link: '<?php echo ($shareurl); ?>', // 分享链接
	imgUrl: '<?php echo ($shareimg); ?>', // 分享图标
	success: function () { 
	   // 用户确认分享后执行的回调函数
	},
	cancel: function () { 
	   // 用户取消分享后执行的回调函数
	}
});

wx.onMenuShareWeibo({
	title: '<?php echo ($title); ?>', // 分享标题
	desc: '<?php echo ($desc); ?>', // 分享描述
	link: '<?php echo ($shareurl); ?>', // 分享链接
	imgUrl: '<?php echo ($shareimg); ?>', // 分享图标
	success: function () { 
	   // 用户确认分享后执行的回调函数
	},
	cancel: function () { 
		// 用户取消分享后执行的回调函数
	}
});
</script>
<script type="text/javascript" src="/Public/Home/js/youmengtongji.js"></script>
<script type="text/javascript" src="/Public/Home/js/delectwenzi.js"></script>
</body>
</html>