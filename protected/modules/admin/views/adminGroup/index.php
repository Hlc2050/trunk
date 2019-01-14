<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">系统 »  部门管理	</div>

        <div class="mt10 clearfix">
            <div class="l">
                <input type="button" class="but2" value="添加管理组" onclick="location='<?php echo $this->createUrl('adminGroup/update');?>'" />
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form action="?m=save_order" name="form_order" method="post">
            <table class="tb">
                <tr>
                    <th align='center'>	ID</th>
                    <th  class="alignleft">部门名称</th>
                    <th>部门负责人</th>
                    <th width=200>操作</th>
                </tr>

                <?php echo $page['categorys']; ?>


            </table>
            <div class="clear"></div>
        </form>
    </div>
<?php require(dirname(__FILE__)."/../common/foot.php"); ?>
<?php 
