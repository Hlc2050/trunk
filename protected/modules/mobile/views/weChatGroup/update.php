<?php require(dirname(__FILE__) . "/../common/menu.php"); ?>
<div class="admin-content">
    <a id="top" name="top"></a>
    <div style="margin-top: 50px;"></div>
    <div class="admin-content-body">
        <div class="am-u-sm-12 am-u-md-8 am-u-md-pull-4">
            <h3>修改微信号小组</h3>
            <form class="am-form" method="post" action="<?php echo $this->createUrl('weChatGroup/edit'); ?>">
                <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>

                <div class="am-g am-margin-top">
                    <div class="am-u-sm-5 am-u-md-2 am-text-right">
                        id
                    </div>
                    <div class="am-u-sm-7 am-u-md-4">
                        <?php echo $page['info']['id']; ?>
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-5 am-u-md-2 am-text-right">
                        微信小组名称
                    </div>
                    <div class="am-u-sm-7 am-u-md-4">
                        <input type="text" class="am-input-sm" id="wechat_group_name" name="wechat_group_name"
                               value="<?php echo $page['info']['wechat_group_name']; ?>">
                    </div>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-5 am-u-md-2 am-text-right">
                        计费方式
                    </div>
                    <div class="am-u-sm-7 am-u-md-4">
                        <input type="hidden" id="charging_type" name="charging_type"
                               value="<?php echo $page['info']['charging_type']; ?>"/>
                        <?php
                        echo vars::get_field_str('charging_type', $page['info']['charging_type']);
                        ?>
                    </div>
                </div>
                <hr/>
                <h6>
                    <small>该小组已添加的微信号</small>
                </h6>
                <div class="am-form-group" id="addedWeChatIds">
                    &nbsp;
                    <?php if ($page['info']['id'] && $page['info']['wechat_ids'] != ''):
                        foreach ($page['info']['wechat_id'] as $key => $val) {
                            $weChatId = WeChat::model()->findByPk($val)->wechat_id;
                            ?>
                            <label class="am-checkbox-inline" id="<?php echo $weChatId; ?>">
                                <input name='weChat_list[]' checked='checked'
                                       onclick='weChatIdsSelect(this)' type="checkbox" value="<?php echo $val; ?>"
                                       attr-val='<?php echo $weChatId; ?>' data-am-ucheck>
                                <span> <?php echo $weChatId; ?></span>
                            </label>
                        <?php }endif ?>
                </div>
                <div class="am-form-group">
                    <h6>
                        <small>添加微信号 &nbsp;<span class=" am-panel-title am-icon-search"
                                                 data-am-collapse="{parent: '#accordion', target: '#do-not-say-1'}"></span>
                        </small>
                    </h6>
                    <div id="do-not-say-1" class="am-panel-collapse am-collapse">
                        <div class="am-panel-bd">
                            <div class="am-form-group">
                                <table class="am-table">
                                    <?php
                                    $promotionStaffList = PromotionStaff::model()->getPromotionStaffList(1);
                                    $customerServiceList = $this->toArr(CustomerServiceManage::model()->findAll());
                                    ?>
                                    <tbody>
                                    <tr>
                                        <td class="">
                                            <small>商品</small>
                                        </td>
                                        <td><input type="text" name="gid" id="gid"
                                                   class="am-input-sm am-form-field am-radius" placeholder="商品"
                                                   value="<?php echo $this->get('gid') ?>"/>
                                        </td>
                                        <td rowspan="5" class="am-text-middle">
                                            <input type="button" class="am-btn am-radius am-btn-primary" value="查询"
                                                   onclick="weChatIdsSearch(this)">

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <small>形象</small>
                                        </td>
                                        <td><input type="text" name="catid" id="catid"
                                                   class="am-input-sm am-form-field am-radius" placeholder="形象"
                                                   value="<?php echo $this->get('catid') ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <small>客服部</small>
                                        </td>
                                        <td>
                                            <select name="csid" id="csid" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($customerServiceList as $r) { ?>
                                                    <option <?php if ($r['id'] == $this->get('csid')) echo "selected" ?>
                                                            value="<?php echo $r['id'] ?>"><?php echo $r['cname'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <small>推广人员</small>
                                        </td>
                                        <td>
                                            <select name="user_id" id="user_id" class="am-input-sm">
                                                <option value="">全部</option>
                                                <?php foreach ($promotionStaffList as $r) { ?>
                                                    <option <?php if ($r['user_id'] == $this->get('user_id')) echo "selected" ?>
                                                            value="<?php echo $r['user_id'] ?>"><?php echo $r['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="">
                                            <small>微信号id</small>
                                        </td>
                                        <td><input type="text" name="wechat_id" id="wechat_id"
                                                   class="am-input-sm am-form-field am-radius" placeholder="微信号id"
                                                   value="<?php echo $this->get('wechat_id') ?>"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="am-form-group" id="searchedWeChatIds">
                    &nbsp;
                    <?php if ($page['info']['id'] && $page['info']['wechat_ids'] != ''):
                        foreach ($page['weChatList'] as $key => $val) {
                            ?>
                            <label class="am-checkbox-inline" id='<?php echo $val['wechat_id']; ?>'>
                                <input onclick="weChatIdsSelect(this)" type="checkbox"
                                       value="<?php echo $val['id']; ?>" attr-val="<?php echo $val['wechat_id']; ?>"
                                       data-am-ucheck/>
                                <span><?php echo $val['wechat_id']; ?></span>
                            </label>
                        <?php }endif ?>
                </div>
                <div class="am-g am-margin-top">
                    <div class="am-u-sm-6 am-u-md-2 am-text-right">
                        <input type="button" class="am-btn am-radius am-btn-primary" value="返回"
                               onclick="javascript:window.history.go(-1)"/>
                    </div>
                    <div class="am-u-sm-6 am-u-md-4">
                        <input type="submit" class="am-btn am-radius am-btn-primary" value="保存"/>
                    </div>
                </div>
                <br>
            </form>
        </div>
        <hr data-am-widget="divider" style="" class="am-divider am-divider-default"/>

    </div>

</div>
<script type="text/javascript">

    /**
     * 微信号AJAX查询
     * @param obj
     */
    function weChatIdsSearch(obj) {
        var idArr = [];
        //已选的微信号
        $("input[name='weChat_list[]']").each(function (i) {
            idArr[i] = $(this).val();
        });
        var idStr = idArr.join(',');
        var params = {
            wid: <?php echo $page['info']['id'] ? $page['info']['id'] : 0;?>,
            gid: $('#gid').val(),
            catid: $('#catid').val(),
            csid: $('#csid').val(),
            wechat_id: $('#wechat_id').val(),
            user_id: $('#user_id').val(),
            charging_type: $('#charging_type').val(),
            bid: <?php echo $page['info']['business_type'];?>,
            ids: idStr
        };
        $.get('<?php echo $this->createUrl('weChatGroup/searchHandler'); ?>', params, function (data) {
            console.log(data)

            try {
                var divshow = $("#searchedWeChatIds");
                divshow.text("");
                divshow.append(data);
            } catch (e) {
                alert(e.message)
            }
        })
    }

    /**
     * 勾选微信号处理
     * @param obj
     */
    function weChatIdsSelect(obj) {
        console.log($(obj).is(':checked'));
        if ($(obj).is(':checked')) {
            var addedObj = $("#addedWeChatIds");
            var content = "<label class='am-checkbox-inline' id='" + $(obj).attr('attr-val') + "'><input name='weChat_list[]'  onclick='weChatIdsSelect(this)'  checked='checked'  type='checkbox' value='" + $(obj).val() + "' attr-val='" + $(obj).attr('attr-val') + "'  data-am-ucheck class='am-ucheck-checkbox'><span class='am-ucheck-icons'><i class='am-icon-unchecked'></i><i class='am-icon-checked'></i></span><span>" + $(obj).attr('attr-val') + "</span></label>";

            $("#" + $(obj).attr('attr-val') + "").remove();
            addedObj.append(content);
        } else {
            var weChatObj = $("#searchedWeChatIds");
            var content = "<label class='am-checkbox-inline' id='" + $(obj).attr('attr-val') + "'><input  onclick='weChatIdsSelect(this)'    type='checkbox' value='" + $(obj).val() + "' attr-val='" + $(obj).attr('attr-val') + "' data-am-ucheck  class='am-ucheck-checkbox'><span class='am-ucheck-icons'><i class='am-icon-unchecked'></i><i class='am-icon-checked'></i></span><span>" + $(obj).attr('attr-val') + "</span></label>";
            weChatObj.append(content);
            $("#" + $(obj).attr('attr-val') + "").remove();
        }
    }

</script>
</body>
</html>