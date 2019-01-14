<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<?php 
if(!isset($page['info'])){
	$page['info']['id']='';
	$page['info']['name']='';
	$page['info']['model_table']='article';
	$page['info']['keywords']='';
	$page['info']['displayorder']=50;
	
	$page['info']['cate_id']='';
	$page['info']['pagenums']='';
	$page['info']['pageurl']='';
	$page['info']['detailurl']='';
	$page['info']['nowpage']='';
	$page['info']['collect_type']='';
	$page['info']['collect_id']='';
	$page['info']['list_title']='';
	
	$page['info']['list_rule']='';
	$page['info']['detail_rule']='';
	$page['info']['replace_data']='';
}
?>
<div class="main mhead">
    <div class="snav">内容中心 »  
    采集器管理 </div>
</div>
<div class="main mbody">
<form method="post" action="<?php echo $this->createUrl('collector/update'); ?>?p=<?php echo $_GET['p'];?>">
<input type="hidden" id="id" name="id" value="<?php echo $page['info']['id']; ?>" />
<table class="tb3">
    <tr>
        <th colspan="2" class="alignleft"><?php echo $page['info']['id']?'修改采集器':'添加采集器' ?></th>
    </tr>   
    
    <tr>
        <td  width="100">采集器名称：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="name"   name="name" value="<?php echo $page['info']['name']; ?>"/> 

        </td>      
    </tr>
    
    <tr>
        <td  width="100">模型表名：</td>
        <td  class="alignleft">
        <input type="text" class="ipt"  id="model_table"   name="model_table" value=""/>

        </td>      
    </tr>
    
 
    <?php /*?>
    <tr >
        <td>商品分类：</td>
        <td style="position:relative;">
        <div style="position:relative;">
          <span id="t_s_product_cate_id">
        </span>
        <span id="t_s_product_cate_id_load"></span> 
           <script>cg_edit_sele_cc("<?php echo $page['info']['product_cate_id'];?>","product_cate_id[]","t_s_product_cate_id","15","0",0);</script>
	        
		                 </div>
        </td>
    </tr>
    <?php */?>
    <tr>
        <td  width="100">页数：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="pagenums"   name="pagenums" value="<?php echo $page['info']['pagenums']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">分页url：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="pageurl"   name="pageurl" value="<?php echo $page['info']['pageurl']; ?>"/> 
        <span class="red">* 页码用 {p} 代替</span>

        </td>      
    </tr>
    <tr>
        <td  width="100">显示顺序：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="displayorder"   name="displayorder" value="<?php echo $page['info']['displayorder']; ?>"/> 

        </td>      
    </tr>
    <tr>
        <td  width="100">当前采集：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="nowpage"   name="nowpage" value="<?php echo $page['info']['nowpage']; ?>"/> 
	<span class="red">* 第几页，任务将从该页开始采集，倒着采</span>
        </td>      
    </tr>
     <tr>
        <td  width="100">采集标识类型：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="collect_type"   name="collect_type" value="<?php echo $page['info']['collect_type']; ?>"/> 
	<span class="red">* 数字，跟采集标识ID是联合索引，用来检测是否采集过，比如 女人私房话，可以标记为1</span>
        </td>      
    </tr>
     <tr>
        <td  width="100">采集标识ID：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="collect_id"   name="collect_id" value="<?php echo $page['info']['collect_id']; ?>"/> 
	<span class="red">* 从列表页正则表达式中的第几个元素，跟上面是联合索引，用来检测是否采集过</span>
        </td>      
    </tr>
     <tr>
        <td  width="100">列表页正则：</td>
        <td  class="alignleft">
         <span class="red">* 正则表达式</span><br>
       <textarea name="list_rule" style="width:600px;height:80px;"><?php echo stripcslashes($page['info']['list_rule']); ?></textarea>
	  
        </td>      
    </tr>
    <tr>
        <td  width="100">内容页url：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="detailurl"   name="detailurl" value="<?php echo $page['info']['detailurl']; ?>"/> 
        <span class="red">* id 用 {id} 代替，其实很多网站内容页可以用它的手机站  ，如果是从列表正则里获取，则直接填写规则里的第几个元素，比如 , 1</span>

        </td>      
    </tr>
    <tr>
        <td  width="100">列表页标题：</td>
        <td  class="alignleft">
        <input type="text"  class="ipt"  id="list_title"   name="list_title" value="<?php echo $page['info']['list_title']; ?>"/> 
        <span class="red">* 直接填写规则里的第几个元素，比如 , 1</span>

        </td>      
    </tr>
    <tr>
        <td  width="100">内容页正则：</td>
        <td  class="alignleft">
         <span class="red">* json格式 
         
         	[
         		{"field":"info_title","rule":"url编码后的规则"],
         		{"field":"info_body","rule":"url编码后的规则"]
         	
         	]
         	</span><br>
            <table class="tb2" style="width:600px;">
                <tr>
                    <th width="150">字段</th>
                    <th>正则表达式</th>
                    <th></th>
                </tr>
                <tbody class="field_mbody">
                <?php $a=json_decode(stripcslashes($page['info']['detail_rule']),1);if(!$a) $a=array(); //print_r($a);die();?>
                <?php foreach($a as $r){ ?>
                    <tr>
                        <td><input name="detail_rule[field][]" class="ipt" type="text" value="<?php echo isset($r['field'])?$r['field']:''; ?>"></td>
                        <td><textarea name="detail_rule[rule][]" style="width:500px;height:40px;font-size:12px;"><?php echo urldecode(isset($r['rule'])?$r['rule']:''); ?></textarea>   </td>
                        <td><input type="button" class="but" value="删除" onclick="$(this).parent().parent().remove();"  /></td>

                    </tr>
                <?php }?>
                </tbody>
                <tr>
                    <td><input type="button" class="but" value="新增字段"  onclick="var a=$('.exmple_tr').html();$('.field_mbody').append(a);" /></td>
                    <td></td>
                </tr>

            </table>

        </td>      
    </tr>
    
    <tr>
        <td  width="100">正文替换：</td>
        <td  class="alignleft">
         <span class="red">* json格式 
         
         	[
         		{"a":"新浪网","b":"呵呵"],
         		{"a":"/upload","b":"http://aaa.com/upload"]
         	
         	]
         	</span><br>
        <textarea name="replace_data" style="width:600px;height:300px;"><?php echo stripcslashes($page['info']['replace_data']); ?></textarea>
        </td>      
    </tr>
    
    <tr>
        <td></td>
        <td  class="alignleft"><input type="submit" class="but" id="subtn" value="确定" /> <input type="button" class="but" value="返回" onclick="window.location='<?php echo $this->createUrl('collector/index'); ?>?p=<?php echo $_GET['p'];?>'" /></td>
    </tr>
</table>
</form>
</div>

<table  style="display:none;">
    <tbody  class="exmple_tr">
    <tr>
        <td><input name="detail_rule[field][]" class="ipt" type="text" value=""></td>
        <td><textarea name="detail_rule[rule][]" style="width:500px;height:40px;font-size:12px;"></textarea></td>
        <td><input type="button" class="but" value="删除"  onclick="$(this).parent().parent().remove();" ></td>

    </tr>
    </tbody>

</table>

<?php require(dirname(__FILE__)."/../common/foot.php"); ?>