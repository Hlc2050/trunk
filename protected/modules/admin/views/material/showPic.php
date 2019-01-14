<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">
        <table class="tb3">
            <tr>
                <th align="left">素材图片查看</th>
            </tr>
        </table>
    </div>
    <div class="mt10" id="container">
        <table>
            <tbody style="align-items: center;">
            <tr>
                <th align="left">
                    <?php echo $page['name'];?>
                </th>
            </tr>
            <tr>
                <td style="width: 100%">
                    &nbsp;&nbsp;&nbsp;
                    <img src="<?php echo $page['imgURL'];?>" style="width: 300px;height: 300px;"  />
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

