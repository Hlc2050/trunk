<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div style="    font-size: large; font-weight: bold;color: dimgrey;">添加总统计</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $page['info']['id'] ? $this->createUrl('cnzzCodeManage/edit') : $this->createUrl('cnzzCodeManage/add'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <table class="tb3">
            <tr>
                <td style="width: 120px;color: dimgrey;">&nbsp;总统计名称：</td>
                <td>
                    <input type="text" class="ipt" id="tag" name="name" value="<?php echo $page['info']['name'] ? $page['info']['name'] : ""; ?>"/>
                </td>
            </tr>
            <tr>
                <td style="width: 120px;color: dimgrey;">&nbsp;域名数量上限：</td>
                <td>500</td>
            </tr>
            <tr>
                <td style="width: 120px;color: dimgrey;">&nbsp;总统计代码：</td>
                <td>
                        <textarea style="font-size: small;height: 50px;" class="ipt" id="total_cnzz"
                                  name="total_cnzz"><?php echo $page['info']['total_cnzz']; ?> </textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                </td>
            </tr>
        </table>
    </form>
</div>
