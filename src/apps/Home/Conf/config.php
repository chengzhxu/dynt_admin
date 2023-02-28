<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Home/home/',
        '__JS__'     => __ROOT__ . '/Public/Home/',
		//'{WEB_URL}' => 'http://dev.box.com/'
		'{WEB_URL}' => ''
    ),
	//'SERVER_URL' => 'http://dev.box.com/',  //地址
	'SERVER_URL' => '',  //地址
	'WEIXIN_OPTIONS' => array(
		'appid'=>'wx86fea9d2c6b26dc7', //填写高级调用功能的app id
		'appsecret'=>'02879a8b9688c453e983ca1bb78022cf' //填写高级调用功能的密钥
    ),
);