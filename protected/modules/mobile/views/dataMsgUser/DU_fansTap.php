<select name="order" class="am-input-sm" style="width: 60%;padding: 5px;float: right" onchange="change_paixu(this,0)">
    <option value="1" <?php if ($page['info']['order_type'] ==1) echo 'selected' ;?>>涨幅-从低到高</option>
    <option value="2" <?php if ($page['info']['order_type'] == 2) echo 'selected' ;?>>涨幅-从高到低</option>
</select>
