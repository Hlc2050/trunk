<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3">
            <tr>
                <th align="left">批量匹配公众号落地页</th>
            </tr>
        </table>
    </div>
    <div class="mt10" id="container">
        <div class="l" id="container">
            &nbsp;&nbsp;&nbsp;&nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="button" class="but2" value="模板下载" onclick="location=\'' . $this->createUrl('weChat/urlTemplate') . '\'" />', 'auth_tag' => 'weChat_urlTemplate')); ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <form action="<?php echo $this->createUrl('weChat/importLandUrl'); ?>" method="post" enctype="multipart/form-data">
            <?php $this->check_u_menu(array('code' => '<input type="file"  name="filename"  />', 'auth_tag' => 'weChat_importLandUrl')); ?>
            &nbsp;
            <?php $this->check_u_menu(array('code' => '<input type="submit" name="submit" class="but2" value="导入" />', 'auth_tag' => 'weChat_importLandUrl')); ?>
        </form>
    </div>
</div>
<div class="main mbody">

</div>
