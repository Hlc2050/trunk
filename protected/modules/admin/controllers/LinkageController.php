<?php
class LinkageController extends AdminController{
	public $page=array();
	public function init(){
		$page=array();
		parent::init();
		$linkage_type_id=$this->get('linkage_type_id',0);
		if(!$linkage_type_id){
			$this->msg(array('state'=>-2,'msgwords'=>'请选择联动类型'));
		}
		$m=LinkageType::model()->findByPk($linkage_type_id);
		if(!$m){
			$this->msg(array('state'=>-2,'msgwords'=>'联动类型不存在'));
		}
		$page['type_info']=$this->toArr($m);
		$this->page=$page;	
	}
	public function actionIndex(){
		$page=$this->page;
		
		//搜索
		$params['where']=' and parent_id=0 and linkage_type_id='.$page['type_info']['linkage_type_id'];
		if($this->get('search_type')=='keys' && $this->get('search_txt')){
			$params['where'] .=" and(a.linkage_name like '%".$this->get('search_txt')."%') ";
		}else if($this->get('search_type')=='id'  && $this->get('search_txt')){ //网点ID
			$params['where'] .=" and(a.linkage_id=".intval($this->get('search_txt')).") ";
		}
		$params['order']="  order by a.linkage_order,a.linkage_id       ";
		$params['pagesize']=200;
		$params['pagebar']=1;
		$params['smart_order']=1;
		$page['listdata']=Dtable::model(Linkage::model()->tableName())->listdata($params);
		$this->render('index',array('page'=>$page));
		
	}	
	
	public function actionUpdate(){ 
		$page=$this->page;
		$id=$this->get('id',0);
		//显示表单
		if(!isset($_POST['id'])){
			//如果有get.id为修改，否则判断为新增;
			if($id){
							
				$info=$this->toArr(Linkage::model()->findByPk($id));
				if(!$info){
					$this->msg(array('state'=>0,'msgwords'=>'文档不存在'));
				}
				$page['info']=$info;
				//print_r($page['recommend']);
			}else{
				
			}
	
		}else{//判断为保存
			$id=$_POST['id']=intval($_POST['id']);
			//处理需要的字段
			$field=array();
			$field['linkage_name']=$this->post('linkage_name');
			if($field['linkage_name']==''){
				$this->msg(array('state'=>0,'msgwords'=>'联动菜单名称不能为空'));
			}
			$field['linkage_attr']=$this->post('linkage_attr');
			$field['linkage_order']=intval($this->post('linkage_order'));
			$field['linkage_type_id']=intval($this->post('linkage_type_id'));
			$field['linkage_name_py']=$this->post('linkage_name_py');
			$field['linkage_deep']=intval($this->post('linkage_deep'));
			$field['linkage_remark']=intval($this->post('linkage_remark'));
			$field['icon']=$this->post('icon');
			if(!isset($_POST['parent_id'])) $_POST['parent_id']=array();
			$field['parent_id']=isset($_POST['parent_id'][count($_POST['parent_id'])-1])?intval($_POST['parent_id'][count($_POST['parent_id'])-1]):0;
			//如果有post.id 为保存修改，否则为保存新增
			if($id){
				
				$dbresult=Linkage::model()->updateAll($field,"linkage_id=$id");  //修改记录
				$msgarr=array('state'=>1,'url'=>$this->createUrl('linkage/index').'?linkage_type_id='.$page['type_info']['linkage_type_id'].'&p='.$_GET['p'].''); //保存的话，跳转到之前的列表
				$logs='修改了联动菜单 ID:'.$id.''.$field['linkage_name'].' ';
			}else{	
				
				$post=$this->data('Linkage',$field);
				$dbresult=$post->save();
				$id=$post->primaryKey;
				$msgarr=array('state'=>1);  //新增的话跳转会添加的页面
				$logs="添加了联动菜单ID：$dbresult".$field['linkage_name'];
			}
			if($dbresult===false){
				//错误返回
				$this->msg(array('state'=>0));
			}else{
				//新增和修改之后的动作
				$this->logs($logs);
				//成功跳转提示
				$this->msg($msgarr);
			}
	
		}
		$this->render('update',array('page'=>$page));
	}
	
	
	public function actionDelete(){
		$page=$this->page;
		
		$idstr=$this->get('ids');
		$ids=explode(',',$idstr);
		foreach($ids as $id){	
			$m=Linkage::model()->findAllByAttributes(array('parent_id'=>$id,'linkage_type_id'=>$page['type_info']['linkage_type_id']));
			if($m){
				$this->msg(array('state'=>0,'msgwords'=>'请先删除子分类'));
			}			
			$m=Linkage::model()->findByPk($id);
			if(!$m) continue;
			$m->delete();
			
		}
		$this->logs('删除了联动菜单ID（'.$idstr.'）');
		$this->msg(array('state'=>1));	
	}
	public function ActionSaveOrder(){		
		
		$page=$this->page;
		$listorders=$this->post('listorders',array());
		foreach($listorders as $id=>$order){
			$m=Linkage::model()->findByPk($id);
			if(!$m) continue;
			$m->linkage_order=$order;
			$m->linkage_deep=isset($_POST['listdeeps'][$id])?intval($_POST['listdeeps'][$id]):0;
			$m->linkage_remark=isset($_POST['listremarks'][$id])?intval($_POST['listremarks'][$id]):0;
			$m->linkage_name_py=isset($_POST['linkage_name_py'][$id])?$_POST['linkage_name_py'][$id]:'';
			$m->linkage_attr=isset($_POST['linkage_attr'][$id])?intval($_POST['linkage_attr'][$id]):0;
			$m->save();		
		}
		$this->logs('批量设定了联动菜单'.$page['type_info']['linkage_type_name']);
		$this->msg(array('state'=>1));
	}
	


