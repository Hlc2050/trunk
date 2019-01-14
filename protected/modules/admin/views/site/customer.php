<?php $page['doctype']=1; ?>
<?php require(dirname(__FILE__)."/../common/head.php"); ?>


<div class="l_page_head">
    <div class="l_width clearfix">
        <div class="l_logo l"><?php echo Yii::app()->params['management']['customer']; ?></div>

    </div>
</div>

<div class="l_page_customer">
    <div class="l_width">
        <div class="l_banner_txt l">
            <div class="r"></div>
        </div>
        <div class="html_login r">
            <div  class="tit">账号密码登录</div>
            <div style="height:20px;"></div>
            <div>
                <form method="post" action="<?php echo $this->createUrl("site/login") ?>">
                    <table cellspacing="0" cellpadding="0" width="280" align="center" border="0">
                        <tr>
                            <td width="10" height="50"></td>
                            <td>&nbsp;<input type="text" id="uname" name="uname" value="" class="ipt ipt_uname" ></td>
                        </tr>
                        <tr>
                            <td height="50"></td>
                            <td>&nbsp;<input type="password" id="upass" name="upass" value="" class="ipt ipt_upass" ></td>
                        </tr>

                        <tr>
                            <td height="50"></td>
                            <td>&nbsp;<input type="rancode" style="width:100px;" id="upass" name="rancode" value="" class="ipt_uvcode" >
                                <img class="imgcode" src="<?php echo $this->createUrl('post/VerifyCode')?>" onClick="refresh_code(this)" width="90" height="26" />
                            </td>
                        </tr>

                        <tr>
                            <td height="50">&nbsp;</td>
                            <td>&nbsp;<input type="submit" id="admin_login" class="btn_login" value="登 录">&nbsp;</td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <div class="clear"></div>
    </div>

</div>


<div class="l_width">
    <div class="l_foot_nav clearfix">
        <ul>

        </ul>
        <ul>

        </ul>


    </div>
    <div class="l_copyright">
        Copyright ©2018 All Rights Reserved.  版权所有
    </div>
</div>



<script>

    if(window.top != window.self ){
        window.top.location=window.location;
    }


    $(document).ready(function(){
        //refresh_code('.imgcode');
    })
    function refresh_code(eobj){
        var url=$(eobj).attr("src");
        var v=Math.random();
        if(url.match(/\?/)){
            url=url+'1';
        }else{
            url=url+'?'+'1';
        }
        $(eobj).attr("src",url)
    }
</script>
