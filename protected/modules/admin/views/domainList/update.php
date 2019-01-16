<?php require(dirname(__FILE__) . "/../common/head.php"); ?>

<style>
    .but1 {
        display: inline-block;
        height: 27px;
        line-height: 27px;
        background: #2fa4e7;
        min-width: 30px;
        text-align: center;
        color: #fff;
        padding: 0px 10px;
        border: 1px solid #2C91CB;
        margin-top: 0px;
        vertical-align: top;
        margin-left: 5px;
    }
</style>

<div class="main mhead">
    <div class="snav">域名管理 »
        管理域名
    </div>
</div>
<div class="main mbody">
    <form method="post"
          action="<?php echo $this->createUrl($page['info']['domain_ids'] ? 'domainList/edit' : 'domainList/add'); ?>">
        <input type="hidden" id="id" name="id" value="<?php echo $page['info']['domain_ids']; ?>"/>
        <input type="hidden" id="backurl" name="backurl" value="<?php echo $this->get('url'); ?>"/>

        <table class="tb3">
            <tr>
                <th colspan="8" class="alignleft"><?php echo $page['info']['domain_ids'] ? '编辑域名' : '添加域名' ?></th>
            </tr>
            <?php if ($page['info']['multi_edit']==1) {?>
                <tr>
                    <td></td>
                    <td><font color="red">批量编辑时，为空的参数将不做修改</font></td>
                    <td></td>
                </tr>
            <?php } ?>

            <?php if (!$page['info']['domain_ids']) { ?>
                <tr>
                    <td class="alignleft">域名</td>
                    <td class="alignleft">费用</td>
                    <td class="alignleft">是否支持Https</td>
                    <td class="alignleft">公众号域名</td>
                    <td class="alignleft">类型</td>
                    <td class="alignleft">推广人员</td>
                    <td class="alignleft">总统计组别</td>
                    <td class="alignleft">应用类型</td>
                </tr>
                <tr>
                    <td width="100"><input class="ipt" name="domain[]" value=""></td>
                    <td width="60" class="alignleft"><input class="ipt" name="money[]" value=""></td>
                    <td width="100" class="alignleft">
                        <select name="is_https[]">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </td>
                    <td width="80" class="alignleft">
                        <select name="is_public_domain[]">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </td>
                    <td width="80" class="alignleft">
                        <select name="domain_type[]">
                            <?php foreach (vars::$fields['domain_types'] as $value){?>
                                <option value="<?php echo $value['value'];?>"><?php echo $value['txt'];?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td width="100" class="alignleft">
                        <?php
                        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
                        echo CHtml::dropDownList('promotion_staff_id[]', '', CHtml::listData($promotionStafflist, 'user_id', 'name'),
                            array(
                                'empty' => '请选择',
                            )
                        );
                        ?>
                    </td>
                    <td width="100">
                        <?php
                        $cnzzCodeList = CnzzCodeManage::model()->findAll();
                        echo $str1 = CHtml::dropDownList('cnzz_code_id[]', '', CHtml::listData($cnzzCodeList, 'id', 'name'),
                            array(
                                'empty' => '请选择',
                            )
                        );
                        $str1 = str_replace("\n", "", $str1);

                        ?>
                    </td>
                    <td class="alignleft">
                        <select width="100" name="application_type[]">
                            <option value=" ">请选择</option>
                            <option value="0">普通应用</option>
                            <option value="1">静态应用</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><input class="ipt" name="domain[]" value=""></td>
                    <td class="alignleft"><input class="ipt" name="money[]" value=""></td>
                    <td class="alignleft">
                        <select name="is_https[]">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </td>
                    <td class="alignleft">
                        <select name="is_public_domain[]">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </td>
                    <td class="alignleft">
                        <select width="100" name="domain_type[]">
                            <?php foreach (vars::$fields['domain_types'] as $value){?>
                                <option value="<?php echo $value['value'];?>"><?php echo $value['txt'];?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="alignleft">
                        <?php
                        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
                        echo $str = CHtml::dropDownList('promotion_staff_id[]', '', CHtml::listData($promotionStafflist, 'user_id', 'name'),
                            array(
                                'empty' => '请选择',
                            )
                        );
                        $str = str_replace("\n", "", $str);
                        ?>
                    </td>
                    <td>
                        <?php
                        echo $str1;
                        ?>
                    </td>
                    <td class="alignleft">
                        <select width="100" name="application_type[]">
                            <option value=" ">请选择</option>
                            <option value="0">普通应用</option>
                            <option value="1">静态应用</option>
                        </select>
                    </td>
                    <td class="alignleft"><a id="add" href="#" style="font-size: xx-large;color: red">+</a></td>
                </tr>

            <?php } else { ?>
                <tr>
                    <td width="80">域名：</td>
                    <td><?php echo $page['info']['domain'] ?></td>
                    <td></td>
                </tr>
                <?php if(isset($page['info']['status']) ) { ?>
                    <tr>
                        <td>状态：</td>
                        <td><?php echo vars::get_field_str('domain_status', $page['info']['status']); ?></td>
                        <td></td>
                    </tr>
                <?php }?>
                <tr>
                    <td>费用：</td>
                    <td width="100" class="alignleft"><input class="ipt" name="money"
                                                             value="<?php echo $page['info']['money'] ?>"></td>
                    <td></td>
                </tr>
                <?php if($page['info']['no_unuse_domain'] === 0) {?>
                    <tr>
                        <td width="80">类型：</td>
                        <td class="alignleft">
                            <select id="dType" name="domain_type">
                                <option value="">请选择</option>
                                <?php foreach (vars::$fields['domain_types'] as $value){?>
                                    <option value="<?php echo $value['value'];?>" <?php if ( $page['info']['domain_type'] == "".$value['value']."") echo 'selected';?>><?php echo $value['txt'];?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>

                <tr id="tg">
                    <td>推广人员：</td>
                    <td class="alignleft">
                        <?php
                        $promotionStafflist = PromotionStaff::model()->getPromotionStaffList(1);
                        echo CHtml::dropDownList('promotion_staff_id', $page['info']['uid'], CHtml::listData($promotionStafflist, 'user_id', 'name'),
                            array(
                                'empty' => '请选择',
                            )
                        );
                        ?>
                    </td>
                    <td></td>
                </tr>
                <tr id="cnzz">
                    <td>总统计组别：</td>
                    <td class="alignleft">
                        <?php
                        $cnzzCodeList = CnzzCodeManage::model()->findAll();
                        echo $str1 = CHtml::dropDownList('cnzz_code_id', $page['info']['cnzz_code_id'], CHtml::listData($cnzzCodeList, 'id', 'name'),
                            array(
                                'empty' => '请选择',
                            )
                        );
                        $str1 = str_replace("\n", "", $str1);

                        ?>
                    </td>
                    <td></td>
                </tr>
                <?php } ?>
                <tr>
                    <td>是否支持https：</td>
                    <td class="alignleft">
                        否&nbsp;<input name="is_https" type="radio" value="0"   <?php if ($page['info']['is_https'] == '0') echo 'checked'; ?>/>&nbsp;&nbsp;&nbsp;
                        是&nbsp;<input name="is_https" type="radio"
                                      value="1" <?php if ($page['info']['is_https'] == 1) echo 'checked'; ?>/>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>公众号：</td>
                    <td class="alignleft">
                    <select name="is_public_domain">
                        <option value="">请选择</option>
                        <option value="0" <?php if($page['info']['is_public_domain'] === '0') echo "selected"; ?>>否</option>
                        <option value="1" <?php if($page['info']['is_public_domain'] === '1') echo "selected"; ?>>是</option>
                    </select>
                    </td>
                    <td></td>
                </tr>
                <?php
                if (isset($page['info']['status']) && $page['info']['status'] != 1 || $page['info']['online_domain'] === 0) {
                    ?>
                    <tr>
                        <td>域名状态：</td>
                        <td class="alignleft">
                            <select name="domain_status" >
                                <option value="">请选择</option>
                                <option value="0" <?php echo $page['info']['status'] == '0' ? 'selected' : ''; ?>>备用
                                </option>
                                <option value="2" <?php echo $page['info']['status'] == '2' ? 'selected' : ''; ?>>被拦截
                                </option>
                                <option value="3" <?php echo $page['info']['status'] == '3' ? 'selected' : ''; ?>>内容被拦截
                                </option>
                                <option value="4" <?php echo $page['info']['status'] == '4' ? 'selected' : ''; ?>>备案有问题
                                </option>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                <?php } ?>
            <?php if($page['info']['no_unuse_domain'] === 0) {?>
                <tr>
                    <td width="80">应用类型：</td>
                    <td class="alignleft">
                        <select width="100" name="application_type">
                            <option value="">请选择</option>
                            <option value="0" <?php echo $page['info']['application_type'] == '0' ? 'selected' : ''; ?>>普通应用</option>
                            <option value="1" <?php echo $page['info']['application_type'] == '1' ? 'selected' : ''; ?>>静态应用</option>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <?php }?>
                <tr>
                    <td>备注：</td>
                    <td>
                        <textarea id="mark" name="mark"><?php echo $page['info']['mark']; ?> </textarea>
                    </td>
                    <td></td>
                </tr>
                <?php
            } ?>
        </table>

        <input type="submit" class="but" id="subtn" value="确定"/>
        <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->get('url'); ?>'"/>
    </form>
</div>
<script>
    $(function () {
        window.onload = function () {
            val = $('#dType').children('option:selected').val();
            if (val == 0 || val == 3) {
                $('#cnzz').show();
            }
            else {
                $('#cnzz').hide();
            }
        };

        $('#dType').change(function () {
            val = $(this).children('option:selected').val();
            if (val == 0 || val == 3) {
                $('#cnzz').show();
            } else {
                $('#cnzz').hide();

            }
        });

        $("#add").click(function () {
            var html = '<tr>' +
                '<td ><input class="ipt" name="domain[]"></td>' +
                '<td class="alignleft"><input class="ipt" name="money[]"></td> ' +
                '<td class="alignleft"><select name="is_https[]"> <option value="0">否</option> <option value="1">是</option></select> </td>' +
                '<td class="alignleft"><select name="is_public_domain[]"> <option value="0">否</option> <option value="1">是</option></select> </td>' +
                '<td class="alignleft"><select name="domain_type[]"> <option value="0">推广</option> <option value="1">跳转</option><option value="2">白域名</option> <option value="3">短域名</option></select> </td>' +
                '<td class="alignleft"><?php echo $str; ?>' +
                '<td class="alignleft"><?php echo $str1; ?>' +
                '</td>' +
                '<td class="alignleft"><select name="application_type[]"><option value=" ">请选择</option> <option value="0">普通应用</option> <option value="1">静态应用</option> </select> </td>' +
                '</tr>';
            $(".tb3").append(html);
        });

        $(":radio").click(function () {
            if ($(this).val() == 0) {
                $("#mark").val('');
            }
        });

    })
</script>
