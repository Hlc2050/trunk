<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 

if(!isset($page['info'])){
	$page['info']['flink_id']='';
	$page['info']['flink_hosturl']='';
	$page['info']['flink_name']='';
	$page['info']['flink_img']='';
	$page['info']['flink_order']=50;
	$page['info']['flink_is_site']=0;
	$page['info']['flink_url']='';
	$page['info']['city_id']=0;
	
}
?>
<div class="main mhead">
    <div class="snav">友情链接中心 »  
    友情链接管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl($page['info']['flink_id']?'flink/edit':'flink/add'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['flink_id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['flink_id']?'修改友链':'添加友链'; ?></th>
    </tr> 
    
    <tr>
        <td  width="100">友情链接名称：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="flink_name"   name="flink_name" value="<?php echo $page['info']['flink_name']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">分布在：</td>
        <td  class="alignleft">
         <input type="text"  class="ipt"  id="flink_hosturl"   name="flink_hosturl" value="<?php echo $page['info']['flink_hosturl']; ?>"/>
         <span> 特殊需求 </span></td>      
    </tr>
    
    <tr>
        <td  width="100">排序：</td>
        <td  class="alignleft">
        <input type="text" size="10" class="ipt"  id="flink_order"   name="flink_order" value="<?php echo $page['info']['flink_order']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">链接地址：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="flink_url"   name="flink_url" value="<?php echo $page['info']['flink_url']; ?>"/> 

        </td>      
    </tr>
    
    <tr>
        <td  width="100">全站：</td>
        <td  class="alignleft">
       <label> <input type="radio" name="flink_is_site" value="0" <?php echo $page['info']['flink_is_site']==0?'checked':''; ?> /> 首页</label>
        <label> <input type="radio" name="flink_is_site" value="1" <?php echo $page['info']['flink_is_site']==1?'checked':''; ?> /> 全站</label>

        </td>      
    </tr>
    
    <tr>
        <td  width="100">缩略图：</td>
        <td  class="alignleft">
        <div class="l">
            <input type="text" class="ipt" id="flink_img" name="flink_img" value="<?php echo $page['info']['flink_img']; ?>"/>
        </div>
        <div class="l" style="margin:0px 10px;" id="flink_img_span">
        <?php echo $page['info']['flink_img']?'<img src="'.$page['info']['flink_img'].'" width=24 height=24>':'' ?>
        </div>
        <div class="l" >
           <script>create_upload_iframe('{"func":"callback_upload","thumb":{"width":"300","height":"300"}}');</script>
        </div>

        </td>      
    </tr>
      
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('flink/index'); ?>?p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
</form>
</div>