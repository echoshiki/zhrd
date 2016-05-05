<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Linkage_model extends CI_Model{
		public function __consturct(){
			parent::__construct();
			
		}
		public function getList($pid=''){
			$result='';
			$pid=	empty($pid) ? '0' : $pid;
			$query	= $this->db->order_by('LINKAGEID','ASC');
			// $query	= $this->db->where(' DISABLE = "1"');


			$query	= $this->db->get_where('LINKAGE',array('PARENTID'=>$pid));	
			$result = $query->result_array();
			return $result;	
		}


		public function getLinkage($id){
			$result='';
			$query	= $this->db->get_where('LINKAGE',array('LINKAGEID'=>$id));	
			$result = $query->result_array();
			return $result[0];	
		}

		public function getLinkageName($id){
			$result='';
			$query	= $this->db->get_where('LINKAGE',array('LINKAGEID'=>$id));	
			$result = $query->result_array();
			if($result){
				return $result[0]['NAME'];	
			}else{
				return '';
			}
				
		}

		public function getChildren($pid,$arr=''){
			if(empty($arr)){
				$arr=$this->linkage->getAllLinkage();
			}
			$cArr='';
			foreach($arr as $k=>$v){
				if($v['PARENTID']==$pid){
					$cArr[$v['LINKAGEID']] 	= $v;
					$bArr 	= $this->getChildren($v['LINKAGEID'],$arr);
					if(is_array($bArr)){
						$cArr= array_merge($cArr,$bArr);	
					}
					
				}
			}
			return $cArr;
		}

		public function getChildrenIds($pid){
			$arr=$this->getChildren($pid);
			if(is_array($arr)){
				foreach($arr as $k=>$v){
					$narr[]=$v['LINKAGEID'];
				}
				return $ids=implode(',',$narr);
			}else{
				return $pid;
			}
		}

		public function getAllLinkage(){
			$result='';
			$query	= $this->db->order_by('LINKAGEID','ASC');
			$query	= $this->db->get('LINKAGE');	
			$result = $query->result_array();
			return $result;	
		}

		public function getCheckbox($pid='0',$checkboxname='0',$selected='',$style='0',$extra = ''){
			$checkboxname 	= $checkboxname>0 ? 'ckcd' : $checkboxname;
			if($selected == ''){
				$selected = array();

			} 
			if ($pid) {
				$list = $this->getList($pid);
				$html = '';
				$s = '';
				if ($style == '1') {
					$s = '<br />';
				}
				// if (count($list)>0) {
					// foreach ($list as $val) {
						// $html.='<input type="checkbox" id="ckcd_'.$val['LINKAGEID'].'" name="'.$checkboxname.'[]" value="'.$val['LINKAGEID'].' " '.$extra.' >'.$val['NAME'].$s;
					// }											
				// }

				$cou = count($list); //去除最后一个br
				for($i=0; $i<$cou; $i++ ){
					if(count($selected) > 0){
						foreach ($selected as $k=>$v){
							$selected[$k] = trim($v);
						}
						if ($list[$i]['DISABLE'] !== '1') {

							if (!in_array($list[$i]['LINKAGEID'],$selected)) {
								$html.='<input type="checkbox" id="ckcd_'.$list[$i]['LINKAGEID'].'" name="'.$checkboxname.'[]" value="'.$list[$i]['LINKAGEID'].'" '.$extra.'  >'.$list[$i]['NAME'];
							}else{
								$html.='<input type="checkbox" id="ckcd_'.$list[$i]['LINKAGEID'].'" name="'.$checkboxname.'[]" value="'.$list[$i]['LINKAGEID'].'" '.$extra.' checked="selected">'.$list[$i]['NAME'];
							}
						}
					
					}else{

						if ($list[$i]['DISABLE'] !== '1') {
							$html.='<input type="checkbox" id="ckcd_'.$list[$i]['LINKAGEID'].'" name="'.$checkboxname.'[]" value="'.$list[$i]['LINKAGEID'].'" '.$extra.'  >'.$list[$i]['NAME'];
						}
					}
					
					if(($i+1) != $cou) $html.=$s;
				}
			}
			return $html;
		}


		public function getSelect($pid='0',$selected='0',$selectname='lxcd',$level='0',$t='0',$extra = ' data-msg-required="请选择"',$key='',$normal='--请选择--'){
			if($pid){
				$list=$this->getList($pid);
				$html='';
				if(count($list)>0){
					if($t==0){
						/*
						$html='
						
							<script type="text/javascript">
								function insertSelect(obj,selected,selectname,level,t){
									pid=$(obj).val();
									$(obj).nextAll().remove();
									$.get("'.site_url("admin/linkage/getselect/").'/"+pid+"/"+selected+"/"+selectname+"/"+level+"/"+t+"/",function(data){
										$(obj).after(data);
									});
								}
							</script>
						';
						*/
						$html.='<span>';
					}

					foreach($list as $kk=>$vv){
						if($selected==$vv['LINKAGEID']){
							$inId=$vv['LINKAGEID'];
						}
					}
					$topSelected='';
					if(empty($inId)&&$selected){
						$selectedArr=$this->getLinkage($selected);
						$topSelected=$selectedArr['PARENTID'];
					}
					
					
					$html.='<select id="'.$selectname.'_'.$pid.'" name="'.$selectname.'['.$key.']"  class="select_fl" onclick="insertSelect(this,\''.$selected.'\',\''.$selectname.'\',\''.$level.'\',\''.$t.'\')"  '.$extra.' ><option value="">'.$normal.'</option>';
					foreach($list as $k=>$v){
						if($v['DISABLE']==1){continue;}
						if(($selected>0)&&($selected==$v['LINKAGEID']||($topSelected==$v['LINKAGEID']))){
							$html.='<option value="'.$v['LINKAGEID'].'" selected="selected">'.$v['NAME'].'</option>';
						}else{
							$html.='<option value="'.$v['LINKAGEID'].'">'.$v['NAME'].'</option>';
						}
						
					}

					$html.='</select>';
					
					

					if($t==0){
						$html.='</span>';
					}
					return $html;
				}else{
					return '';
				}
			}
			
		}




		
	}
?>