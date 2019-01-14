<?php
//预定义数组
class vars {
	public static $fields=array(
		/******************************二部新系统*********************/
		//面向对象
		'sex'=>array(
			array('value'=>0,'txt'=>'全部'),
			array('value'=>1,'txt'=>'女'),
			array('value'=>2,'txt'=>'男'),
		),
		//微信号状态
		'weChat_status'=>array(
			array('value'=>0,'txt'=>'推广'),
			array('value'=>1,'txt'=>'暂停'),
			array('value'=>2,'txt'=>'满粉'),
			array('value'=>3,'txt'=>'封号'),
		),
        //排期表微信号状态
        'timetable_status'=>array(
            array('value'=>0,'txt'=>'推广'),
            array('value'=>1,'txt'=>'满'),
            array('value'=>2,'txt'=>'临'),
            array('value'=>3,'txt'=>'封'),
            array('value'=>4,'txt'=>'转'),
            array('value'=>5,'txt'=>'收'),
            array('value'=>6,'txt'=>'渠')
        ),
        //路由规则
        'url_rule' =>array(
            array('value'=>1,'txt'=>'规则1'),
            array('value'=>2,'txt'=>'规则2'),
            array('value'=>3,'txt'=>'规则3'),
            array('value'=>4,'txt'=>'规则4'),
        ),
        //域名状态
        'domain_status'=>array(
            array('value'=>0,'txt'=>'备用'),
            array('value'=>1,'txt'=>'正常'),
            array('value'=>2,'txt'=>'被拦截'),
            array('value'=>3,'txt'=>'内容被拦截'),
            array('value'=>4,'txt'=>'备案有问题')
        ),
		//微信号小组状态
		'weChatGroup_status'=>array(
			array('value'=>0,'txt'=>'备用'),
			array('value'=>1,'txt'=>'上线'),
			array('value'=>2,'txt'=>'下线'),
			array('value'=>3,'txt'=>'-'),
			array('value'=>4,'txt'=>'特殊'),

		),
		//推广状态
		'promotion_status'=>array(
			array('value'=>0,'txt'=>'正常'),
			array('value'=>1,'txt'=>'下线'),
			array('value'=>2,'txt'=>'暂停'),
		),
		//素材种类
		'material_group'=>array(
			array('value'=>0,'txt'=>'图文消息'),
			array('value'=>1,'txt'=>'图片'),
			array('value'=>2,'txt'=>'视频'),
			array('value'=>3,'txt'=>'语音'),
			array('value'=>4,'txt'=>'问卷'),
			array('value'=>5,'txt'=>'评论'),

		),
		//打款方式
		'charging_type'=>array(
			array('value'=>0,'txt'=>'cpc(pv)'),
			array('value'=>1,'txt'=>'cpc(uv)'),
			array('value'=>2,'txt'=>'cpc(ip)'),
			array('value'=>3,'txt'=>'cps'),
			array('value'=>4,'txt'=>'cpa'),
			array('value'=>5,'txt'=>'cpt'),
		),
		//打款公式
		'charging_formula'=>array(
			array('value'=>0,'txt'=>'pv*计费单价'),
			array('value'=>1,'txt'=>'uv*计费单价'),
			array('value'=>2,'txt'=>'ip*计费单价'),
		),

		//域名变更类型
		'domain_change_types'=>array(
			array('value'=>0,'txt'=>'人工'),
			array('value'=>1,'txt'=>'自动'),

		),

		'weixin_intercept_status'=>array(
			array('value'=>0,'txt'=>'正常','txt_color'=>''),
			array('value'=>1,'txt'=>'403错误','txt_color'=>''),
			array('value'=>2,'txt'=>'被拦截','txt_color'=>''),
			array('value'=>3,'txt'=>'查询失败','txt_color'=>''),
			array('value'=>4,'txt'=>'404','txt_color'=>''),
		),

		//域名类型
		'domain_types'=>array(
			array('value'=>0,'txt'=>'推广'),
			array('value'=>1,'txt'=>'跳转'),
			array('value'=>2,'txt'=>'白域名'),
			array('value'=>3,'txt'=>'短域名'),

		),

		//业务类型
		'businessTypes'=>array(
			array('value'=>1,'txt'=>'订阅号'),
			array('value'=>2,'txt'=>'非订阅号'),
			//array('value'=>3,'txt'=>'特殊非订阅号'),
		),

		//发货状态
		'delivery_status'=>array(
			array('value'=>1,'txt'=>'已发货'),
			array('value'=>2,'txt'=>'撤单'),
		),
		//效果表
		'effect_tables'=>array(
			array('value'=>0,'txt'=>'整体'),
			array('value'=>1,'txt'=>'推广人员'),
			array('value'=>2,'txt'=>'合作商'),
			array('value'=>3,'txt'=>'渠道'),
			array('value'=>4,'txt'=>'客服部'),
			array('value'=>5,'txt'=>'计费方式'),
			array('value'=>6,'txt'=>'图文'),
		),

      //缓存效果表
        'effect_cache_table'=>array(
          array('value'=>0,'txt'=>'渠道'),
          array('value'=>1,'txt'=>'合作商'),
        ),

   // 下单商品统计表
        'goods_effect'=>array(
            array('value'=>0,'txt'=>'下单商品统计表'),
            array('value'=>1,'txt'=>'下单商品统计图'),
            array('value'=>2,'txt'=>'下单商品对比图'),
        ),
		//黑名单表
        'blacklist_tables' =>array(
          array('value'=>0,'txt'=>'ip黑名单'),
          array('value'=>1,'txt'=>'手机黑名单'),
        ),
		//问卷状态
		'vote_status'=>array(
			array('value'=>0,'txt'=>'使用中'),
			array('value'=>1,'txt'=>'已过期'),
			array('value'=>2,'txt'=>'已过期'),
		),
		//推广类型
		'promotion_types'=>array(
			array('value'=>0,'txt'=>'标准'),
			array('value'=>1,'txt'=>'免域'),
			array('value'=>2,'txt'=>'开户'),
			array('value'=>3,'txt'=>'短域名'),
		),
		'article_types'=>array(
			array('value'=>0,'txt'=>'标准图文'),
			array('value'=>1,'txt'=>'语音问卷'),
			array('value'=>2,'txt'=>'论坛问答'),
            array('value'=>3,'txt'=>'微信图文'),
		),
		'fancePay_types'=>array(
			array('value'=>0,'txt'=>'打款'),
			array('value'=>1,'txt'=>'特殊'),
			array('value'=>2,'txt'=>'续费'),
		),
		'order_status'=>array(
			array('value'=>0,'txt'=>'未处理'),
			array('value'=>1,'txt'=>'交易成功'),
			array('value'=>2,'txt'=>'拒收'),
		),
		'best_time'=>array(
			array('value'=>1,'txt'=>'上午（9点-12点）'),
			array('value'=>2,'txt'=>'下午（12点-18点）'),
			array('value'=>3,'txt'=>'09：00-10：00'),
			array('value'=>4,'txt'=>'10：00-11：00'),
			array('value'=>5,'txt'=>'11：00-12：00'),
			array('value'=>6,'txt'=>'12：00-13：00'),
			array('value'=>7,'txt'=>'13：00-14：00'),
			array('value'=>8,'txt'=>'14：00-15：00'),
			array('value'=>9,'txt'=>'15：00-16：00'),
			array('value'=>10,'txt'=>'16：00-17：00'),
			array('value'=>11,'txt'=>'17：00-18：00'),
			array('value'=>0,'txt'=>'随时都可以'),

		),
        //小程序状态
        'miniApps_status'=>array(
            array('value'=>0,'txt'=>'未生成'),
            array('value'=>1,'txt'=>'已生成'),
            array('value'=>2,'txt'=>'上线'),
            array('value'=>3,'txt'=>'下线'),
        ),
        //域名词语
        'random_phrases'=>array(
            0=>'index',
            1=>'cgi-bin',
            2=>'user',
            3=>'id',
            4=>'url',
            5=>'from',
            6=>'login',
            7=>'php',
            8=>'frame'
        ),
        'payment'=>array(
            array('value'=>0,'txt'=>'支付宝'),
            array('value'=>1,'txt'=>'微信'),
            array('value'=>2,'txt'=>'货到付款'),

        ),
        'mobile_menus'=>array(
            array('value'=>1,'name'=>'微信号列表','url'=>'weChat/index','auth'=>'weChat','icon'=>'am-icon-wechat'),
            array('value'=>2,'name'=>'微信号小组','url'=>'weChatGroup/index','auth'=>'weChatGroup','icon'=>'am-icon-group'),
            array('value'=>3,'name'=>'微信效果表','url'=>'weChatEffect/index','auth'=>'weChatEffect','icon'=>'am-icon-list-alt'),
            array('value'=>4,'name'=>'排期表','url'=>'timetable/index','auth'=>'timetable','icon'=>'am-icon-calendar'),
            array('value'=>5,'name'=>'微信号查询表','url'=>'weChatQuery/index','auth'=>'weChatQuery','icon'=>'am-icon-search'),
            array('value'=>5,'name'=>'客服部数据报表','url'=>'dataMsg/serviceData','auth'=>'dataMsg','icon'=>'am-icon-folder'),
            array('value'=>6,'name'=>'推广组数据报表','url'=>'dataMsgGroup/index','auth'=>'dataMsgGroup','icon'=>'am-icon-book'),
            array('value'=>7,'name'=>'个人数据报表','url'=>'dataMsgUser/index','auth'=>'dataMsgUser','icon'=>'am-icon-eye'),
            array('value'=>8,'name'=>'待审批计划','url'=>'planAudit/index','auth'=>'planAudit','icon'=>'am-icon-eye'),
        ),
        //渠道拦截反馈结果
        'intercept_result'=>array(
            0=>'未处理',
            1=>'渠道链接已替换',
            2=>'部分渠道链接已替换',
        ),
        //计划管理
        'plan_manage'=>array(
            array('value'=>0,'txt'=>'待我审核'),
            array('value'=>1,'txt'=>'个人计划'),
            array('value'=>2,'txt'=>'组计划'),
            array('value'=>3,'txt'=>'提交记录'),
        ),
        'check_status'=>array(
            array('value'=>0,'txt'=>'未审核'),
            array('value'=>1,'txt'=>'组长审核未通过，待修改'),
            array('value'=>2,'txt'=>'组长审核通过'),
            array('value'=>3,'txt'=>'经理审核未通过，待修改'),
            array('value'=>4,'txt'=>'经理审核通过'),
        ),
        'week_day' =>array(
            array('value'=>0,'txt'=>'日'),
            array('value'=>1,'txt'=>'一'),
            array('value'=>2,'txt'=>'二'),
            array('value'=>3,'txt'=>'三'),
            array('value'=>4,'txt'=>'四'),
            array('value'=>5,'txt'=>'五'),
            array('value'=>6,'txt'=>'六'),
        ),
        //计费单价方式
        'charging_price'=>array(
            0=>array('txt'=>'cpc(pv)','mask'=>'按点击单价'),
            1=>array('txt'=>'cpc(uv)','mask'=>'按点击单价'),
            2=>array('txt'=>'cpc(ip)','mask'=>'按点击单价'),
            3=>array('txt'=>'cps','mask'=>'分成比例'),
            4=>array('txt'=>'cpa','mask'=>'单个进粉单价'),
            5=>array('txt'=>'cpt','mask'=>'万粉单价'),
        ),
	);

