<table id="exportTable" class="tb fixTh" style="width: 100%">
    <thead>
    <tr>
        <th>图文编码</th>
        <th>图文类型</th>
<!--        <th>投入金额</th>-->
<!--        <th>发货金额</th>-->
<!--        <th>ROI</th>-->
<!--        <th>进线成本</th>-->
<!--        <th>均线产出</th>-->
<!--        <th>发货量</th>-->
<!--        <th>进线量</th>-->
<!--        <th>IP</th>-->
<!--        <th>UV</th>-->
        <th>渠道转化</th>
        <th>图文转化</th>
    </tr>
    <tr id="totalInfo">

    </tr>
    </thead>
    <tbody>
    <?php
    $total_money = $total_in_count = $total_out_count = $total_uv = $total_ip = $total_deliver_money = 0;
    foreach ($allData['list'] as $d){
        $chnanel_transfrom = $d['in_count'] ? round($d['sout_count']*100 / $d['in_count'],2) : 0;
        $article_transfrom = $d['uv'] ? round($d['in_count']*100 / $d['uv'],2) : 0;
        $total_in_count += $d['in_count'];
        $total_out_count += $d['sout_count'];
        $total_uv += $d['uv'];
        ?>
        <tr>
            <td><?php echo $d['article_code'];?></td>
            <td>
                <?php
                if ($d['article_code'] == '') {
                    echo '';
                } else if($d['article_type'] == '0') {
                    echo "标准图文" ;
                } else if($d['article_type'] == '1') {
                    echo "语音问卷" ;
                } else if($d['article_type'] == '2') {
                    echo "论坛问答" ;
                } ?>
            </td>
            <td><?php echo $chnanel_transfrom .'%';?></td>
            <td><?php echo $article_transfrom .'%';?></td>
        </tr>
    <?php } ?>
    <?php
    $total_chnanel_transfrom = $total_in_count ? round($total_out_count*100 / $total_in_count,2) : 0;
    $total_article_transfrom = $total_uv ? round($total_in_count*100 / $total_uv,2) : 0;
    ?>
    </tbody>
</table>
<script type="text/javascript">
    $(function () {
        //将合计填数据加上去
        var html = "<th>-</th> <th>合计</th> " +
            "<th><?php echo $total_chnanel_transfrom . '%';?></th>"+
            "<th><?php echo $total_article_transfrom . '%';?></th>";
        $("#totalInfo").html(html);
    })
</script>