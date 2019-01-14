<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
//页面默认值
if (!isset($page['info'])) {
    $page['info']['id'] = '';
    $page['info']['sname'] = '';
    $page['info']['target_a'] = '';
    $page['info']['target_b'] = '';
    $page['info']['target_c'] = '';
    $page['info']['target_time'] = '';
}
?>
<style>
    .fix-width {
        display: inline-block;
    }
</style>
<div class="main mhead">
    <div class="snav">推广管理 » 计划表管理 » <?php echo $page['info']['id'] ? '修改计划表' : '添加计划表' ?></div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'schedule/edit' : 'schedule/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft"><?php echo $page['info']['id'] ? '修改计划表' : '添加计划表' ?></th>
            </tr>
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="120">ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="120">日期：</td>
                <td class="alignleft">
                    <input type="text" size="20" class="ipt" name="target_time" id="target_time"
                           value="<?php echo $page['info']['target_time']?date('Y-m',$page['info']['target_time']):''; ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
                </td>
            </tr>
            <tr>
                <td  width="120">对象：</td>
                <td  class="alignleft">
                    <select name="schedule_id">
                        <option value="" selected>
                            选择对象
                        </option>
                        <?php
                        $scheduleList=Linkage::model()->getScheduleList();
                        $goodsList = Linkage::model() -> getGoodsCategoryList();
                        $scheduleList = $scheduleList + $goodsList;
                        foreach ($scheduleList as $key => $val) {
                            ?>
                            <option
                                value="<?php echo $key; ?>" <?php echo $key==$page['info']['schedule_id'] ? 'selected' : ''; ?>>
                                <?php echo $val;?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="120">目标类型：</td>
                <td class="alignleft">
                    <input type="radio" class="ipt typeBtu" id="target_type" name="target_type"
                           value="0" checked/>整型&nbsp;&nbsp;
                    <input type="radio" class="ipt typeBtu" id="target_type" name="target_type"
                           value="1" <?php if($page['info']['target_type'] == 1) echo 'checked';?>/>百分比
                </td>
            </tr>
            <tr>
                <td width="120">目标A：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="target_a" name="target_a"
                           value="<?php echo $page['info']['target_a']; ?>"/>
                    <span class="showPercent" hidden> % <span>
                </td>
            </tr>
            <tr>
                <td width="120">目标B：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="target_b" name="target_b"
                           value="<?php echo $page['info']['target_b']; ?>"/>
                     <span class="showPercent" hidden> % <span>
                </td>

            </tr>
            <tr>
                <td width="120">目标C：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="target_c" name="target_c"
                           value="<?php echo $page['info']['target_c']; ?>"/>
                     <span class="showPercent" hidden> % <span>
                </td>
            </tr>

            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/> <input type="button"
                                                                                                      class="but"
                                                                                                      value="返回"
                                                                                                      onclick="window.location='<?php echo $this->createUrl('schedule/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>
        </table>
        <script type="text/javascript">
            $(function () {
                $(':radio').click(function () {
                    var type = $(this).val();
                    console.log(type);
                    if(type == 0){
                        $('.showPercent').hide();
                    }else
                        $('.showPercent').show();
                })
            })
            window.onload = function () {
                var type = $(':radio:checked').val();
                console.log(type);
                if(type == 0){
                    $('.showPercent').hide();
                }else
                    $('.showPercent').show();
            };
        </script>
    </form>
</div>


