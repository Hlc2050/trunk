<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<?php
$picGroupList = $this->toArr(MaterialPicGroup::model()->findAll());
?>
<div class="main mhead">
    <div class="snav">
        <table  class="tb3"><tr><td style="font-size: large;font-weight: bold;" align="left">另存为</td></tr></table></div>
</div>
<div class="main mbody">

    <form method="post" action="<?php echo $this->createUrl('material/saveAs'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['id']; ?>"/>
        <table class="tb3">
            <tr>
                <td style="width: 60px">标题：</td>
                <td>
                <input type="text" name="article_title" class="ipt">
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" id="subtn" value="确定"/>&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" class="but" onclick="setTimeout(function(){artDialog.close();}, 200)" value="取消"/>
                </td>

            </tr>
        </table>
    </form>
</div>
