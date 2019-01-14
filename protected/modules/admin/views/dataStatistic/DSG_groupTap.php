<!-- 图文效果表搜索栏 -->
<form action="<?php echo $this->createUrl('/admin/dataStatistic/index'); ?>">
    <input type="hidden" name="tab_id" value="<?php echo $page['tab_id'] ;?>">
    <div class="mt10">
        客服部：
        <?php
        helper::getServiceSelect('service_id');
        ?>
        &nbsp; &nbsp; &nbsp;推广组:
        <select name="group_id">
            <option value="">全部</option>
            <?php foreach ($page['service_group'] as $key=>$value) {?>
                <option value="<?php echo $key;?>" <?php if ($this->get('group_id') == $key) echo 'selected' ?>><?php echo $value;?></option>
            <?php }?>
        </select>
        &nbsp;            日期：
        <input type="text" size="20" class="ipt" style="width:120px;" name="start_date" id="start_addfan_date"
               value="<?php echo $this->get('start_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>-
        <input type="text" size="20" class="ipt" style="width:120px;" name="end_date" id="end_addfan_date"
               value="<?php  echo $this->get('end_date'); ?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>&nbsp;

        <input type="submit" class="but" value="查询"> &nbsp;&nbsp;
    </div>
</form>

