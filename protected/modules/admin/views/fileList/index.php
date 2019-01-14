<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<script>
function backfile(file){
	try{
	var a=file.substr(1,file.length);
	window.parent.opener.__backfile(a,'<?php echo $_GET['element_id']; ?>');	
	window.close();
	//alert(a);
	}catch(e){alert(e.message);}
}
</script>
<style>
.iii{ margin:0px 5px 0 5px; position:relative; top:-2px;}
</style>

<div class="main mhead">
    <div class="snav">模版管理 »  themes/default/views/<?php echo $page['basedir']; ?>	</div>
    <div class="mt10 clearfix">
        <div class="l">
        <?php if($_GET['basedir']){?> 
            <input type="button" class="but2" value="返回" onClick="history.go(-1)" />&nbsp;
        <?php } ?>
        </div> 
        <div class="r">
            
        </div>
    </div>
</div>
<div class="main mbody">
<form action="?m=save_order" name="form_order" method="post">
<table class="tb">
    <tr>
        <th class="alignleft"> 文件</th>
        <th width="150">最后修改时间</th>
    </tr>
    
   <?php 
   foreach($page['files']['list'] as $r){
   ?>
    <tr>   
        
        <td class="alignleft">
		<?php if($r['is_forder']==1){?>
        <a href="?basedir=<?php echo $page['basedir'].'/'.$r['file_name']; ?>&element_id=<?php echo $_GET['element_id']; ?>"><img src="<?php echo Yii::app()->params['basic']['cssurl'];?>admin/img/dir.gif" class="iii"/> <?php echo $r['file_name']; ?></a>
        <?php }else{?>
         <a href="javascript:void(0);" onClick="backfile('<?php echo $page['basedir'].'/'.$r['file_name']; ?>');"><img src="<?php echo Yii::app()->params['basic']['cssurl'];?>admin/img/htm.gif" class="iii"/> <?php echo $r['file_name']; ?></a>
        <?php }?> 
		</td>
        <td><?php echo date('Y-m-d H:i:s',$r['file_time']); ?></td>	
    </tr>
   <?php 
   } ?> 
     
    
</table>
  <div class="pagebar"></div>
  <div class="clear"></div>
</form>
</div>

