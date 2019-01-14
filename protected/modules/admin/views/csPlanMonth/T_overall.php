<form action="<?php echo $this->createUrl('csPlanMonth/index'); ?>" method="post">
    <input type="hidden" name="group_id" value="0"/>
    日期：
    <input type="text" size="20" class="ipt" style="width:120px;" name="start_date"
           id="start_date" value="<?php if($page['start_date']){echo date('Y-m',$page['start_date']);}  ; ?>"
           onclick="WdatePicker({dateFmt:'yyyy-MM'})"/>
    <input type="submit" class="but" value="搜索">&nbsp;
</form>