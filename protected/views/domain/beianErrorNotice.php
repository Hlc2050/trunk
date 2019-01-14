<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>域名备案错误详情(二部)</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=no">

</head>
<style>
    body {
        color: #333;
        margin: 0px;
        padding: 0px;
    }

    h1, h3 {
        text-align: center;
    }

    .domain_list {
        padding: 10px;
    }

    .domain_list ul, .domain_list li {
        list-style: none;
        font-size: 14px;
        padding: 10px;
    }

    .domain_list li a {
        text-underline: none;
        text-decoration: none;
    }

    .domain_list li span.name {
        color: #333;
        text-underline: none;
        text-decoration: none;
        font-weight: bold;
    }

    .domain_list li p {
        padding: 5px;
    }

    .detail_attr {
        text-align: center;
        color: #8c8c8c;
    }

    .new_url {
        color: green;
        word-wrap: break-word;
        word-break: normal;
    }

</style>
<body>
<h1>域名备案错误处理详情</h1>
<div class="detail_attr">
    <?php echo $page['start'] . "-" . $page['end']; ?>
</div>
<h3>一共<?php echo $page['intercepts']; ?>个域名备案错误</h3>

<ul class="domain_list">
    <?php
    $i = 1;
    $finance_sno = $page['finance_sno'];
    $user_id = $page['user_id'];
    $replace_txt = vars::$fields['intercept_result'];
    foreach ($page['domains'] as $r) { ?>
        <?php
        $statusStr = vars::get_field_str('domain_types', $r['domain_type']);
        $info = DomainList::model()->findByPk($r['new_domain_id']);
        if ($r['domain_type'] == 0 || $r['domain_type'] == 3) {
//            $new_url = $info->domain;
            if ($info->is_https == 1) $href = "https://" . $info->domain;
            else $href = "http://" . $info->domain;
        } else {
            $white_domain = '';
            $new_domain = DomainList::model()->findByPk($r['new_domain_id']);
            // 拦截域名为跳转域名
            if ($r['domain_type'] == 1) {
                $goto_domainInfo = $new_domain;
                if ($r['is_white_domain'] == 0) {
                    $white_domainInfo = DomainList::model()->findByPk($r['promotion_domain']); //域名
                    if ($white_domainInfo) {
                        if ($white_domainInfo->is_https == 1) {
                            $white_domain = "https://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                        } else {
                            $white_domain = "http://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                        }
                    } else {
                        $white_domain = Yii::app()->params['basic']['white_domain'];//白域名
                    }
                }
            }
            //拦截域名为白域名
            if ($r['domain_type'] == 2) {
                $goto_domainInfo = DomainList::model()->findByPk($r['promotion_domain']); //域名
                $white_domainInfo = $new_domain;
                if ($r['is_white_domain'] == 0) {
                    if ($white_domainInfo->is_https == 1) {
                        $white_domain = "https://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                    } else {
                        $white_domain = "http://" . $white_domainInfo->domain . "/qq/?go=";//白域名
                    }
                }
            }
            if ($goto_domainInfo->is_https == 1) {
                $new_url = "https://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $r["promotion_id"] . "_" . $r['channel_code'] . "_" . $r["finance_pay_id"] . ".html";
            } else {
                $new_url = "http://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $r["promotion_id"] . "_" . $r['channel_code'] . "_" . $r["finance_pay_id"] . ".html";
            }
            if ($white_domain != '') {
                $new_url = $white_domain . urlencode($new_url);//跳转链接
            }
//            $goto_domainInfo = DomainList::model()->findByPk($r['new_domain_id']); //域名
//            $white_domainInfo = DomainList::model()->findByPk($r['white_domain_id']); //域名
//            $goto_domain = $goto_domainInfo->domain;//跳转域名
//            if ($white_domainInfo) {
//                if ($white_domainInfo->is_https == 1) {
//                    $white_domain = "https://" . $white_domainInfo->domain . "/qq/?go=";//白域名
//                } else {
//                    $white_domain = "http://" . $white_domainInfo->domain . "/qq/?go=";//白域名
//                }
//            } else {
//                $white_domain = Yii::app()->params['basic']['white_domain'];//白域名
//            }
//            if ($goto_domainInfo->is_https == 1) {
//                $new_url = "https://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $r["promotion_id"] . "_" . $r['channel_code'] . "_" . $r["finance_pay_id"] . ".html";
//            } else {
//                $new_url = "http://" . $goto_domainInfo->domain . "/" . date("md", time()) . "_goto/" . $r["promotion_id"] . "_" . $r['channel_code'] . "_" . $r["finance_pay_id"] . ".html";
//            }
//            if ($white_domain != '') {
//                $new_url = $white_domain . urlencode($new_url);//跳转链接
//            }
            $href = $new_url;
            $finance = $r['finance_pay_id'];
            $sno = $finance_sno[$finance];
        }
        ?>
        <li>
            <span class="name"><?php echo $i;
                $i++; ?>.</span>
        </li>
        <li>
            <span class="name">应用类型：</span>
            <span><?php echo  $r['application_type'] == 1?'静态应用':'普通应用'; ; ?></span>
        </li>
        <li>
            <span class="name">备案错误域名：</span>
            <?php $old_url = $r['is_https']==1?"https://".$r['domain']:"http://".$r['domain'] ?>
            <a href="<?php echo $old_url; ?>" style="color: red"><?php echo $old_url; ?></a>
        </li>
        <li>
            <span class="name">域名类型：</span>
            <span><?php echo $statusStr; ?></span>
        </li>
        <li>
            <span class="name">推广id：</span>
            <?php echo $r['promotion_id'] ? $r['promotion_id'] : "无"; ?>
        </li>
        <li>
            <span class="name">推广人员：</span>
            <?php echo $r['tg_name']; ?>
        </li>
        <li>
            <span class="name">合作商：</span>
            <?php echo $r['partner_name'] ? $r['partner_name'] : '-'; ?>
        </li>
        <li>
            <span class="name">渠道名称：</span>
            <?php echo $r['channel_name'] ? $r['channel_name'] : '-'; ?>
        </li>
        <li>
            <span class="name">渠道编码：</span>
            <?php echo $r['channel_code'] ? $r['channel_code'] : '-'; ?>
        </li>
        <li>
            <span class="name">备注：</span>
            <?php echo $r['mark']; ?>
        </li>
        <li <?php if($r['domain_type']==0 || $r['promotion_id']==0){?> style="border-bottom: 1px solid #e7e7eb;" <?php }?>>
            <span class="name">新链接：</span>
            <?php if ($r['promotion_id'] == 0) {
                echo '-'; ?>
            <?php } else { ?>
                <a class="new_url" href="<?php echo $href; ?>"><?php echo $href; ?></a>
            <?php } ?>
        </li>
        <?php  if($user_id == $sno && $r['domain_type']!=0 && $r['promotion_id']!=0) { ?>
            <li style="border-bottom: 1px solid #e7e7eb;">
                <form method="post" action="<?php echo $this->createUrl('/domain/editResult'); ?>">
                    <span class="name">渠道处理结果反馈：</span>
                    <select name="is_replace" <?php  if($r['is_replace'] == 1)  echo 'disabled'; ?>>
                        <option value="0" <?php  if($r['is_replace'] == 0)  echo 'selected'; ?>><?php echo $replace_txt[0];?></option>
                        <option value="1" <?php  if($r['is_replace'] == 1)  echo 'selected'; ?>><?php echo $replace_txt[1];?></option>
                        <option value="2" <?php  if($r['is_replace'] == 2)  echo 'selected'; ?>><?php echo $replace_txt[2];?></option>
                    </select>
                    <?php  if($r['is_replace'] != 1) { ?>
                        <p style="margin-left: 40%"><input type="button" value="提交" onclick="edit_resule(this,<?php echo $user_id?>,<?php echo $r['id']?>)"></p>
                    <?php } ?>
                </form>

            </li>
        <?php } ?>
        <?php  if($user_id != $sno && $r['domain_type']!=0 && $r['promotion_id']!=0) { ?>
            <li style="border-bottom: 1px solid #e7e7eb;">
                <span class="name">渠道处理结果反馈：</span>
                <?php echo $replace_txt[$r['is_replace']];?>
            </li>
        <?php }?>
    <?php } ?>

</ul>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/jquery-1.7.1.min.js" ></script>
<script>
    function edit_resule(dom,user_id,intercept_id) {
        var from = $(dom).parent().parent();
        var is_replace = from.find('select').val();
        if (is_replace == 1) {
            if (!confirm('修改为渠道链接已替换后，将不可再编辑，确认修改吗？')) {
                return false;
            }
        }
        $.ajax({
            url: '/domain/editResult',
            type: 'POST',
            data:{'user_id':user_id,'intercept_id':intercept_id,'is_replace':is_replace},
            dataType: 'json',
            success: function (data, status) {
                var ret = data.ret;
                if (ret != 200) {
                    alert(data.msg);
                    return false;
                } else {
                    if (is_replace == 1) {
                        from.find('select').attr('disabled','disabled');
                        $(dom).parent().remove();
                    }
                }

            },
            fail: function (err, status) {
            }
        });
    }
</script>
</body>
</html>




