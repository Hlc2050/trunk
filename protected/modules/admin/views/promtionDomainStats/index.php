<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">域名管理 » 推广人员域名统计</div>
    <div class="mt10">
        <form action="<?php echo $this->createUrl('PromtionDomainStats/index'); ?> " method="post">
            状态：&nbsp;&nbsp;
            <select name="status">
                <option value=" ">请选择</option>
                <option value="1" <?php if($this->get('status') == 1) echo 'selected'; ?>>推广</option>
                <option value="0" <?php if($this->get('status') == 0 && $this->get('status') != '') echo 'selected'; ?>>备用</option>
            </select>
            &nbsp;&nbsp;
            应用类型：&nbsp;&nbsp;
            <select name="type">
                <option value=" ">请选择</option>
                <option value="1" <?php if($this->get('type') == 1) echo 'selected'; ?>>静态应用</option>
                <option value="0" <?php if($this->get('type') == 0 && $this->get('type') != '') echo 'selected'; ?>>普通应用</option>
            </select>
            &nbsp;&nbsp;
            是否支持https:&nbsp;&nbsp;
            <select name="is_https">
                <option value=" ">请选择</option>
                <option value="1" <?php if($this->get('is_https') == 1) echo 'selected'; ?>>是</option>
                <option value="0" <?php if($this->get('is_https') == 0 && $this->get('is_https') != '') echo 'selected'; ?>>否</option>
            </select>
            &nbsp;&nbsp;
            <input type="submit" value="搜索" class="but">
        </form>
    </div>
    <div class="mt10">
        <table class="tb">
            <tr>
                <th>推广人员</th>
                <th>短域名</th>
                <th>推广域名</th>
                <th>跳转域名</th>
            </tr>
            <?php foreach ($data['listdata']['list'] as $key => $value) { ?>
                <tr>
                    <td><?php echo $value['name'] ?></td>
                    <td><?php echo $value['short_domain_num'] ?></td>
                    <td><?php echo $value['promotion_domain_num'] ?></td>
                    <td><?php echo $value['jump_domain_num'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="pagebar"><?php echo $data['pageInfo']['pagecode']; ?></div>
</div>