	public function  actionGetCateChildClass(){ //子栏目
		$parent_id=intval($this->get('parent_id'));
		$select_name=$this->get('select_name');
		$span_id=$this->get('span_id');
		$is_child=intval($this->get('is_child'));
		$selectnum=intval($this->get('selectnum'));
		$attr_extern=intval($this->get('attr_extern'));
		$linkage_type_id=intval($this->get('linkage_type_id'));
		$b=Linkage::model()->get_linkage($parent_id,$linkage_type_id);
		$re='';
		$extern_code='';
		foreach($b as $rs){
			if($attr_extern=='py'){
				$extern_code=' '.$attr_extern.'="'.$rs['linkage_name_py'].'" ';
			}
			$re .="<option value='".$rs['linkage_id']."' $extern_code>".$rs['linkage_name']."</option>";
	
		}
		if($re=='' && $is_child==0 ){
			die("<select name='".$select_name."'  onchange=\"cg_sele_cc(this.value,this,'".$select_name."','".$span_id."','".$linkage_type_id."','".$selectnum."')\"><option value='".$parent_id."'>--选择--</option></select>");
		}
		if($re=='' && $is_child==1 ){
			die();
		}
		$re="<select name='".$select_name."'  onchange=\"cg_sele_cc(this.value,this,'".$select_name."','".$span_id."','".$linkage_type_id."','".$selectnum."')\"><option value='".$parent_id."'>--选择--</option>".$re."</select>";
		echo $re;
		die();
	}
	public function  actionGetSelectClassForEdit(){
		$type=intval($this->get('linkage_id',0));
		$parent_id=intval($this->get('parent_id',0));
		$select_name=$this->get('select_name');
		$span_id=$this->get('span_id');
		$linkage_type_id=intval($this->get('linkage_type_id',0));
		$selectnum=$this->get('selectnum',0);
		$attr_extern=$this->get('attr_extern',0);
		$v_html='';
		$s_html='';
		if ($type==0){			
			$a['list']=Linkage::model()->get_linkage($parent_id,$linkage_type_id);
			foreach ($a['list'] as $r){
				$extern_code='';
				if($attr_extern=='py'){
					$extern_code=' '.$attr_extern.'="'.$r['linkage_name_py'].'" ';
				}
				$s_html .='<option value="'.$r['linkage_id'].'" '.$extern_code.'>'.$r['linkage_name'].'</option>';
			}
			$v_html ="<select name='".$select_name."'  onchange=\"cg_sele_cc(this.value,this,'".$select_name."','".$span_id."','".$linkage_type_id."','".$selectnum."','".$attr_extern."')\"><option value='".$type."'>--选择--</option>".$s_html."</select>";
		}else{
			while ($type<>$parent_id){
				$s_html='';
				$m=Linkage::model()->findByAttributes(array('linkage_id'=>$type,'linkage_type_id'=>$linkage_type_id));
				$type=$m->parent_id;
				$a['list']=Linkage::model()->get_linkage($type,$linkage_type_id);					
				foreach ($a['list'] as $r){
					$extern_code='';
					if($attr_extern=='py'){
						$extern_code=' '.$attr_extern.'="'.$r['linkage_name_py'].'" ';
					}
					$s_html .='<option value="'.$r['linkage_id'].'" '.($r['linkage_id']==$m->linkage_id?'selected':'').' '.$extern_code.'>'.$r['linkage_name'].'</option>';
				}
				$v_html ="<select name='".$select_name."'  onchange=\"cg_sele_cc(this.value,this,'".$select_name."','".$span_id."','".$linkage_type_id."','".$selectnum."','".$attr_extern."')\"><option value='".$type."'>--选择--</option>".$s_html."</select>".$v_html;
					
			}
		}
		die($v_html);
	}
	
