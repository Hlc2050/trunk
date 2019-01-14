//特殊 ，获取企业id

function get_company_id(){
	var url=window.location;
    var rule=/company_id=(\d+)/i;
    var result=rule.exec(url);
    var company_id=0;
    if(result){
    	company_id=result[1];
    }
    return company_id;
}

//放入购物车 ,jump 是是否跳转到 结算页面，attr 非订餐时间要不用放进购物车
function addcart(goods_id,goods_total,jump,get_attr){
	var goods_attr='';
	if(get_attr==1){
		goods_attr_arr=[];
		$(".attr_value").each(function(){
			var k=$(this).attr("attr_type_id");
			var v=$(this).attr("value");
			var m=$(this).attr("addmoney");
			var a={"attr_type_id":$(this).attr("attr_type_id"),"attr_id":$(this).attr("value")}
			goods_attr_arr.push(a);
		})
		goods_attr=encodeURIComponent($.toJSON(goods_attr_arr));
	    //alert(goods_attr);
	}
	
	var company_id=get_company_id();
    
	$.get("/cart/addCart?goods_id="+goods_id+"&company_id="+company_id+"&goods_total="+goods_total+"&goods_attr="+goods_attr,function(data){
		//alert(data);
		try{
		var json=$.evalJSON(data);
		if(json.code==-3){
			window.location='/user/site/login?url='+document.URL;
		    return false;	
		}
		if(json.code<=0){
		    alert(json.statewords);
		}else{
			if(jump==1){
				window.location='/order/list';
			}else{
				art.dialog({content:'成功放入购物车<div class="gocart"><a href="/cart/index" class="checkout_btn">去购物车结算</a>',lock:true,opacity:0.2,time:2,cancel:false,icon: 'succeed'});
				top_cart();
			}
		   // window.location='/cart.php';	
		}
		//top_cart();
		
		}catch(e){alert(e.message);}	
	})
}

//删除购物车的商品
function delecart(cart_id,evalcode){
	if(!confirm('确定删除吗？')){
	    return false;	
	}
	$.get("/cart/delete?cart_id="+cart_id,function(data){													 
		try{
		var json=$.evalJSON(data);
		if(!evalcode){
			window.location.reload();
		}else{
		    try{
				eval(evalcode);
			}catch(e){alert(e.message);}	
		}
		}catch(e){alert(e.message);}	
	});
}



//删除购物车的全部商品
function delecart_all(){
	if(!confirm('确定删除吗？')){
	    return false;	
	}
	$.get("/cart/deleCartAll",function(data){													 
		try{
		var json=$.evalJSON(data);
		window.location.reload();
		}catch(e){alert(e.message);}	
	})	
}


//更新购物车的数量
function update_cart_goods_total(evalcode){
  var a=$(".update_cart_id");
	var cartdata=[];
	
	for(var i=0;i<a.length;i++){
	     var cart_id=a.eq(i).attr("cart_id");
		 var goods_total=a.eq(i).val();
		 //alert(cart_id+'的数量为'+goods_total);	
	     cartdata.push({"cart_id":cart_id,"goods_total":goods_total});
	}
	var jsonstr=($.toJSON(cartdata));
	var data="cartdata="+encodeURIComponent(jsonstr);
	$.post("/cart/updateCartTotal",data,function(jsonstr){
		if(!evalcode){
			window.location.reload();
		}else{
		    eval(evalcode);	
		}
	})
	//var cart_id_arr	
	
}

