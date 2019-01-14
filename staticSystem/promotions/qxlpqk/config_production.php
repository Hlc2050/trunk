<?php
return array(
    //mysql 数据库组
    'db_piwik'=>array(
        'default'=>array(
            'host'=>'10.28.30.129', //数据库主机
            'user'=>'piwik', //数据库用户
            'passwd'=>'q02^VhbowjnZ', //数据库密码
            'dbname'=>'zk2bu_piwik_db', //数据库
            'port'=>'3306',
            'query_charset'=>'utf8', //编码
        )
    ),
   //主数据库
    //mysql 数据库组
    'db_mysql'=>array(
        'default'=>array(
            'host'=>'10.28.30.129', //数据库主机
            'user'=>'zk2bu', //数据库用户
            'passwd'=>'7sy<x3uBPsnqft', //数据库密码
            'dbname'=>'zk2partNewSystem_db', //数据库
            'port'=>'3306',
            'query_charset'=>'utf8', //编码
        )
    ),

    //订单库配置
    'db_order'=>array(
        'default'=>array(
            'host'=>'10.28.30.129', //数据库主机
            'user'=>'zk2bu', //数据库用户
            'passwd'=>'7sy<x3uBPsnqft', //数据库密码
            'dbname'=>'zk2partorder_db', //数据库
            'port'=>'3306',
            'query_charset'=>'utf8', //编码
        )
    ),

    'domain'=>array(
        'domainlist_api_url'=>'',    //供检测的域名列表接口
        'replace_api_url'=>'http://www.taopeiku.com/domain/checkReplace',  //替换接口
        'show_domain_url'=>'http://www.taopeiku.com/domain/domainList',

    ),
    'weixin_access_token'=>'http://mm.xjich.net/index.php?s=/Home/Page/t_info.html',
    'openids'=>array(
        'oNJOCs3Hi-C3tyjCq4X15wT0UR5w',  //finn
    ),
);
