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
        'frame_*',
        'timetable_getPromotionStaffByPg',
        'wechat_getCharacter',
        'wechat_getChargingType',
        'wechat_getDepartment',
        'wechat_getGoodsByCs',
        'wechat_getGoods',
        'wechatGroup_searchHandler',
        'wechatGroup_searchHandler',

        'timetable_comAdd',
        'timetable_allAdd',
        'timetable_editList',

        'planAudit_audit',
        'planAudit_auditEdit',
        'dataMsg_serviceGroupData',
        'dataMsg_serviceUserData',
        'dataMsgGroup_groupUserData',
        'planAudit_index',
        'dataMsg_serviceData',
        'dataMsgGroup_index',
        'dataMsgUser_index',

    );


}

?>