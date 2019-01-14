<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.package_name').on('keyup focus', function () {
            $('.searchsBox').show();
            var myInput = $(this);
            var key = myInput.val();
            packageName_id = myInput.attr('id');

            var postdata = {search_type: 'keys', search_txt: key};
            var html = '';
            $.getJSON('<?php echo $this->createUrl('packageManage/PackageGroupManage') ?>?jsoncallback=?', postdata, function (reponse) {
                for (var i = 0; i < reponse.data.list.length; i++) {
                    html += '<a href="javascript:void(0);" style="display:block;font-size:12px;padding:2px 5px;" onmouseDown="getTValues(this);" packageName_id="' + packageName_id + '" data-id="' + reponse.data.list[i].id + '" data-name="' + reponse.data.list[i].name + '">' + reponse.data.list[i].name + '</a>'
                }
                var s_height = myInput.height();
                var top = myInput.offset().top + s_height;
                var left = myInput.offset().left;
                var width = myInput.width();
                $('.searchsBox').remove();
                $('body').append('<div class="searchsBox" style="position:absolute;top:' + top + 'px;left:' + left + 'px;background:#fff;z-index:2;border:1px solid #ccc;width:' + width + 'px;">' + html + '</div>');
            });
            myInput.blur(function () {
                $('.searchsBox').hide();
            })
        });
    })

    function getTValues(ele) {
        var myobj = $(ele);
        packageName_id = myobj.attr('packageName_id')
        packageId_id = 'package_id_' + packageName_id.substr(packageName_id.length - 1);
        console.log(packageId_id)
        var id = myobj.attr('data-id');
        var name = myobj.attr('data-name')
        $('#' + packageName_id).val(name);
        $('#' + packageId_id).val(id);
    }

    //删除商品组
    function del(id,group_name) {
        if(confirm('确认删除商品组【'+ group_name+'】吗')){
            var url = "<?php echo $this->createUrl('packageManage/delPackageGroup') ?>";
            window.location.href = url + '?id=' + id;
        }else{
            return false;
        }
    }
</script>

<div class="main mhead">
    <div class="snav">直接下单管理 » 套餐管理</div>
    <div class="mt10 clearfix">
        <div class="l">
            <?php $this->check_u_menu(array('code' => '<a class="but2" onclick="return dialog_frame(this,500,500,1)" href="' . $this->createUrl('packageManage/addPackageGroup') . '" />'."添加商品组".'</a>', 'auth_tag' => 'packageManage_addPackageGroup')); ?>
            <?php $this->check_u_menu(array('code' => '<a class="but2" href="' . $this->createUrl('packageManage/packageManage?') . '" />'."下单商品管理".'</a>', 'auth_tag' => 'packageManage_packageManage')); ?>
        </div>
    </div>
    <div class="mt10 clearfix">
        <div class="1">
            <form action="<?php echo $this->createUrl('packageManage/index'); ?>">
                <select id="search_type" name="search_type">
                    <option value="group_name" <?php echo $this->get('search_type') == 'group_name' ? 'selected' : ''; ?>>商品组</option>
                    <option value="package_name" <?php echo $this->get('search_type') == 'package_name' ? 'selected' : ''; ?>>下单商品</option>
                </select>&nbsp;
                <input type="text" id="search_txt" style="width:120px;" name="search_txt" class="ipt"
                       value="<?php echo $this->get('search_txt')?$this->get('search_txt'):''; ?>">
                <input type="submit" class="but" value="搜索">
            </form>
        </div>
    </div>
</div>
<div class="main mbody">
    <form>
        <table class="tb fixTh">
            <thead>
            <tr>
                <th align='center'>商品组</th>
                <th align='center'>ID</th>
                <th align='center'>下单商品</th>
                <th align='center'>价钱</th>
                <th class="center">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php
                //第一行显示操作
                $last_group_name = '';
                //判断权限
                $edit = $this->check_u_menu(array('auth_tag'=>'packageManage_updatePackGroup'));
                $del = $this->check_u_menu(array('auth_tag'=>'packageManage_delPackageGroup'));
                foreach ($page['listdata']['list'] as $k => $r) {
            ?>
                <tr>
                    <td><?php if ($last_group_name != $r['group_name']) echo $r['group_name'] ?></td>
                    <td><?php echo $r['package_id'] ?></td>
                    <td><?php echo $r['package_name'] ?></td>
                    <td><?php echo $r['price'] ?></td>
                    <td>
                        <?php if ($last_group_name != $r['group_name']){ ?>
                         <a href="<?php echo $this->createUrl('packageManage/packageGroupManage?group_id=' . $r['id']) ?>">下单商品</a>&nbsp;&nbsp;&nbsp;
                        <?php if ($edit) { ?>
                            <a href="<?php echo $this->createUrl('packageManage/updatePackGroup?id=' . $r['id']);  ?>" onclick="return dialog_frame(this,400,300,1)">修改</a>
                        <?php }; ?>&nbsp;&nbsp;&nbsp;
                        <?php if ($del) { ?>
                            <a onclick="del(<?php echo $r['id']; ?>,<?php echo "'".$r['group_name']."'"; ?>)">删除</a>
                        <?php } ?><?php }; ?>&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <?php $last_group_name = $r['group_name'];} ?>
            </tbody>
        </table>
        <div class="pagebar"><?php echo $page['listdata']['pagearr']['pagecode']; ?></div>
        <div class="clear"></div>
    </form>
</div>
