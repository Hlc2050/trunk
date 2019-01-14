<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<script type="text/javascript">
    function cheFrom() {
        var package_string = $("textarea[name='package_list']").val() + '\n';
        var package_list = package_string.split('\n');
        var package_count = 0;
        var arr = []
        for (var i = 0; i < package_list.length; i++) {
            if (!isNull(package_list[i])) {
                package_count++;
                arr.push(package_list[i]);
            }
        }
        if (package_count <= 0) {
            alert('请先输入下单商品!');
            return false;
        }

        function isNull(str) {
            if (str == "") return true;
            var regu = "^[ ]+$";
            var re = new RegExp(regu);
            return re.test(str);
        }
    }
</script>

<div class="main mhead">
    <div class="snav">
        <div class="snav">直接下单管理 » 下单商品管理 » 添加下单商品</div>
        <div class="mt10 clearfix">添加下单商品(一行一个)</div>
    </div>
</div>

<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('packageManage/addPackage') ?>" onsubmit="return cheFrom()">
        <div>
            <textarea rows="7" style="height: 430px;width: 280px" name="package_list"></textarea>
        </div>
        <div class="mt10"></div>
        <input type="submit" class="but2" value="保存">
        <input type="button" class="but2" value="返回" onclick="window.location='<?php echo $this->createUrl('packageManage/packageManage'); ?>'"/>
    </form>

</div>
