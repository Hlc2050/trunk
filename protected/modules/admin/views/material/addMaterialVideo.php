<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<input type="hidden" value="<?php echo $this->get('num'); ?>" id="num">
<div class="main mhead">
    <div class="snav">
        <table class="tb3">
            <tr>
                <th style="font-size: small;" align="left">选择插入的视频</th>
            </tr>
        </table>
    </div>
    <div class="mt10">

    </div>
</div>
<div class="main mbody">
    <table style="width: 100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>视频名称</th>
            <th>视频大小</th>
            <th>支持人员</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($page['listdata']['list'] as $key => $val) {
            $videoUrl = Resource::model()->findByPk($val['video_id'])->resource_url;
            ?>
            <tr>
                <td><?php echo $val['id']; ?></td>
                <td><?php echo $val['video_name']; ?></td>
                <td><?php echo $this->sizecount($val['video_size']) ?></td>
                <td><?php echo AdminUser::model()->findByPk($val['support_staff_id'])->csname_true; ?></td>
                <td>
                    <a type="button" href="#" class="but1" attr-data="<?php echo $videoUrl; ?>"
                       onclick="backfile(this);">添加视频</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>

</div>
<style>
    td{
        text-align:center;
        vertical-align:middle
    }
    tr{
        line-height: 30px;
    }
</style>

<script type="text/javascript">
    function backfile(obj) {
        try {
            var $url = $(obj).attr('attr-data');
            var $num = $('#num').val()
            console.log($url);
            artDialog.opener.addOneVideo($url,$num);
            alert('添加成功');
            artDialog.close();
        } catch (e) {
            alert(e.message);
        }
    }

</script>

