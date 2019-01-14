<?php $page['doctype']=isset($page['doctype'])?$page['doctype']:0; ?>
<?php $page['dialog_skin']=isset($page['dialog_skin'])?$page['dialog_skin']:'default'; ?>
<?php if($page['doctype']==0){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php }else if($page['doctype']==1){?>
<html>
<?php }?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo Yii::app()->params['management']['name']; ?></title>
<link rel="stylesheet" media="screen" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>admin/base.css?v2015031213" />
<link rel="stylesheet" media="screen" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>admin/<?php echo $this->admin_style;?>/admin.css?v2015031213" />
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/jquery-1.7.1.min.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/jquery.external.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/common.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/colorpicker.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>admin/js/common.js?v346" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/My97DatePicker/WdatePicker.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>admin/js/linkage.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>admin/js/plupload.full.min.js" ></script>
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/xheditor/xheditor-1.1.14-zh-cn.min.js"></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/xheditor/xheditor_plugins/coder.js"></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog4.1.7/artDialog.js?skin=<?php echo $page['dialog_skin'];?>" ></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog4.1.7/plugins/iframeTools.source.js" ></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/tableExport/tableExport.js" ></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/tableExport/jquery.base64.js" ></script>
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>default/js/echarts.common.min.js" ></script>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/jquery_tree/jquery.treeview.css" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/font_awesome/css/font-awesome.css" />
<script  src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/jquery_tree/jquery.treeview.js" ></script>


<link rel="stylesheet" href="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog-master/css/ui-dialog.css">
<script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/artDialog-master/dist/dialog-plus.js"></script>
    <script src="<?php echo Yii::app()->params['basic']['cssurl']; ?>lib/js/jquery.freezeheader.js"></script>

</head>
<body  <?php echo isset($page['body_extern'])?$page['body_extern']:''; ?>>



<script>
    $(document).ready(function() {
        try {
            var h1 = $(window).height();
            var h2 = $(".fixTh").offset().top;
            var h_tb=$('.fixTh').height();
            var h3 = $('.mfoot').height();
            var h = h1 - h2 - h3 - 70;
            if(h_tb+h2+h3<h1){
                console.log('need not fixed  ');
                return false;
            }
            $(".fixTh").freezeHeader({'height': h + 'px'});
        }catch(e){console.log(e.message)}

        //$(".fixTh").freezeHeader();
    });
    function exportTable(filename) {

        $("#exportTable").table2excel({
            filename: filename //do not include extension
        });
    }
    (function($){
        $.fn.serializeJson=function(){
            var serializeObj={};
            var array=this.serializeArray();
            var str=this.serialize();
            $(array).each(function(){
                if(serializeObj[this.name]){
                    if($.isArray(serializeObj[this.name])){
                        serializeObj[this.name].push(this.value);
                    }else{
                        serializeObj[this.name]=[serializeObj[this.name],this.value];
                    }
                }else{
                    serializeObj[this.name]=this.value;
                }
            });
            return serializeObj;
        };
    })(jQuery);
</script>
