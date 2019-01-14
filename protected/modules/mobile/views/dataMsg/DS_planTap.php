<select name="group_id" class="am-input-sm" style="width: 60%;padding: 5px;float: right" onchange="change_group(this)">
    <option value="">全部推广组</option>
    <?php foreach ($page['groups'] as $key=>$group) {?>
        <option value="<?php echo $key;?>" <?php if ($page['info']['group_id'] == $key ) echo 'selected';?>><?php echo $group;?></option>
    <?php }?>
</select>