	/**
	 * 根据文本查找value
	 * @param $node string 节点
	 * @param $txt string 文本
	 * @return null 如果没有找到返回空
	 * author: yjh
	 */
	public static function get_value($node,$txt){
		foreach(vars :: $fields[$node] as $f) {
			if ($f['txt'] == $txt) {
				return $f['value']; //print_r($field);
			}
		}
		return null;
	}
	/**
	 * 返回某个节点的某个值
	 * $node   节点
	 * $value  值
	 */
	public static function get_field($node, $value) {
		// 遍历某个节点
		foreach(vars :: $fields[$node] as $f) {
			if ($f['value'] == $value) {
				return $f; //print_r($field);
			}
		}
		return array('value' => '', 'txt' => '-', 'txt_color' => '');
	}

	/**
	 * 根据值，返回文本或者HTML
	 *    $node   节点
	 *    $value  值
	$type   返回类型 txt 文本 html 带颜色属性的HTML文本
	 */
	public static function get_field_str($node, $value, $type = 'txt') {
		$field = vars :: get_field($node, $value); //print_r($field);
		return $field[$type];

		/* if ($type == 'txt') {
             return $field['txt'];
         } else {
             return '<font color="' . (isset($field['txt_color'])?$field['txt_color']:'') . '">' . $field['txt'] . '</font>';
         } */
	}
	/**
	 * 输出HTML表单
	 *    $params 参数数组 array('node'=>'','type'=>'','default'=>'')
	 *    -> $type 表单类型 select,checkbox,radio
	 *    -> $node    节点
	 *    -> $default 默认选中
	 *    -> $name    表单名称后缀，用于一个页面多次出现时候区分
	 */
	public static function input_str($params) {
		// 初始化
		$node = isset($params['node'])?$params['node']:'';
		$type = isset($params['type'])?$params['type']:'select';
		$default = isset($params['default'])?$params['default']:'';
		$name = isset($params['name'])?$params['name']:'';
		// 下拉框
		if ($type == 'select') {
			$html = '';
            $html.='<select name="'.$name.'">';
			foreach(vars :: $fields[$node] as $f) {
				$select = '';
				if (strlen($default) > 0 && $f['value'] == $default) $select = ' selected';
				$html .= '<option value="' . $f['value'] . '"' . $select . '>' . $f['txt'] . '</option>';
			}
            $html.='</select>';
			$html .= '';
			return $html;
		}
		// 单选框
		if ($type == 'radio') {
			$html = '';
			foreach(vars :: $fields[$node] as $f) {
				$select = '';
				if (strlen($default) > 0 && $f['value'] == $default) $select = ' checked';
				$html .= '<input type="radio" name="' . ($name?$name:$node) . '" value="' . $f['value'] . '"' . $select . '>&nbsp;' . $f['txt'] . '&nbsp;&nbsp;';
			}
			return $html;
		}
		// 复选框
		if ($type == 'checkbox') {
			$html = '';
			foreach(vars :: $fields[$node] as $f) {
				$select = '';
				if (strlen($default) > 0 && $f['value'] == $default) $select = ' checked';
				$html .= '<input type="checkbox" name="' .( $name?$name:$node)  . '" value="' . $f['value'] . '"' . $select . '>&nbsp;' . $f['txt'] . '&nbsp;&nbsp;';
			}
			return $html;
		}
		return '-';
	}

}
?>