<?php

class PublicAuth
{
//定义左侧按钮
    /*用户权限字符串
    控制器名，  控制器名_方法名  ,支持 星号 ，参见 AdminModule.php
    */

    //允许全体管理员使用的方法
    public static $public_user_auth = array(
        'site_*',
        'api_*',
        'post_verifyCode',
        'frame_index',
        'frame_left',
        'frame_top',
        'frame_welcome',
        'infoCategory_showCategoryLeftmenu',
        'infoCategory_getCateTotalInfo',
        'info_cutImage',
        'linkage_getSelectClassForEdit',
        'linkage_getCateChildClass',

        /**
         * 推广默认方法
         */
        'promotion_getChannel',
        'promotion_getOnlineDate',
        'promotion_getOnlineChannelDate',
        'promotion_getOtherData',
        'promotion_getChannelData',
        'promotion_getDataById',

        //素材默认方法
        'material_showPic',//显示图片方法
//        'material_deletePic',//删除单张图片
//        'material_changePicGroup',//修改图片组别
//        'material_editPicName',//修改图片名称
        'material_addMaterialPics',//图文添加素材库图片
        'material_addMaterialVideos',//图文添加素材库视频
        'material_showPreview',//图文添加素材库图片
        'material_deleteReviewDetail',//删除评论编辑页面标签

        //微信号默认方法
        'weChat_getCharacter',//Ajax
        'weChat_getDepartment',//Ajax
        'weChat_showQRCode',//二维码查看
        'weChat_getChargingType',//Ajax
        'customerService_getGoods',//获取商品
        //微信号小组默认方法
        'weChatGroup_searchHandler',//查询
        'weChatGroup_getSuitableWechatIds',//选取满足条件的为微信
        //财务打款
        'infancePay_inputtip',
        'infancePay_getChargingTypes',
        'infancePay_getDataById',
        'fixedCost_wechatIndex',
        //参数分组管理
        'paramsGroup_add',//新增参数分组
        'paramsGroup_edit',//修改参数分组
        'paramsGroup_delete',//删除选中
        'paramsGroup_saveOrder',//修改排序
        //菜单管理
        'adminModule_font',//图标选择
        //类别配置
        'linkage_saveOrder',//修改菜单排序、层级、标识等
        'linkage_getChildLinkageForList',//AJAX
        'linkage_getSelectClassForEdit',//AJAX
        'linkage_getCateChildClass',//AJAX

        //效果表
//        'effectTable_export',//效果表导出权限
        //选择图标
        'adminModules_font',

        'piwikHour_insertLongPress',

        //业务运营表
        'bssOperationTable_getGoodsByCs',
        'bssOperationTable_getPromotionStaffByPg',
        'bssOperationTable_getChargingTypes',
        
        //添加图文默认方法
        'material_getPSQByCatId',
        
        //编辑合作商费用日志默认方法
        'partnerCost_toEdit',

        //缓存图文模板数据
        'material_saveTempData',

        //获取图文列表
        'promotion_getArticleList',

        //获取合适的域名
        'promotion_getSuitableDomains',

        //微信匹配落地页模板
        'weChat_urlTemplate',

        'material_addReceiveStyle',
        'material_editForumReview',
        'material_editSelectReview',
        'material_getVotePage',
        'material_getReviewList',
        'material_getPicGroups',

        'orderTemplete_getOrderTempletes',
        //排期
        'timetable_getWachat',
        'timetableType_getTypeValue',
        'timetableType_getTypeValueBYtid',
        'timetableType_getTypeValueAjax',
        // 下单商品
        'orderGoodsEffect_getGoodsCheckbox',
        'orderGoodsEffect_getGoodsByGroup',
        'packageManage_getPackages',
        'orderManage_unExportNum',
        'orderManage_del',

        //域名查询
        'promotion_getPromotionDomain',
        'promotion_getGotoDomain',

        //计划管理
        'planWeek_getGroupPlanTotal',
        'dataStatistic_serviceDetail',
        'planMonth_agree',
        'planMonth_refuse',
        'csPlanMonth_getServiceDetail',
        'planMonth_getGroupPlanTotal',
        'csTimetable_GetTgPeople',
        'planWeek_getGroupPlanData',
        'planWeek_auditUserPlan',
        'planWeek_auditGroupPlan',

        //财务打款打印
        'infancePay_print',
        //渠道类型设置
        'channelTypeManage_isSameName',
        //选择推广域名
        'promotion_selectDomains',
        'promotion_freshDomains',
    );


}

?>