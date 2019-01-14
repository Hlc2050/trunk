<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3">
            <tr>
                <th align="left">二维码查看</th>
            </tr>
        </table>
    </div>
    <div class="mt10" id="container">
        <table>
            <tbody style="align-items: center;">
            <tr>
                <td style="font-size: large">
                    <?php echo $wechat_id;?>
                </td>
            </tr>
            <tr>
                <td style="width: 100%">
                    &nbsp;&nbsp;&nbsp;
                    <img src="<?php echo $imgURL;?>" style="width: 100%; " />
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

