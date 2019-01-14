<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Yii Blog Demo',
		
	'theme'=>'default',	
	
	
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*', 
		'application.components.*',
		'application.extensions.PHPExcel.*',
		'application.extensions.ArrayGroupBy.*',
		'application.extensions.getid3.*',
		'application.extensions.DbConnectionMan',
	),

	'defaultController'=>'site',

	'modules'=>array(
		'admin',
		'upload',
		'user',
        'mobile',
	),
	
	'preload'=>array('log'),
	// application components
	'components'=>array(
		'session'=>array(
				'timeout'=>3600*24*365,
		),
		'user'=>array(
					//'class'=>'UserWebUser',//后台登录类实例
					'stateKeyPrefix'=>'user',//后台session前缀
					'allowAutoLogin'=>true,
					'loginUrl'=>'user/site/login',
					// 'returnUrl'=>Yii::app()->createUrl('admin/node/index'),
			),
	
		// uncomment the following to use a MySQL database
		
		//测试域名自动根据检测数据库配置文件
		'db'=>require(dirname(__FILE__).'/'.(strpos($_SERVER['HTTP_HOST'],'localhost')?'db_test':'db').'.php'),
		'zk2bu_piwik_db'=>require(dirname(__FILE__).'/zk2bu_piwik_db.php'),
		'order_db'=>require(dirname(__FILE__).'/order_db.php'),
		'cache' => array (
		
				'class' => 'system.caching.CFileCache',
				'directoryLevel' => 1,
			
		),
		'redis'=> array(
			'class'=>'system.caching.CRedisCache',
			'hostname'=>'192.168.13.227',
			'port'=>6379,
			'database'=>0,
            'password' => '123456'
		),
		'dbcache'=>array(
				'class'=>' system.caching.CDbCache',
		),





		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'urlManager'=>require(dirname(__FILE__).'/url.php'),
//         'log'=>array(
//			'class'=>'CLogRouter',
//			'routes'=>array(
//				array(
//					'class'=>'CFileLogRoute',
//					'levels'=>'error, warning',
//				),
//				// 下面显示页面日志
//
//            array(
//	                'class'=>'CWebLogRoute',
//	                'levels'=>'trace',
////	                级别为trace
//	                'categories'=>'system.db.*'
//	                //只显示关于数据库信息,包括数据库连接,数据库执行语句
//	            ),
//			),
//		),
		'search_article' => array(
				'class' => 'application.extensions.xunsearch.EXunSearch',
				'xsRoot' => ' /usr/local/xunsearch',  // xunsearch 安装目录
				'project' => '/usr/local/xunsearch/sdk/php/app/shouyou_article.ini', // 搜索项目名称或对应的 ini 文件路径
				'charset' => 'utf-8', // 您当前使用的字符集（索引、搜索结果）
		),
		'search_game' => array(
				'class' => 'application.extensions.xunsearch.EXunSearch',
				'xsRoot' => ' /usr/local/xunsearch',  // xunsearch 安装目录
				'project' => '/usr/local/xunsearch/sdk/php/app/shouyou_game.ini', // 搜索项目名称或对应的 ini 文件路径
				'charset' => 'utf-8', // 您当前使用的字符集（索引、搜索结果）
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
    'params'=>array_merge(require(dirname(__FILE__).'/params.php'),require(dirname(__FILE__).'/mobile.php')),
);