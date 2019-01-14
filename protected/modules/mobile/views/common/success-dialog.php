<?php require(dirname(__FILE__) . "/../common/login-header.php"); ?>
</head>
<body>
<script type="text/javascript">
    var $success = AMUI.dialog.success({
        content: '<span class=\'am-success am-icon-check\' style=\'color:green\'>&nbsp;<?php echo $msg['content']?></span>'
    });
    setTimeout(function () {
        $success.modal('close');
        <?php if(isset($msg['url'])){
        ?>
        var url = "<?php echo $msg['url']?>";
        var ua = window.navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == 'micromessenger') {
            url = url + '?time=' + ((new Date()).getTime());
        }
        window.location.href = url;        <?php
        }else {?>
        window.history.go(-2);
        return false;
        <?php } ?>
    }, 2000);

</script>
<?php echo isset($msg['jscode']) ? $msg['jscode'] : ''; ?>

</body>
</html>