	public function actionMoreUpdate(){
		$page=$this->page;
		if($_POST){
			$linkage_name_str=$this->post('linkage_name_str');
			$arr=explode(' ',$linkage_name_str);
			$py=new cn2pinyin();
			foreach($arr as $v){
				$m=new Linkage();
				$m->linkage_type_id=$this->post('linkage_type_id');
				$m->linkage_attr=intval($this->post('linkage_attr'));
				$m->linkage_deep=intval($this->post('linkage_deep'));
				if(!isset($_POST['parent_id'])) $_POST['parent_id']=array();
				$m->parent_id=isset($_POST['parent_id'][count($_POST['parent_id'])-1])?intval($_POST['parent_id'][count($_POST['parent_id'])-1]):0;		
				$m->linkage_order=50;
				$m->linkage_name=$v;
				$m->linkage_name_py=$py->get($v);
				$m->save();
			}
			$this->msg(array('state'=>1,'msgwords'=>'批量添加成功'));
		}
		
		$this->render('more_update',array('page'=>$page));
	}
	
	
	public function actionGetChildLinkageForList(){
		$page=$this->page;
		$linkage_id=$this->get('linkage_id');
		$list=Linkage::model()->get_linkage($linkage_id,$page['type_info']['linkage_type_id']);
		$recode='';
		foreach($list as $r){
			$recode .='<div class="bb002_box clearfix" id="linkage_tr_'.$r['linkage_id'].'">
		<p class="l">
		    <input type="checkbox" class="cklist" value="'.$r['linkage_id'].'" />
        '.$r['linkage_id'].'
        '.($r['icon']?'<img src="'.$r['icon'].'" width=20 height=20>':'').'		
        <a href="javascript:void(0);" onclick="get_child_tr(\''.$r['linkage_id'].'\',\''.$page['type_info']['linkage_type_id'].'\');">'.$r['linkage_name'].'</a>
       </p>
	   <p class="r">
	   <span class="aorder005"><input type="text" size="2" name="linkage_attr['.$r['linkage_id'].']" value="'.$r['linkage_attr'].'" /></span>
	   <span class="aorder004"><input type="text" size="10" name="linkage_name_py['.$r['linkage_id'].']" value="'.$r['linkage_name_py'].'" /></span>
	   <span class="aorder003"><input type="text" size="10" name="listremarks['.$r['linkage_id'].']" value="'.$r['linkage_remark'].'" /></span>
	   <span class="aorder002"><input type="text" size="2" name="listdeeps['.$r['linkage_id'].']" value="'.$r['linkage_deep'].'" /></span>
	   <span class="aorder001"><input type="text" size="2" name="listorders['.$r['linkage_id'].']" value="'.$r['linkage_order'].'" /></span>
	    <a  class="am001" href="'.$this->createUrl('linkage/moreUpdate').'?parent_id='.$r['linkage_id'].'&p='.$_GET['p'].'&linkage_type_id='.$page['type_info']['linkage_type_id'].'">批量添加</a>
	   		<a  class="am001" href="'.$this->createUrl('linkage/update').'?parent_id='.$r['linkage_id'].'&p='.$_GET['p'].'&linkage_type_id='.$page['type_info']['linkage_type_id'].'">添加子菜单</a>
		<a  class="am001" href="'.$this->createUrl('linkage/update').'?id='.$r['linkage_id'].'&p='.$_GET['p'].'&linkage_type_id='.$page['type_info']['linkage_type_id'].'">修改</a>
		</p>
		<p class="clear"></p>
		<span class="bot_line"></span>
		</div>';
		}
		echo $recode;
		die();
	
	}
	

}
?>