//点击取得购买数量，去确认订单
function go_get_order(element,num_element,extern){
	
	var company_id=get_company_id();

	var goods_attr='';
	if(extern=='get_goods_attr'){
		goods_attr_arr=[];
		$(".attr_value").each(function(){
			var k=$(this).attr("attr_type_id");
			var v=$(this).attr("value");
			var m=$(this).attr("addmoney");
			var a={"attr_type_id":$(this).attr("attr_type_id"),"attr_id":$(this).attr("value")}
			goods_attr_arr.push(a);
		})
		goods_attr=encodeURIComponent($.toJSON(goods_attr_arr));
	    //alert(goods_attr);
	}
	if($(num_element).val().match(/^\d+$/)==null&&parseInt($(num_element).val())<=0){
		alert('请输入正确数量');
		return false;
	}
	var url=$(element).attr('href').replace(/&num=\d+/,'&num='+$(num_element).val());//alert(url);
	url+='&goods_attr='+goods_attr+"&company_id="+company_id;	
	window.location=url;	
	return false;
}


//右侧餐车
function top_cart(){
	$.get("/cart/getCartInfo",function(jsonstr){
		var json=$.evalJSON(jsonstr);
		//alert(jsonstr);
		$("#goods_list787777 table").html('');
		$("#cartjiesuanbtnbox").html('');
		$("#cartotal078566").html(0);
		$("#jcart-subtotal strong").html('￥0.00');
		try{
		if(json.code<0||!json.goods_list || json.goods_list.length==0){
			$("#goods_list787777").hide();
			$(".nogoodstips").show();
			return false;	
		}
		$("#cartotal078566").html(json.cart_total);
		$("#jcart-subtotal strong").html(' ￥ '+json.cart_money+' 元 ');
		var listcode='';
		for(var i=0;i<json.goods_list.length;i++){
			var a=json.goods_list[i];
			var s=a.goods_total*a.now_price;//alert(a.goods_total*a.now_price);
			s=s.toString().replace(/(\.\d{2})(.*)/,'$1');
		    listcode+='<tr><td class="jcart-item-qty">'+
					  '	<input type="text"  class="update_cart_id" cart_id="'+a.cart_id+'"  value="'+a.goods_total+'"  size="2" autocomplete="off" onchange="update_cart_goods_total(\'top_cart()\');"></td>'+
					  '<td class="jcart-item-name"><div><a href="/goods/'+a.info_id+'.html">'+a.info_title+'</a><input type="hidden" name="jcart_item_name[ ]">'+
						'<input type="hidden" value="AC404F02-247B-4396-8047-82377C7E15D8"></div></td>'+
					  '<td class="jcart-item-price">￥'+s+'元'+'	<a href="javascript:void(0);" onclick="delecart('+a.cart_id+',\'top_cart()\')" class="jcart-remove">删除</a>'+
					'</td></tr>';  	
		}
		$("#goods_list787777 table").html(listcode);
		$("#goods_list787777").show();
		$(".nogoodstips").hide();
		}catch(e){alert(e.message);}
		
	})
}
//删除订单
function del_order(order_id,url){
	if(!confirm('确定删除吗？')){
	    return false;	
	}
	var postdata={order_id:order_id};
    $.post(url,postdata,function(data){													 
		try{
		var json=$.evalJSON(data);
		if(json.code>0){
			window.location.reload();
		}else{
		    alert(json.statewords);	
		}
		
		}catch(e){alert(e.message);}	
	})			
}


//删除充值失败的记录
function dele_pay(pay_id){
	if(!confirm('确定删除吗？')){
		return false;	
	}	
	$.get("/member_center.php?m=dele_pay&pay_id="+pay_id,function(data){
	try{
	 var json=$.evalJSON(data);
	 if(json.code>0){
		window.location.reload();	 
	}else{
		alert(json.statewords);	
	}
	}catch(e){alert(e.message);}															  																  											
	})
	
}




//选择配送方式之后的价钱
function set_shipping(){
	try{
	var a_money=parseFloat($("#order_money002").val());
	var shipping=$("#shipping022 option:selected");
	$("#shipping_id002").val(shipping.val());
	var shipping_monery=parseFloat(shipping.attr("money"));
	if(isNaN(shipping_monery)){
	    shipping_monery=0;	
	}
	var newmoney=shipping_monery+a_money;
	$("#newmoney002").html(changeTwoDecimal_f(shipping_monery));
	$("#new_money0077").val(changeTwoDecimal_f(newmoney));
	$("#totalmoney003").html(changeTwoDecimal_f(newmoney));
	
	}catch(e){alert(e.message);}
}


