<!--统计图搜索栏-->
<form action="<?php echo $this->createUrl('orderGoodsEffect/index'); ?>" onsubmit="return check_select()"
      id="searchForm">
    <div class="mt10">
        <input type="hidden" name="group_id" value="<?php echo $this->get('group_id'); ?>"/>
        日期：
        <input type="text" size="20" class="ipt" style="width:120px;" name="start_online_date"
               id="start_online_date" value="<?php echo $allData['start_online_date']; ?>"
               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
        <input type="text" size="20" class="ipt" style="width:120px;" name="end_online_date"
               id="end_online_date" value="<?php echo $allData['end_online_date']; ?>"
               onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
        <a href="#"
           onclick="$('#start_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date('Y'))); ?>');$('#end_online_date').val('<?php echo date("Y-m-d", mktime(0, 0, 0, date('m'), date('t'), date('Y'))); ?>')">本月</a>
        &nbsp;
        商品组：
        <?php
        $goodsGrouplist = PackageGroupManage::model()->findAll();
        echo CHtml::dropDownList('package_group_id', $this->get('package_group_id'), CHtml::listData($goodsGrouplist, 'id', 'group_name'),
            array(
                'empty' => '全部',
                'ajax' => array(
                    'type' => 'POST',
                    'url' => $this->createUrl('orderGoodsEffect/getGoodsCheckbox'),
                    'update' => '#goods_list',
                    'data' => 'js:$("#searchForm").serializeJson()'
                )
            )
        );
        ?>&nbsp;
    </div>
    <div class="mt10" id="order_goods">
        <label><strong>下单商品：</strong></label>
        <div style="display: inline-block" id="goods_list">
            <?php
            if ($this->get('package_group_id') == '') {
                $packages = PackageManage::model()->findAll();
            } else {
                $packages = PackageRelation::model()->getPackageList($this->get('package_group_id'));
            }
            foreach ($packages as $k => $val) {
                if (in_array($val['id'], $allData['ids'])) continue;
                echo CHtml::tag('input', array('type' => 'checkbox', 'name' => 'goods_check', 'value' => $val['id'], 'style' => 'margin-left:10px;', 'onclick' => 'goods_checked(this)'),
                    '<span id="span_' . $val['id'] . '">' . CHtml::encode($val['name']) . '</span>', true);
            }
            ?>
        </div>
    </div>
    <div class="mt10">
        &nbsp;<strong>已选下单商品：</strong>
        <div id="serch_goods" style="display: inline-block">
            <?php
            $select_package_id = '';
            foreach ($allData['list'] as $value) {
                $select_package_id .= '"' . $value['package_id'] . '"' . ',';
                ?>
                <label id="goods_<?php echo $value['package_id']; ?>">
                    <input class="s_goods" name="package_id[]" value="<?php echo $value['package_id']; ?>"
                           type="checkbox" style="margin-left: 10px;" checked="true"
                           onclick="change_checked(this)"><span><?php echo $value['package_name']; ?></span>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="mt10">
        <input type="submit" class="but" value="搜索">
    </div>
</form>
<script>
    var select_package_id = [<?php echo rtrim($select_package_id, ',');?>];

    function goods_checked(dom) {
        var goods_name = $(dom).next().text();
        var package_id = $(dom).val();
        $(dom).remove();
        var is_check = $(dom).is(':checked');
        $("#span_" + package_id).remove();

        check_serch_goods($(dom).val(), goods_name, is_check);
    }

    function check_serch_goods(goods_id, goods_name, is_checked) {
        if (is_checked === true && $.inArray(goods_id, select_package_id) == -1) {
            select_package_id.push(goods_id);
            var dom_str = "<label id = goods_" + goods_id + "><input class='s_goods' name='package_id[]' value='" + goods_id + "' type='checkbox' style='margin-left: 10px;' checked='" + is_checked + "' onclick='change_checked(this)'><span>" + goods_name + "</span></label>";
            $("#serch_goods").append(dom_str);
        }
    }

    function change_checked(dom) {
        var package_name = $(dom).next().text();
        console.log(package_name)
        var package_id = $(dom).val();
        $(dom).remove();
        select_package_id.pop(package_id);
        $("#goods_" + package_id).remove();
        var dom_str = "<input type='checkbox' name='goods_check' value='"+package_id+"' style='margin-left:10px;' onclick='goods_checked(this)'> <span id='span_"+package_id+"'>"+package_name+"</span>";
        $("#goods_list").append(dom_str);
    }

    function check_select() {
        var has_select_package = false;
        $("input[name='package_id[]']").each(function () {
            var is_checked = $(this).is(':checked');
            if (is_checked === true) has_select_package = true;
        });
        if (has_select_package === false) alert('请先选择下单商品！');
        return has_select_package;
    }
</script>