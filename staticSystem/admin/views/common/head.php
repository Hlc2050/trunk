<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $config['system_name'] ?></title>
<link rel="stylesheet" media="screen" href="<?php echo $config['css_url']; ?>base.css?v2015031213" />
<link rel="stylesheet" media="screen" href="<?php echo $config['css_url']; ?>default/admin.css?v2015031213" />
<script src="<?php echo $config['css_url']; ?>lib/js/jquery-1.7.1.min.js" ></script>
<script src="<?php echo $config['css_url']; ?>lib/js/jquery.freezeheader.js" ></script>
<script  src="<?php echo $config['css_url']; ?>lib/artDialog4.1.7/artDialog.js?skin=default" ></script>
<script  src="<?php echo $config['css_url']; ?>lib/artDialog4.1.7/plugins/iframeTools.source.js" ></script>
<link rel="stylesheet" href="<?php echo $config['css_url'];?>lib/artDialog-master/css/ui-dialog.css">
<script src="<?php echo $config['css_url']; ?>lib/artDialog-master/dist/dialog-plus.js"></script>
<script src="<?php echo $config['css_url']; ?>js/plupload.full.min.js" ></script>
<script src="<?php echo $config['css_url']; ?>js/common.js" ></script>

    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo $config['css_url']; ?>lib/font-awesome.css" />
</head>
<body <?php echo isset($page['body_extern'])?$page['body_extern']:''; ?>>
<script>
    $(document).ready(function() {
        try {
            var h1 = $(window).height();
            var h2 = $(".fixTh").offset().top;
            var h_tb=$('.fixTh').height();
            var h3 = $('.mfoot').height();
            var h = h1 - h2 - h3 - 70;
            if(h_tb+h2+h3<h1){
                console.log('need not fixed  ');
                return false;
            }
            $(".fixTh").freezeHeader({'height': h + 'px'});
        }catch(e){console.log(e.message)}

//$(".fixTh").freezeHeader();
    });
</script>

