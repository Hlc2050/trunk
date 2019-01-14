<?php require(dirname(__FILE__)."/../common/head.php"); ?>
<style>
#collect_box{height:400px;overflow:auto;}
#collect_box div{height:28px;line-height:28px;}
</style>


<div class="main mhead">
    <div class="snav">内容中心 »  采集器管理 » 运行状态	</div>	
</div>
<div class="main mbody">
	<div><strong>采集器名称：<?php echo $page['collect']['name'];?>--正在采集第<?php echo $page['collect']['nowpage'];?>页...</strong></div>
	<div id="collect_box">		
		<?php foreach($page['list'] as $r){?>
		<div id="collect_id_<?php echo $r['detailid'];?>" data-id="<?php echo $r['detailid'];?>" data-detailurl="<?php echo $r['detailurl'];?>">(ID:<?php echo $r['detailid'];?>)<?php echo $r['detailtitle'];?></div>
		<?php }?>
	</div>
	<div class="mt10"><span id="co-state"></span></div>
</div>

<script>
function runsingle(i){
	var pagenums=<?php echo $page['collect']['pagenums'];?>;
	if(i==$("#collect_box div").length){
		var stateobj=$('#co-state');
		stateobj.html('<span>该页采集完毕，请稍后，正在检查任务状态..</span>');
		$.get("/admin/collector/cutpage?id=<?php echo $page['collect']['id'];?>",function(jsonstr){
			try{
				var json=eval('('+jsonstr+')');
				if(json.state<1){
					stateobj.css({"color":"red"});
					stateobj.append('<span>'+json.msgwords+'</span>');
				}else if(json.state==1){
					stateobj.css({"color":"green"});
					stateobj.append('<span>该页采集完成，即将跳转到上一页采集..</span>');
					setTimeout("window.location.reload();window.location=window.location.href",1500);
				}else if(json.state==2){
					stateobj.css({"color":"green",'font-weight':"bold"});
					stateobj.append('<span>该采集器已经彻底采集完成！</span>');
				}
			}catch(e){
				stateobj.css({"color":"red"});
				stateobj.append('<span>异常错误</span>');
			}
		})
		return;
	}
	var eobj=$("#collect_box div").eq(i);
	if(!eobj) return;
	var collect_id=eobj.attr('data-id');
	var detailurl=eobj.attr('data-detailurl');
	eobj.append('<span class="load-img"><img src="/static/admin/img/loading8.gif"></span>');
	/*
	$.get("/admin/collector/runsingle?id=<?php echo $page['collect']['id'];?>&collect_id="+collect_id,function(jsonstr){
		try{
			var json=eval('('+jsonstr+')');
			if(json.state<1){
				eobj.css({"color":"red"});
				eobj.append('<span>------'+json.msgwords+'</span>');
				$(".load-img").remove();
			}else{
				eobj.css({"color":"green"});
				eobj.append('<span>----<b>采集成功</b></span>');
				$(".load-img").remove();
			}
		}catch(e){
			eobj.css({"color":"red"});
			eobj.append('<span>-------异常错误</span>');
			$(".load-img").remove();
		}
		runsingle(i+1);
		if(i>8){
			$("#collect_box").scrollTop($("#collect_box").scrollTop()+28);
		}
	})
	*/
	$.ajax({
		url:"/admin/collector/runsingle?id=<?php echo $page['collect']['id'];?>&collect_id="+collect_id+"&detailurl="+encodeURIComponent(detailurl),
		cache: false,
		timeout:100*1000,
		success: function(jsonstr){
			try{
				var json=eval('('+jsonstr+')');
				if(json.state<1){
					eobj.css({"color":"red"});
					eobj.append('<span>------'+json.msgwords+'</span>');
					$(".load-img").remove();
				}else{
					eobj.css({"color":"green"});
					eobj.append('<span>----<b>采集成功</b></span>');
					$(".load-img").remove();
				}
			}catch(e){
				eobj.css({"color":"red"});
				eobj.append('<span>-------异常错误01</span>');
				$(".load-img").remove();
			}
			
			runsingle(i+1);
			if(i>8){
				$("#collect_box").scrollTop($("#collect_box").scrollTop()+28);
			}
		},
		error:function(){
			eobj.css({"color":"red"});
			eobj.append('<span>-------超时</span>');
			$(".load-img").remove();			
			runsingle(i+1);
			if(i>8){
				$("#collect_box").scrollTop($("#collect_box").scrollTop()+28);
			}
		}
	});

	
}
runsingle(0);
</script>