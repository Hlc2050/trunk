<?php require(dirname(__FILE__) . "/../common/head.php"); ?>
<div class="main mhead">
    <div class="snav">第三方统计 » 友盟统计</div>
</div>
<div class="main mbody" onload="Init()">
    <form name=frm method="post" action="<?php echo $this->createUrl('statCnzz/add'); ?>?p=<?php echo $_GET['p']; ?>">
        <table class="tb3">
            <tr>
                <th colspan="2" class="alignleft">添加单条统计</th>
            </tr>
            <tr>
                <td width="100">推广ID</td>
                <td>
                    <input class="ipt" style="font-size:small;width: 100px" name="promotion_id" id="promotion_id" />
                    &nbsp;&nbsp;&nbsp;
                    <a href="#" id="get_data">确定</a>
                </td>
            </tr>
            <tr>
                <td width="120">合作商名称：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="partner_name" name="partner_name"
                           disabled/>
                </td>
            </tr>
            <tr>
                <td width="120">渠道名称：</td>

                <td class="alignleft">
                    <input type="text" class="ipt" id="channel_name" name="channel_name"
                           disabled/>
                </td>
            </tr>
            <tr>
                <td width="120">上线日期：
                </td>

                <td class="alignleft">
                    <input type="text" class="ipt" id="stat_date" name="stat_date"
                           disabled/>
                </td>
            </tr>
            <tr>
                <th colspan="2" align="left">流量统计</th>
            </tr>
            <tr>
                <td width="100">日期：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="stat_date" name="stat_date"
                           value="<?php echo date('Y-m-d', time()); ?>"
                           onclick="WdatePicker({dateFmt:'yyyy-MM-dd'})"/>
                </td>
            </tr>
            <tr>
                <td width="100">IP：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="ip" name="ip" value="<?php echo $page['info']['ip']; ?>"/>
                </td>
            </tr>
            <tr>
                <td width="100">uv：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="uv" name="uv" value="<?php echo $page['info']['uv']; ?>"/>

                </td>
            </tr>
            <tr>
                <td width="100">pv：</td>
                <td class="alignleft">
                    <input type="text" class="ipt" id="pv" name="pv" value="<?php echo $page['info']['pv']; ?>"/>

                </td>
            </tr>
            <tr>
                <td></td>
                <td class="alignleft"><input type="submit" class="but" id="subtn" value="确定"/>
                    <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('statCnzz/index'); ?>?p=<?php echo $_GET['p']; ?>'"/>
                </td>
            </tr>

        </table>
        <script language="javascript">
            $(function () {
                $('#get_data').on('click',function () {
                    promotion_id = $('#promotion_id').val();
                    if(promotion_id==null)return false;
                    $.ajax({
                        type: "GET",
                        url: "/admin/promotion/getDataById",
                        data: "promotion_id=" + promotion_id,
                        success: function (data) {
                            if (data != ''){
                                json = eval('(' + data + ')');
                                jQuery("#channel_name").val(json.channel_name);//渠道名称
                                jQuery("#partner_name").val(json.partner_name);//合作商名称
                                jQuery("#stat_date").val(json.stat_date);//上线日期
                            }else {
                                var str = "推广id不存在";
                                jQuery("#channel_name").val(str);//渠道名称
                                jQuery("#partner_name").val(str);//合作商名称
                                jQuery("#stat_date").val(str);//上线日期
                            }
                            return false;
                        }
                    });

                })
            })

        </script>
    </form>
</div>
