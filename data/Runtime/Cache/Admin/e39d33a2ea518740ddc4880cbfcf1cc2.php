<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en" style="background: #4f8ad0">
    <head>
        <meta charset="UTF-8">
        <title>登录后台</title>
        <link rel="stylesheet" type="text/css" href="/Public/Admin/Static/css/login.css" media="all">
        <!--zui-->
        <link rel="stylesheet" type="text/css" href="/Public/Admin/Static/zui/css/zui.css" media="all">
        <!--zui end-->
    </head>
    <body >
        <div id="main-content">

            <!-- 主体 -->
            <div class="login-body">
                <div class="login-main pr">
                    <form action="<?php echo U('login');?>" method="post" class="login-form">
                        <h3 class="welcome"><img class="logo" src="/Public/Admin/Static/images/login_logo.png"> 登陆后台</h3>
                        <div id="itemBox" class="item-box">
                            <div class="input-group user-name" >
                                <span class="input-group-addon"><i class="icon-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="用户名">
                            </div>
                            <div class="input-group password">
                                <span class="input-group-addon"><i class="icon-lock"></i></span>
                                <input type="password" name="password"  class="form-control" placeholder="密码">
                            </div>

                        </div>
                        <div class="login_btn_panel">
                            <button class="login-btn" type="submit">
                                <span class="in"><i class="icon-loading"></i>登 录 中 ..</span>
                                <span class="on">登 录</span>
                            </button>
                            <div class="check-tips"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
	<!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/js/jquery-2.0.3.min.js"></script>
    <!--<![endif]-->
    <script type="text/javascript" src="/Public/Admin/Static/zui/js/zui.js"></script>
    <script type="text/javascript">
    	/* 登陆表单获取焦点变色 */
    	$(".login-form").on("focus", "input", function(){
            $(this).closest('.item').addClass('focus');
        }).on("blur","input",function(){
            $(this).closest('.item').removeClass('focus');
        });
        
        //刷新验证码
        function refreshVerifyimg(){
            var verifyimg = document.getElementById("verifyimg").src;
            if( verifyimg.indexOf('?') > 0){
                document.getElementById("verifyimg").src = verifyimg+'&random=' + Math.random();
            }else{
                document.getElementById("verifyimg").src = verifyimg.replace(/\?.*$/,'')+'?' + Math.random();
            }
        }

    	//表单提交
    	$(document)
	    	.ajaxStart(function(){
	    		$("button:submit").addClass("log-in").attr("disabled", true);
	    	})
	    	.ajaxStop(function(){
	    		$("button:submit").removeClass("log-in").attr("disabled", false);
	    	});

    	$("form").submit(function(){
    		var self = $(this);
    		$.post(self.attr("action"), self.serialize(), success, "json");
    		return false;

    		function success(data){
    			if(data.status){
    				window.location.href = data.url;
    			} else {
                    var msg = new $.Messager(data.info, {placement: 'bottom'});
                    msg.show();
    				//刷新验证码
                    $('[name=verify]').val('');
                            refreshVerifyimg();
    			}
    		}
    	});

		$(function(){
			//初始化选中用户名输入框
			$("#itemBox").find("input[name=username]").focus();
			//刷新验证码
			refreshVerifyimg();

            //placeholder兼容性
                //如果支持
            function isPlaceholer(){
                var input = document.createElement('input');
                return "placeholder" in input;
            }
                //如果不支持
            if(!isPlaceholer()){
                $(".placeholder_copy").css({
                    display:'block'
                })
                $("#itemBox input").keydown(function(){
                    $(this).parents(".item").next(".placeholder_copy").css({
                        display:'none'
                    })
                })
                $("#itemBox input").blur(function(){
                    if($(this).val()==""){
                        $(this).parents(".item").next(".placeholder_copy").css({
                            display:'block'
                        })
                    }
                })
            }
		});
    </script>
</body>
</html>