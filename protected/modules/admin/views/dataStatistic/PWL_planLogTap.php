<!-- 提交记录 -->
<form action="<?php echo $this->createUrl('planWeek/index'); ?>">
    <input type="hidden" name="tap_id" value="4"/>
    <div class="mt10">
        &nbsp;标题：<input id="title" name="title" class="ipt" style="width: 200px" value="<?php echo $page['info']['title']?>" type="text">
        <input type="hidden" name="tab_id" value="<?php echo $page['tab_id'] ;?>">
        <input type="submit" class="but" value="查询"> &nbsp;&nbsp;
    </div>
</form>