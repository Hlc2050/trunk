<?php
return array(
    //mysql 数据库组
    'db_mysql'=>array(
        'default'=>array(
            'host'=>'localhost', //数据库主机
            'user'=>'root', //数据库用户
            'passwd'=>'zhoufei', //数据库密码
            'dbname'=>'zk2partNewSystem_db', //数据库
            'port'=>'3306',
            'query_charset'=>'utf8', //编码
        )
    ),
    'domain'=>array(
        'domainlist_api_url'=>'',    //供检测的域名列表接口
        'replace_api_url'=>'http://2bufdy.ybnew.com/domain/checkReplace',  //替换接口
        'show_domain_url'=>'http://http://2bufdy.ybnew.com/domain/domainList',

    ),
    'weixin_access_token'=>'http://mm.dat56.com/index.php?s=/Home/Page/t_info.html',
    'openids'=>array(
        'oNJOCs3Hi-C3tyjCq4X15wT0UR5w',  //finn
    ),
    'db_piwik'=>array(
        'default'=>array(
            'host'=>'localhost', //数据库主机
            'user'=>'root', //数据库用户
            'passwd'=>'fang123', //数据库密码
            'dbname'=>'zk2bu_piwik', //数据库
            'port'=>'3306',
            'query_charset'=>'utf8', //编码
        )
    ),
);