<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">添加文章分块</div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $page['info']['id']?$this->createUrl('articleBlock/edit'):$this->createUrl('articleBlock/add'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr>
                <td width="120">
                    文章名称
                </td>
                <td>
                    <input type="text" class="ipt" id="block_name" name="block_name"
                           value="<?php echo $page['info']['block_name']; ?>"/>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    空白位置显示内容
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt" id="blank_content" name="blank_content"><?php echo $page['info']['blank_content']; ?></textarea>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    分块一
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt block_content"  name="block_content[]"><?php echo $page['info']['block_one']; ?></textarea>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    分块二
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt blank_content"  name="block_content[]"><?php echo $page['info']['block_two']; ?></textarea>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    分块三
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt blank_content"  name="block_content[]"><?php echo $page['info']['block_three']; ?></textarea>
                    <span style="color: red;">*必填</span>
                </td>
            </tr>
            <tr>
                <td width="120">
                    分块四
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt blank_content"  name="block_content[]"><?php echo $page['info']['block_four']; ?></textarea>
                </td>
            </tr>
            <tr>
                <td width="120">
                    分块五
                </td>
                <td>
                    <textarea style="font-size: small;width: 400px;height: 100px;" class="ipt blank_content"  name="block_content[]"><?php echo $page['info']['block_five']; ?></textarea>
                </td>
            </tr>

            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $this->get('url'); ?>'"/>
                </td>
            </tr>
        </table>

    </form>
</div>