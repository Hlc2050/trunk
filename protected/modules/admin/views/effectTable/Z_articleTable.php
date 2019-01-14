<!-- 图文效果表 -->
<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>图文编码</th>
        <th>图文类型</th>
        <th>渠道转化</th>
        <th>图文转化</th>
        <th>UV转化</th>
    </tr>
    <tr id="totalInfo"></tr>
    </thead>
    <tbody>
    <?php
    $total_uv = $total_fans = $total_estimate_count = $total_fans_count = 0;
    foreach ($page['list'] as $r) {

        $channel_transform = $r['fans_count'] ? round($r['estimate_count'] * 100 / $r['fans_count'], 1) : 0;//渠道转化  预估发货量/粉丝量
        $uv_transform = $r['fans'] ? round($r['uvs'] * 100 / $r['fans'], 1) : 0;//uv转化 阅读量/粉丝量
        $article_transform = $r['uvs'] ? round($r['fans_count'] * 100 / $r['uvs'], 1) : 0;//图文转化 粉丝量/阅读数
        $total_estimate_count += $r['estimate_count'];
        $total_fans_count += $r['fans_count'];
        $total_uv += $r['uvs'];
        $total_fans += $r['fans'];
        ?>
        <tr>
            <td><?php echo $r['article_code']; ?></td>
            <td><?php
                if ($r['article_code'] == '') {
                    echo '';
                } else if($r['article_type'] == '0') {
                    echo "标准图文" ;
                } else if($r['article_type'] == '1') {
                    echo "语音问卷" ;
                } else if($r['article_type'] == '2') {
                    echo "论坛问答" ;
                } ?></td>
            <td><?php echo $channel_transform . "%"; ?></td>
            <td><?php echo $article_transform . "%"; ?></td>
            <td><?php echo $uv_transform . "%"; ?></td>
        </tr>
    <?php }
    $total_channel_transform = round($total_estimate_count * 100 / $total_fans_count, 1);
    $total_uv_transform = round($total_uv * 100 / $total_fans, 1);
    $total_article_transform = round($total_fans_count * 100 / $total_uv, 1);
    ?>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {
        //将合计填数据加上去
        var html = "<th>合计</th><th>-</th> " +
            "<th><?php echo $total_channel_transform . '%';?></th><th><?php echo $total_article_transform . '%';?></th> <th><?php echo $total_uv_transform . '%';?></th>"
        $("#totalInfo").html(html);
    })
</script>