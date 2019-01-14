<table class="tb3" style="width: 1200px;">
    <tr>
        <th>日期</th>
        <th>推广组</th>
        <th>推广人员</th>
        <th>客服部</th>
        <th>微信号个数</th>
        <th>计划进粉</th>
        <th>计划产值</th>
    </tr>
    <tr>
        <td style="text-align: center">-</td>
        <td style="text-align: center">-</td>
        <td style="text-align: center">-</td>
        <td style="text-align: center">总计</td>
        <td style="text-align: center"><?php echo $page['listdata']['all_weChat_num']?$page['listdata']['all_weChat_num']:'-'; ?></td>
        <td style="text-align: center"><?php echo $page['listdata']['all_fans_counts']?$page['listdata']['all_fans_counts']:'-'; ?></td>
        <td style="text-align: center"><?php echo $page['listdata']['all_outputs']?$page['listdata']['all_outputs']:'-'; ?></td>
    </tr>
    <?php foreach ($page['listdata']['list'] as $value){ ?>
        <tr style="text-align: center">
        <td style="text-align: center"> <?php echo date('Y-m-d',$value['date']); ?></td>
        <td style="text-align: center"> <?php echo $value['groupname']; ?></td>
        <td style="text-align: center"> <?php echo $value['csname_true']; ?></td>
        <td style="text-align: center"> <?php echo $value['cname']; ?></td>
        <td style="text-align: center"> <?php echo $value['weChat_num']; ?></td>
        <td style="text-align: center"> <?php echo $value['fans_counts']; ?></td>
        <td style="text-align: center"> <?php echo $value['outputs']; ?></td>
        </tr>
    <?php } ?>
</table>
<div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
<div class="clear"></div>