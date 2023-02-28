<?php
return array(
//'配置项'=>'配置值'
//数据库配置
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => 'localhost', // 服务器地址
    'DB_NAME' => '', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '', // 密码
    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'dy_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
//
    'URL_MODEL' => '2', //URL模式    
    'URL_CASE_INSENSITIVE' => true,
    'LOG_RECORD' => true, //是否开启session
//Memcache config
    //'DATA_CACHE_TYPE' => 'Memcache',
    //'MEMCACHE_HOST' => 'tcp://127.0.0.1:11211',
    //
    'FILE_UPLOAD_TYPE' => 'Oss',
    'UPLOAD_TYPE_CONFIG' => array(
        'url'=>'niaoting-bucket.oss-cn-shanghai.aliyuncs.com',
        'access_id' => 'LTAItbt8uWoW488i', //阿里云Access Key ID
        'access_key' => 'ls7zl44OUoyDs5Xp09gFSdnn5MN58G', //阿里云Access Key Secret
        'bucket' => 'niaoting-bucket' //阿里云的bucket
    ),
//
    "LOAD_EXT_FILE" => "pinyin",
);
