<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script type="text/javascript">
    function chekFrom() {
        var compate = 0 ;
        var no_number = 0;
        $("input[name='count[]']").each(function(key, dom){
            var count = $(dom).val().split(',');
            var s = $(dom).val()+",";
            for(var i=0;i<count.length;i++)
            {
                if (s.replace(count[i]+",","").indexOf(','+count[i]+",")>-1)
                {
                    alert("重复数值："+count[i]);
                    dom.focus();
                    compate = 1;
                    break;
                }
                if (!/^[0-9]*$/.test(count[i]))
                {
                    alert('输入的数据类型有误！');
                    no_number = 1;
                    dom.focus();
                    break;
                }
            }
            if(compate == 1 || no_number== 1)
            {
                return false; //退岀each循环
            }else{
                return true;//继续each循环
            }
        });
        if(compate == 1 || no_number== 1)
        {
            return false;
        }else{
            return true;
        }
    }
</script>
<div class="main mhead">
    <div class="snav">排期管理 »
        排期类型和数值管理</div>
</div>
<div class="main mbody">
    <form method="post" action="<?php echo $this->createUrl('timetableType/edit')?>" onsubmit="return chekFrom()">
        <table class="tb3" style="width: 50%">
            <tr>
                <th colspan="1" align="center">排期类型</th>
                <th colspan="1" class="alignleft">数值</th>
            </tr>
            <?php
            foreach ($type_info as $info)
            {
              ?>
                <tr>
                    <td width="150" style="text-align: center"><?php echo $info['name']?></td>
                    <td class="alignleft">
                        <input type="text" class="ipt" id="count" name="count[]" value="<?php echo $info['count']?>" required/>
                        <input type="hidden" name="type_id[]" value="<?php echo $info['type_id']?>"/>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td></td>
                <td><p><span>*多个数值请用英文的 , 隔开</span></p></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" class="but" id="subtn" value="保存" />
                </td>
            </tr>
        </table>

        <div style="clear: both"></div>
    </form>
</div>

