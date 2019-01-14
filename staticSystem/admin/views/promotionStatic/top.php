<?php $page['body_extern']='style="overflow:hidden;" class="body_top"' ;?>
<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script>
    function set_admin_style(style_folder) {
        $.cookie("admin_style", style_folder, {expires: 1000, path: '/'});
        parent.location.reload();
    }
</script>
<style>
    .stylebox a {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin-right: 5px;
        overflow: hidden;
        text-indent: 100px;
        line-height: 100px;
        vertical-align: middle;
    }
</style>

<div class="l">
    <a href="#" target="main" class="logoa" style=" display:block; height:48px;line-height:48px;overflow:hidden;color:#fff;">
        <?php echo $config['system_name']; ?></a>
</div>


<div style="height:48px;text-align:right;float:right;">
    <div class="r" style=" padding:13px 10px 0 0;">
        欢迎登录，<?php echo $account; ?>
        <a href="user.php?action=logout" target=_parent><img src="/static/admin/img/out.png"></a>
    </div>
</div>


<div id="sound_box" style=" height:1px; overflow:hidden; position:relative; width:1px; position:absolute;"></div>

</body>
</html>