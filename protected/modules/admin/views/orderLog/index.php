<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<div class="main mhead">
    <div class="snav">下单日志
    </div>
</div>
<div class="main mbody">

    <div class="list08 clearfix">
            <div class="mt10 clearfix">
                <span style="color: red">*默认只可下载20天内的下单日志，其他日志联系运维获取</span>
            </div>
            <div class="mt10">
                <form>
                    <table class="tb fixTh" style="width: 400px">
                        <thead>
                        <tr>
                            <th width="300">文件名字</th>
                            <th width="80">操作</th>
                        </tr>
                        </thead>

                        <?php
                        foreach ($page['list'] as $r) { ?>
                            <tr>
                                <td><?php echo $r; ?></td>
                                <td>
                                    <a href="<?php echo $this->createUrl('orderLog/download?name='.$r)?>"> 下载</a>
                                </td>
                            </tr>
                            <?php
                        } ?>


                    </table>
                    <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
                    <div class="clear"></div>
                </form>
            </div>

    </div>


</div>

