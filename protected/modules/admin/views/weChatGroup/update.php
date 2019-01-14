<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">渠道管理 » 微信号小组管理 »<?php echo $page['info']['id'] ? '修改微信号小组' : '添加微信号小组' ?></div>
</div>
<div class="main mbody">

    <?php
    $bid = $this->get('bid')||$this->get('bid')==='0'?$this->get('bid'):1;
    $businessTypes = Dtable::toArr(BusinessTypes::model()->findAll());
    if (!$page['info']['id']) : ?>
        <div class="tab_box">
            <!--业务类型栏-->
            <?php
            foreach ($businessTypes as $k => $r) { ?>
                <a href="?bid=<?php echo $r['bid']; ?>" <?php if ($bid == $r['bid']) echo 'class="current"'; ?>><?php echo $r['bname']; ?></a>
            <?php }
            ?>
            <a href="<?php $this->createUrl('weChatGroup/add'); ?>?bid=0" <?php if ($bid == 0) echo 'class="current"'; ?>>特殊手赚</a>

        </div>
    <?php endif; ?>
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['id'] ? 'weChatGroup/edit' : 'weChatGroup/add', array('bid' => $bid)); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>" />

        <table class="tb3">
            <?php if ($bid == 1) :?>
                <tr>
                    <th colspan="2"
                        class="alignleft"><?php echo $page['info']['id'] ? '修改订阅号微信号小组' : '添加订阅号微信号小组' ?></th>
                </tr>

            <?php elseif ($bid == 0) : //特殊非订阅号?>
                <tr>
                    <th colspan="2"
                        class="alignleft"><?php echo $page['info']['id'] ? '修改特殊手赚微信号小组' : '添加特殊手赚微信号小组' ?></th>
                </tr>
                <?php else : //非订阅号类型?>
                <tr>
                    <th colspan="2"
                        class="alignleft"><?php echo $page['info']['id'] ? '修改'.BusinessTypes::model()->getNameByPk($bid).'微信号小组' : '添加'.BusinessTypes::model()->getNameByPk($bid).'微信号小组' ?></th>
                </tr>
            <?php endif; ?>
            <?php if ($page['info']['id']): ?>
                <tr>
                    <td width="150">ID：</td>
                    <td><?php echo $page['info']['id'] ?></td>
                </tr>
            <?php endif ?>
            <tr>
                <td width="150">微信号小组名称：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="name" name="name"
                           value="<?php echo $page['info']['wechat_group_name']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="150">计费方式：</td>
                <td>
                    <?php
                    if (!$page['info']['id']) {
                        if ($bid == 0) {
                            $chargingTypes = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=2'));
                        } else {
                            $chargingTypes = Dtable::toArr(BusinessTypeRelation::model()->findAll('bid=:bid', array(':bid' => $bid)));
                        }
                        foreach ($chargingTypes as $k => $v) {
                            $chargingTypes[$k]['cname'] = vars::get_field_str('charging_type', $v['charging_type']);
                        }
                        echo CHtml::dropDownList('charging_type', $page['info']['charging_type'], CHtml::listData($chargingTypes, 'charging_type', 'cname'),
                            array(
                                'empty' => '计费方式',
                                'ajax' => array(
                                    'type' => 'POST',
                                    'url' => $this->createUrl('weChatGroup/getSuitableWechatIds'),
                                    'update' => '#searchedWeChatIds',
                                    'data' => array('charging_type' => 'js:$("#charging_type").val()', 'bid' => $bid),
                                ),
                                'onchange' => '$("#addedWeChatIds").html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")'
                            )
                        );
                    } else { ?>
                        <input type="hidden" id="charging_type" name="charging_type" value="<?php echo $page['info']['charging_type'];?>" />
                        <?php
                        echo vars::get_field_str('charging_type', $page['info']['charging_type']);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th colspan="2" class="alignleft">该小组已添加的微信号</th>
            </tr>
            <tr>
                <td colspan="2" id="addedWeChatIds">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php if ($page['info']['id'] && $page['info']['wechat_ids'] != ''):
                        foreach ($page['info']['wechat_id'] as $key => $val) {
                            $weChatId = WeChat::model()->findByPk($val)->wechat_id;
                            ?>
                            <input id='weChatIds' onclick='weChatIdsSelect(this)' name='weChat_list[]'
                                   style='width: 20px' checked='checked' type='checkbox'
                                   value='<?php echo $val; ?>' attr-val='<?php echo $weChatId; ?>'>
                            <span id='<?php echo $weChatId; ?>'> <?php echo $weChatId; ?></span>
                        <?php }endif ?>
                </td>
            </tr>
            <tr>
                <th colspan="2" class="alignleft">添加微信号</th>
            </tr>
            <tr>
                <td colspan="2">
                    商品：<input style="width:80px;height: 20px" type="text" id="gid" value="">&nbsp;&nbsp;
                    形象：<input style="width:80px;height: 20px" type="text" id="catid" value="">&nbsp;&nbsp;
                    推广人员：
                    <?php
                    $promotionStafflist = PromotionStaff::model()->getPromotionStaffList();
                    echo CHtml::dropDownList('user_id', $this->get('user_id'), CHtml::listData($promotionStafflist, 'user_id', 'name'),
                        array('empty' => '全部')
                    );
                    ?>
                    客服部：
                    <?php
                    $params['htmlOptions'] = array('empty' => '客服部', 'id' => 'csid');
                    helper::getServiceSelect('csid',$params);
                    ?>
                    &nbsp;
                    微信号ID：<input style="width:80px;height: 20px" type="text" id="wechat_id" value="">&nbsp;
                    <input type="button" class="but" value="查询" onclick="weChatIdsSearch(this)">
                </td>
            </tr>
            <tr>
                <td colspan="2" id="searchedWeChatIds">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?php $i = 0;
                    foreach ($page['weChatList'] as $key => $val) { ?>
                        <input onclick="weChatIdsSelect(this)" style="width: 20px" type="checkbox"
                               value="<?php echo $val['id']; ?>" attr-val="<?php echo $val['wechat_id']; ?>"/>
                        <span id="<?php echo $val['id']; ?>"><?php echo $val['wechat_id']; ?></span>
                        <?php
                        $i++;
                        if ($i == 5) {
                            $i = 0; ?>
                            <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php }
                    } ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft">
                    <input type="submit" class="but" id="subtn" value="确定"/>

                    <input type="button" class="but" value="返回"
                           onclick="window.location='<?php echo $page['info']['id']?$this->get('url'):$this->createUrl('weChatGroup/index');; ?>'"/>
                </td>
            </tr>
        </table>
    </form>
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
            gid: $(obj).parent().find('#gid').val(),
            psid: $(obj).parent().find('#psid').val(),
            catid: $(obj).parent().find('#catid').val(),
            csid: $(obj).parent().find('#csid').val(),
            wechat_id: $(obj).parent().find('#wechat_id').val(),
            user_id: $(obj).parent().find('#user_id').val(),
            charging_type: $(obj).parents().find('#charging_type').val(),
            bid: <?php echo $bid;?>,
            ids: idStr
        };
        $.get('<?php echo $this->createUrl('weChatGroup/searchHandler'); ?>', params, function (data) {
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
        if ($(obj).attr('checked') == 'checked') {
            var addedObj = $("#addedWeChatIds");
            var content = "<input id='weChatIds' onclick='weChatIdsSelect(this)' name='weChat_list[]' style='width: 20px' checked='checked' type='checkbox' value='" + $(obj).val() + "' attr-val='" + $(obj).attr('attr-val') + "'><span id='" + $(obj).attr('attr-val') + "'>" + $(obj).attr('attr-val') + "</span>";
            //console.log(content);
            addedObj.append(content);
            $("#" + $(obj).val() + "").remove();
            $(obj).remove();
        } else {
            var weChatObj = $("#searchedWeChatIds");
            var content = "<input id='weChatIds' onclick='weChatIdsSelect(this)'  style='width: 20px'  type='checkbox' value='" + $(obj).val() + "' attr-val='" + $(obj).attr('attr-val') + "'><span id='" + $(obj).val() + "'>" + $(obj).attr('attr-val') + "</span>";
            weChatObj.append(content);
            $("#" + $(obj).attr('attr-val') + "").remove();
            $(obj).remove();
        }
    }


</script>
