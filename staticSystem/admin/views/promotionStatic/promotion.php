<?php require(dirname(__FILE__) . "/../common/head.php");
$status_str = array(
    '2'=>'被拦截',
    '3'=>'内容被拦截',
    '4'=>'备案错误',
);
?>
<div class="main mhead">
    <div class="snav">系统 » 页面管理</div>
</div>
<div class="main mbody">
    <div class="list08 clearfix">
        <div class="mt10 clearfix">
            <form action="promotionStatic.php" method="get" target="main">
                ID：
                <input style="width: 100px" type="text" name="promotion_id" class="ipt"
                       value="<?php echo $_GET['promotion_id']; ?>">&nbsp;
                渠道名称：
                <input style="width: 100px" type="text" name="channel_name" class="ipt"
                       value="<?php echo $_GET['channel_name']; ?>">&nbsp;
                <input type="hidden" name="action" value="promotion">
                <input type="submit" class="but" value="查询">
            </form>
        </div>
        <div class="mt10">
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th width="80">ID</th>
                    <th width="80">渠道名称</th>
                    <th width="100">渠道编码</th>
                    <th width="80">跳转地址</th>
                    <th width="100">落地页地址</th>
                    <th width="100">文件夹名称</th>
                    <th width="80">推广人员</th>
                    <th width="80">推广类型</th>
                    <th width="80">状态</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($promotions->list as $r) {
                    $folder = getPromotionFolder($r->id);
                    $path = ROOT_PATH.$config['promotion_path'].$folder;
                ?>
                    <tr>
                        <td><?php echo $r->id; ?></td>
                        <td><?php echo $r->channel_name; ?></td>
                        <td><?php echo $r->channel_code; ?></td>
                        <td>
                            <?php
                            $goto_domain = $r->goto_url;
                            if ($r->status == 2) { ?>
                                <span style="font-weight: bold;color:red;">暂停中</span>
                            <?php } elseif ($goto_domain == 2) { ?>
                                <span style="font-weight: bold;color:red;">被拦截</span>
                            <?php } else {
                                if (!$goto_domain) {
                                    echo "<b>无</b>";
                                } else {
                                    ?>
                                    <a href="#"
                                       onclick="dialog({title:'跳转链接，直接复制一下',content:$(this).attr('data-clipboard-text')}).showModal();"
                                       data-clipboard-text="<?php echo $goto_domain; ?>">点击查看</a>
                                <?php }
                            } ?>
                        </td>
                        <td>
                            <?php
                            $link_url=buildTgLink($r,$r->id,$r->promotion_type);
                            if ($r->status == 2) { ?>
                                <span style="font-weight: bold;color:red;">暂停中</span>
                            <?php }  else {
                                if (!$link_url) {
                                    echo "<b>无</b>";
                                } else {
                                    ?>
                                    <a href="#"
                                       onclick="dialog({title:'推广链接，直接复制一下',content:$(this).attr('data-clipboard-text')}).showModal();"
                                       data-clipboard-text="
                                       <?php foreach ($link_url as $l) {
                                           $d_status = '';
                                           if ($l['domain_status'] != 0 && $l['domain_status']!=1) {
                                               $d_status = '('.$status_str[$l['domain_status']].')';
                                           }
                                           echo $l['domain'].$d_status.'<br/>';
                                       }?>
                                    ">点击查看</a>
                                <?php }
                            } ?>
                        </td>
                        <td><?php echo getPromotionFolder($r->id); ?></td>
                        <td><?php echo $r->csname_true; ?></td>
                        <td>
                            <?php
                            if($r->promotion_type == 0) echo '标准';
                            if($r->promotion_type == 1) echo '免域';
                            if($r->promotion_type == 2) echo '开户';
                            if($r->promotion_type == 3) echo '短域名';
                            ?>
                        </td>
                        <td><?php echo $status = emptyDir($path)?'未上传':'已上传'; ?></td>
                        <td>
                            <a href="promotionStatic.php?action=upload_file&id=<?php echo $r->id;?>" onclick="return dialog_frame(this,500,500,1)">上传文件</a>
                            <a href="promotionStatic.php?action=delete_file&id=<?php echo $r->id;?>" onclick="return confirm('确定删除该推广下文件？')">删除</a>
                        </td>
                    </tr>
                    <?php
                } ?>

                </tbody>


            </table>
            <div class="pagebar"><?php echo $promotions->pagearr->pagecode; ?></div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<script>
    //替换分页的链接
    (function ($) {
        var promotion_id = $('input[name="promotion_id"]').val();
        var channel_name = $('input[name="channel_name"]').val();
        var str = '';
        if (promotion_id != '') {
            str += '&promotion_id=' + promotion_id;
        }
        if (channel_name != '') {
            str += '&channel_name=' + channel_name;
        }
        $(".pagebar").find('a').each(function () {
            var old_href = $(this).attr('href');
            var link = old_href.split('&p=');
            var p = link[1].split('&');
            var page = p[0];
            var all_str = str+'&p='+page;
            $(this).attr('href', 'promotionStatic.php?action=promotion' + all_str);
        });
    })(jQuery);
</script>

