<?php require(dirname(__FILE__)."/../common/head.php"); ?>
    <div class="main mhead">
        <div class="snav">基础类别 » 商品列表	</div>
        <div  class="mt10">
            <select id="search_type">
                <option value="keys" <?php echo $this->get('search_type')=='keys'?'selected':''; ?>>商品名称</option>
                <option value="id" <?php echo $this->get('search_type')=='id'?'selected':''; ?>>ID</option>
            </select>&nbsp;
            <input type="text" id="search_txt" class="ipt"  value="<?php echo isset($_GET['search_txt'])?$_GET['search_txt']:''; ?>" >&nbsp;<input type="button" class="but" value="查询" onclick="window.location='<?php echo $this->createUrl('goods/index');?>?search_txt='+$('#search_txt').val()+'&search_type='+$('#search_type').val();" >
        </div>
        <div class="mt10 clearfix">
            <div class="l">
                <?php $this->check_u_menu(array('code'=>'<input type="button" class="but2" value="添加商品" onclick="location=\''.$this->createUrl('goods/add').'\'" />','auth_tag'=>'goods_add')); ?>
            </div>
            <div class="r">

            </div>
        </div>
    </div>
    <div class="main mbody">
        <form>
            <table class="tb fixTh">
                <thead>
                <tr>
                    <th align='center'>ID</th>
                    <th>商品</th>
                    <th>形象</th>
                    <th>商品类别</th>
                    <th>性别特征</th>
                    <th>描述</th>
                    <th>修改时间</th>
                    <th  class="alignleft">操作</th>
                </tr>
                </thead>

                <?php
                foreach($page['listdata']['list'] as $r){
                    ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td><?php echo $r['goods_name']; ?></td>
                        <td style="max-width: 400px"><?php echo $r['characters']; ?></td>
                        <td><?php echo $r['cat_name']; ?></td>
                        <td><?php echo $r['service_group']; ?></td>
                        <td><?php echo $r['remark']; ?></td>
                        <td><?php echo date('Y-m-d',$r['update_time']); ?></td>
                        <td class="alignleft">
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('goods/edit',array('id'=>$r['id'])).'">修改</a>','auth_tag'=>'goods_edit')); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php $this->check_u_menu(array('code'=>'<a href="'.$this->createUrl('goods/delete',array('id'=>$r['id'])).'"  onclick="return confirm(\'确定删除吗\')">删除</a>','auth_tag'=>'goods_delete')); ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </table>
            <div class="clear"></div>
        </form>
    </div>


<?php require(dirname(__FILE__)."/../common/foot.php"); ?>