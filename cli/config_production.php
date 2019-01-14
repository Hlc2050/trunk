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
    'weixin_access_token' => 'http://sysadmin.huashengkan.com/portal/Weixin/getToken',
    'weixin_template_url' => 'http://sysadmin.huashengkan.com/portal/Weixin/getTemplateId?type_id=1',
    'openids'=>array(
        'oNJOCs3Hi-C3tyjCq4X15wT0UR5w',  //finn
    ),
    //域名检测one接口
    'domain_check_one' => 'http://vip.weixin139.com/weixin/wx_domain.php?user=yaoyy1&key=9d9ef5e8a830853def00984a141d74f4',
    //域名检测two接口
    'domain_check_two' => 'http://vip.weixin139.com/weixin/wx_domain.php?user=qaoqq2&key=bfaeeed1d4956a8a4759e1261ff2c0b0',
    //微信发送模板接口
    'send_msg_url'=>'https://api.weixin.qq.com/cgi-bin/message/template/send',
    //ip检测接口
    'check_ip_url' => 'http://ip.taobao.com/service/getIpInfo.php',

    'super_admin_id' => 1,
);
