<?php require(dirname(__FILE__)."/head.php"); ?>
<script>


</script>
<body style="text-align: center">
<div class="msg_dialog">
    <img src="<?php echo $config['css_url']?>/img/icons/<?php echo isset($msg['icon'])?$msg['icon']:'question'; ?>.png">
    <span style="margin-left: 10px"><?php echo isset($msg['icon'])?$msg['msgwords']:''; ?></span>
</div>
    <?php echo isset($msg['jscode'])?$msg['jscode']:'';?>
</body>
</html>