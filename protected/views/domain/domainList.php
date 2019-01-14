<!DOCTYPE HTML>
<html>
<head><meta charset="UTF-8">
    <title>在用域名(二部)</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=no">

</head>
<style>
    body{color:#333;margin:0px;padding:0px;}
    h1,h3{text-align:center;}
    .domain_list{padding:10px;}
    .domain_list ul,.domain_list li{list-style: none;font-size:14px;padding:10px;}
    .domain_list li a{color:#333; text-underline: none; text-decoration: none;  }
    .domain_list li p{padding:5px;}

</style>
<body>
<h1>在用域名(二部新系统)</h1>
<h3>一共<?php echo count($page['domains']); ?>个域名正在推广,其中<?php echo $page['intercepts']; ?>个被拦截</h3>

<ul class="domain_list">
    <?php foreach($page['domains'] as $r){ ?>
        <?php $statusStr=vars::get_field_str('weixin_intercept_status',$r['status']); ?>
        <li><a href="http://<?php echo $r['domain']; ?>"><?php echo $r['domain']; ?></a>

            <?php
            if($r['status']==0) {
                echo '<font color=green>'.$statusStr.'</font>';
            }else if($r['status']==1) {
                echo '<font color="#e4ba12">'.$statusStr.'</font>';
            }else if($r['status']==2) {
                echo '<font color="red">'.$statusStr.'</font>';
            }else {
                echo '<font >'.$statusStr.'</font>';
            }
            ?>
            <p><?php echo $r['remark']; ?></p>
        </li>
    <?php }?>

</ul>

</body>
</html>




