<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Category_model extends CI_Model{
		function __consturct(){
			parent::__construct();
			
		}
		public function getList($pid=''){			
						
			$treeArr  = $this->getTreeArr($pid);
			$treeList = $this->getTreeList($treeArr);
			return $treeList;
		}
		public function getTreeList($arr,$tg=''){
			 $listArr = array();
			if((count($arr)>0)&&(is_array($arr))){
				$i=0;
				foreach($arr as $k=>$v){
					$i++;					
						
					$c=count($arr);
					
					if($v['PARENTID']==0){
						$sg				= '';
						$tgType			= "　";
					}elseif($i==$c){
						$sg 			= '　　'.$tg;
						$tgType			= "└─ ";
					}else{
						$sg				='　　'.$tg;
						$tgType			= "├─ ";
					}					

					$catArr['CATID']	 = $v['CATID'];
					$catArr['TG']		 = $sg.$tgType;
					$catArr['CATNAME']	 = $v['CATNAME'];
					$catArr['TYPE']		 = $v['TYPE'];
					$catArr['URL']		 = $v['URL'];
					$catArr['LISTORDER'] = $v['LISTORDER'];
					$catArr['PARENTID']	 = $v['PARENTID'];
					$catArr['ISBANNER']	 = $v['ISBANNER'];
					
					$listArr[]=$catArr;

			

					if((count($v['sArr'])>0)&&(is_array($v['sArr']))){
						//$sg.='++';
						$listArr2=$this->getTreeList($v['sArr'],$sg);
						
						$listArr=array_merge($listArr,$listArr2);
					}
				}
			}
			//is_array($listArr) ? $listArr :false;
			return $listArr;
		}
		public function getTreeArr($pid=''){
			$narr='';
			$arr=$this->category();
			foreach($arr as $k=>$v){
				if((int)$v['PARENTID']==(int)$pid){
					 $cid=$v['CATID'];
					 $narr[$cid]=$v;	
				}
			}
			
			if((count($narr)>0)&&(is_array($narr))){
				foreach($narr as $sk=>$sv){
					$ccid=$sv['CATID'];					
					$narr[$ccid]['sArr']=$this->getTreeArr($ccid);
				}				
			}
			return $narr;			
		}

		public function category(){
			$query	= $this->db->order_by('CATID','ASC');
			$query	= $this->db->get('CATEGORY');
			$result = $query->result_array();
			return $result;
		}

		public function getTopId(){
			
			return $result;
		}

		public function getChild($id){
			$this->db->where('PID',$id);
			$query	= $this->db->get('CATEGORY');
			$result = $query->result_array();
			return $result;
		}

		public function getAllChild($cid='',$sArr='',$f=''){
			$sids=$nsids=$v='';
			if($f!=1){
				$sArr=$this->getTreeArr($cid);
			}
			if(is_array($sArr)){
				foreach($sArr as $k=>$v){
					$sids[]=$v['CATID'];
					if(is_array($v['sArr'])){
						$nids=$this->getAllChild('',$v['sArr'],1);
						$sids=array_merge($sids,$nids);	
					}
					
				}
			}
			return $sids;					
		}
		
		

	   /*
		*构造 childID
		 *@param   catid array 所有的cateogry   select ==''返回所有的
		 *@return  
		 */
		public function childid($all,$select = '' )
		{
			
			if($select == '')
				$select = $all;
			
			foreach($select as $key => $value)
			{
				$select[$key]['childid'] = '';
				foreach($all as $val){
					if( $val['PARENTID'] == $value['CATID'] ){
						$select[$key]['childid'] = $val['CATID'];
					}
				}
			}
			return $select;
		}
		
		
		
	}
?>