<?php
return array(
    //mysql 数据库组
    'db_mysql' => array(
        'default' => array(
            'host' => '192.168.0.227', //数据库主机
            'user' => '2bu', //数据库用户
            'passwd' => 'xmzk2bu666', //数据库密码
            'dbname' => 'zk2partnewsystem_db', //数据库
            'port' => '3306',
            'query_charset' => 'utf8', //编码
        )
    ),
    'db_piwik' => array(
        'default' => array(
            'host' => 'localhost', //数据库主机
            'user' => 'root', //数据库用户
            'passwd' => 'root', //数据库密码
            'dbname' => 'piwik_db', //数据库
            'port' => '3306',
            'query_charset' => 'utf8', //编码
        )
    ),

    'domain' => array(
        'domainlist_api_url' => '',    //供检测的域名列表接口
        'replace_api_url' => 'http://admin.two.com/domain/checkReplace',  //替换接口
        'show_domain_url' => 'http://admin.two.com/domain/domainList',

    ),
    'weixin_access_token' => 'http://mm.xjich.net/index.php?s=/Home/Page/t_info.html',
    'openids' => array(
        'oNJOCs3Hi-C3tyjCq4X15wT0UR5w',  //finn
    ),
    //超级管理员默认用户id
    'super_admin_id' => 1,
    //电销pid
    'dx_bid' => 2,
    //域名检测one接口
    'domain_check_one' => 'http://vip.weixin139.com/weixin/wx_domain.php?user=yaoyy1&key=9d9ef5e8a830853def00984a141d74f4',
    //域名检测two接口
    'domain_check_two' => 'http://vip.weixin139.com/weixin/wx_domain.php?user=qaoqq2&key=bfaeeed1d4956a8a4759e1261ff2c0b0',
    //微信发送模板接口
    'send_msg_url'=>'https://api.weixin.qq.com/cgi-bin/message/template/send',
    //ip地址检测接口
    'ip_check_url' => 'http://ip.taobao.com/service/getIpInfo.php',
);