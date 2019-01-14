<?php
return array(
    'urlFormat' => 'path',
    'showScriptName' => false,
    'rules' => array(
        'admin2050' => 'admin/site/login',
        'admin2060' => 'mobile/site/login',
        'goto/?' => 'site/goto',
        //跳转域名规则
        '<id:\d\d\d\d>_goto/<pid:\w+>.html'=>'site/goto',
        '<id:\d\d\d\d>/<t:[a-z]*?>/<c:\d>/<pid.*>.html'=>'site/goto',
        //落地页域名规则
        '<id:\d\d\d\d>_pid/<pid:\w+>/<nid:\d>.html' => '',
        '<id:\d\d\d\d>_pid/<pid:\w+>.html' => '',
        '<t:[a-z]*?>/<id:\d\d\d\d>/<pid:\w+>/<nid:\d>.html' => '',
        '<t:[a-z]*?>/<id:\d\d\d\d>/<pid:\w+>.html' => '',
        '<id:[1-9][0-9]{9}>/<pid:\w+>/<nid:\d>.html' => '',
        '<id:[1-9][0-9]{9}>/<pid:\w+>.html' => '',
        //加密规则
        '<did:[0-9]{16}>/<nid:\d>' => '',
        '<did:[0-9]{16}>' => '',
        '<t:(index|cgi-bin|user|id|frame|url|from|login|php)>/<did:[0-9]{16}>/<nid:\d>' => '',
        '<t:(index|cgi-bin|user|id|frame|url|from|login|php)>/<did:[0-9]{16}>' => '',
        '<t:(index|cgi-bin|user|id|frame|url|from|login|php)>/<mid.*>/<nid:\d>' => '',
        '<t:(index|cgi-bin|user|id|frame|url|from|login|php)>/<mid.*>' => '',
        'admin/site/login' => 'site/error',
//        'mobile/site/login' => 'site/error',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ),
);