//确认订单，只需提交 地址ID，和 配送方式，附加留言
function check_order(is_login){
	try{
	var shipping_id=$("#shipping022").val();
	if(shipping_id==0){
	    alert('请选择一个配送方式');
		return false;	
	}
	if($("#t_s_area select").eq(0).val()==0){
		alert('请选择区域')
		return false;
	}else if($("#t_s_area select").eq(1).val()==$("#t_s_area select").eq(0).val()){
		alert('请选择街道')
		return false;
	}else if($("#t_s_area select").eq(2).val()==$("#t_s_area select").eq(1).val()){
		alert('请选择社区')
		return false;
	}
	var payway=$(".pay-cbox:checked").val();
	var company_id=get_company_id();
	var postscript=$("#postscript").val();
	var type=$("#type").val();
	var goods_id=$("#goods_id").val();
	var goods_attr=$("#goods_attr").val();
	var tohours=$("#dt").val();
	var data="shipping_id="+shipping_id+"&postscript="+encodeURIComponent(postscript)+
	"&address="+$("#address").val()+
	"&mobile="+$("#mobile").val()+
	"&tel="+$("#tel").val()+
	"&consignee="+encodeURIComponent($("#consignee").val())+
	"&type="+$("#type").val()+
	"&goods_id="+$("#goods_id").val()+
	"&num="+$("#num").val()+
	"&goods_attr="+$("#goods_attr").val()+
	"&region_id="+$("#t_s_area select").eq(2).val()+
	"&company_id="+company_id+
	"&payway="+payway+
	"&tohours="+tohours;
	$.post("/order/saveOrderInfo",data,function(data){
	    try{
		var json=$.evalJSON(data);
		if(json.code<=0){
		    alert(json.statewords);	
		    return false;
		}else{
			/*
			if(is_login==1){
		    	window.location='/user/order/detail?order_id='+json.user_order_id+'#pay';
			}else{
			    window.location='/order/detail?order_id='+json.user_order_id+'#pay';	
			}
			*/
			if(payway==8){
				window.location='/order/list';
				return;
			}
			
			
			var user_order_id_str=json.user_order_id;
			$("#f_order_id").val(user_order_id_str);
			$("#formpay").submit();
			return false;	
		}
		}catch(e){alert(e.message);}	
	})
	
	return false;
	}catch(e){alert(e.message);return false;}
	
}



function selectaddress(){
	try{
	$("#address").val($("#address_default option:selected").attr("data-recv_address"));
	$("#mobile").val($("#address_default option:selected").attr("data-recv_cellphone"));
	$("#consignee_name").val($("#address_default option:selected").attr("data-recv_contact"));
	}catch(e){alert(e.message);return false;}
}


function sub_pay_form(pay_type){
	$("input[name=pay_type]").val(pay_type);
	C.alert.opacty({"width":"500","height":"300","title":"温馨提示","content":"","div_tag":"#pay_complete_box"});
    $("#formpay").submit();	
}


//非会员完成支付
function sub_session_complete_pay(order_id){
	var data="order_id="+order_id;
    $.post("/session_order.php?m=balance_pay",data,function(jsonstr){
		var json=$.evalJSON(data);
		window.location='/session_order.php?m=show_user_order';
	})	
	
}


//货到付款
function set_cash_pay(order_id){
	if(!confirm('确定要货到付款吗？')){
		return false;
	}
	var data="order_id="+order_id;
	$.post("/order/cashPay",data,function(data){
		var json=eval('('+data+')');
		if(json.state>0){
			window.location.reload(true);
		}else{
			alert(json.msgwords);	
		}
	})	
    	
}

