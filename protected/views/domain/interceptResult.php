<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8">
    <title>域名拦截详情(二部)</title>
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
<h1>域名拦截渠道处理反馈详情</h1>

<ul class="domain_list">
    <li>
        <span class="name">被拦截域名：</span>

        <?php
        $old_ret = DomainList::model()->findByPk($r['domain_id']);
        $old_public_domain = $old_ret['is_public_domain'] == 1?"(公众号)":" ";
        $new_ret = DomainList::model()->findByPk($r['new_domain_id']);
        $new_public_domain = $new_ret['is_public_domain'] == 1?"(公众号)":" ";
        $old_url = $r['is_https']==1?"https://".$r['domain']:"http://".$r['domain'];
        $finance = $r['finance_pay_id'];
        $sno = $finance_sno[$finance];
        ?>
        <a href="<?php echo  $page['info']['domain']; ?>" style="color: red"><?php echo $page['info']['domain_info']; ?></a>
    </li>
    <li>
        <span class="name">域名类型：</span>
        <span><?php echo $page['info']['domain_type']; ?></span>
    </li>
    <li>
        <span class="name">推广id：</span>
        <?php echo $page['info']['promotion_id'] ? $page['info']['promotion_id'] : "无"; ?>
    </li>
    <li>
        <span class="name">推广人员：</span>
        <?php echo $page['info']['user_name']; ?>
    </li>
    <li>
        <span class="name">合作商：</span>
        <?php echo $page['info']['partner_name']; ?>
    </li>
    <li>
        <span class="name">渠道名称：</span>
        <?php echo $page['info']['channel_name']; ?>
    </li>
    <li>
        <span class="name">渠道编码：</span>
        <?php echo $page['info']['channel_code']; ?>
    </li>
    <li>
        <span class="name">备注：</span>
        <?php echo $page['info']['mark']; ?>
    </li>
    <li >
        <span class="name">新链接：</span>
        <?php if ($page['info']['promotion_id'] == 0) {
            echo '-'; ?>
        <?php } else { ?>
            <a class="new_url" href="<?php echo $page['info']['new_domain']; ?>"><?php echo $page['info']['new_domain']; ?></a>
        <?php } ?>
    </li>
    <?php  if($page['info']['is_replace'] == 1) { ?>
        <li>
            <span class="name">域名替换时间：</span>
            <?php echo $page['info']['upd_time']; ?>
        </li>
    <?php }  ?>

    <li>
        <span class="name">渠道处理结果反馈：</span>
        <?php echo $page['info']['replace_txt']; ?>
    </li>

</ul>

</body>
</html>




