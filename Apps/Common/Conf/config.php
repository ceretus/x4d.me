<?php

define('URL_CALLBACK', 'http://www.tudouhan.com/login/callback.html?type=');

return array(
    'TMPL_ENGINE_TYPE' =>'PHP',

    'URL_MODEL'             =>  2,

    'DOMAIN' => 'http://www.tudouhan.com',

    'ERROR_PAGE'  =>  'http://www.tudouhan.com/error.html',

    'COOKIE_DOMAIN' => '.tudouhan.com',

    'APP_SUB_DOMAIN_DEPLOY' => 1, 
    'APP_SUB_DOMAIN_RULES' => array(
        'shicai.tudouhan.com' => 'Material',
    ),

    'DB_TYPE'   => 'mysql',
    'DB_HOST'   => 'rds153q33oejq9zkz007.mysql.rds.aliyuncs.com',
    'DB_NAME'   => 'tudouhan',
    'DB_USER'   => 'db_all',
    'DB_PWD'    => 'yuanhong_123_a_b_cFLY',
    'DB_PORT'   => 3306,
    'DB_PREFIX' => 'f_',
    'DB_CHARSET'=> 'utf8',

    /*'REDIS_HOST' => 'ef02893ee06c4de2.m.cnbja.kvstore.aliyuncs.com',
    'REDIS_AUTH' => 'hxredisaLYTT1c',*/

    'SESSION_OPTIONS'=>array(
        'type' => 'db',
        'expire' => 86400,
    ),
    'SESSION_TABLE' => 'f_session',

    //腾讯QQ登录配置
    'THINK_SDK_QQ' => array(
        'APP_KEY'    => '101284681', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '3ca0183f6cf1f29e12c7404dbf1b4c74', //应用注册成功后分配的KEY
        'CALLBACK'   =>  URL_CALLBACK . 'qq',
    ),
    //腾讯微博配置
    'THINK_SDK_TENCENT' => array(
        'APP_KEY'    => ' ', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        //'CALLBACK'   => URL_CALLBACK . 'tencent',
    ),
    //新浪微博配置
    'THINK_SDK_SINA' => array(
        'APP_KEY'    => '1285497384', //应用注册成功后分配的 APP ID
        'APP_SECRET' => 'e14138417e4ee151cd69b921e4d366a6', //应用注册成功后分配的KEY
        'CALLBACK'   => URL_CALLBACK . 'sina',
    ),
);