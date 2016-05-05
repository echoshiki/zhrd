<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Role_model extends CI_Model{
		public function __consturct(){
			parent::__construct();
			
		}
		public function getList(){
			$query	= $this->db->order_by('ROLENAME','ASC');
			$query	= $this->db->get('ADMIN_ROLE');
			$result = $query->result_array();
			return $result;
		}

		public function getRoleList(){
			$query	= $this->db->where("ALLOWALL is  null ");
			$query	= $this->db->order_by('LISTORDER','ASC');
			$query	= $this->db->get('ADMIN_ROLE');
			$result = $query->result_array();
			return $result;
		}

		public function orderTreeArr($arr,$pid=0,$id='ROLEID'){
			$narr='';
			if(is_array($arr)){
				foreach($arr as $k=>$v){
					if($v['PID']==$pid){
	 					$narr[$v[$id]]=$v;
	 					$narr[$v[$id]]['son']=$this->orderTreeArr($arr,$v[$id]);	
	 				} 								
				}
			}
			return $narr;
		}

		public function getTreeList($treeList='',$roleArr='',$tag=''){
			if($treeList==''){
				$arr 		= $this->getRoleList();
				$treeList 	= $this->orderTreeArr($arr);
				//print_r($treeList);
			}
			
			if($roleArr==''){
				$gid 	= $this->uri->segment(4,0);
				$roleArr= $this->role->getRolesByGroupId($gid);
			}
			$html='';
			$tag='';
			
			foreach($treeList as $k=>$v){
				$str=in_array($v['ROLEID'],$roleArr) ? 'checked' : '';
				
				
				if($v['ISMENU']=='1'){
					$html.='<li class="li_h nf"><input name="info[roleid][]" type="checkbox" value="'.$v["ROLEID"].'"'.$str.'>'.'<span>'.$v["ROLENAME"].'</span>';						
				}elseif(trim($v["ROLENAME"])!=''){
					$html.='
					<li>
						<input name="info[roleid][]" type="checkbox" value="'.$v["ROLEID"].'" '.$str.'>'.$v["ROLENAME"].'
					</li>
					';	
				}
				if(is_array($v['son'])){
					$html.='<ul>';
					$html.=$this->getTreeList($v['son'],$roleArr,$tag);
					$html.='</ul>';
				}
				if($v['ISMENU']=='1'){
					$html.='</li>';
				}
				
			}
			return $html;
		}

		public function getMenu(){
			$query	= $this->db->order_by('ROLENAME','ASC');
			$query	= $this->db->get_where('ADMIN_ROLE',array('ISMENU'=>'1'));
			$result = $query->result_array();
			return $result;
		}



		public function checkRole(){
			$this->load->library('session');
			$uname=$this->session->userdata('uname');

			$this->load->model('group_model',"group");
			$groupid=$this->group->getGroupIdByUser();
			if(($uname=='admin')||($groupid==1)){



				//------------------------------------------------------------------------------------------------
				$cm=$this->uri->rsegment_array();
				$controller	= $cm[1];
				$method		= isset($cm[2]) ? $cm[2] : 'index' ;
				$parame		= isset($cm[3]) ? $cm[3] : '' ;
				//print_r($cm);
					
			
				
				$query=$this->db->get_where('ADMIN_ROLE',array(
														'M'=>'admin',
														'C'=>$controller,
														'A'=>$method,
													));
				$result = $query->result_array();
				//print_r($result);
				$roleid=$result[0]['ROLEID'];
				//自动存储控制器、方法到数据库——开发完成后可删除
				if(empty($result)){
					$this->db->insert('ADMIN_ROLE',array(
															'M'=>'admin',
															'C'=>$controller,
															'A'=>$method,

														));	
					msg('第一次继承访问此方法~');exit();

				}
				//------------------------------------------------------------------------------------------------------------


				return true;	
			}else{
				$cm=$this->uri->rsegment_array();
				$controller	= $cm[1];
				$method		= isset($cm[2]) ? $cm[2] : 'index' ;
				$parame		= isset($cm[3]) ? $cm[3] : '' ;
				//print_r($cm);
				//msg('没有权限访问',site_url('admin/index'));exit();	
			
				
				$query=$this->db->get_where('ADMIN_ROLE',array(
														'M'=>'admin',
														'C'=>$controller,
														'A'=>$method,
													));
				$result = $query->result_array();
				//print_r($result);
				$roleid=$result[0]['ROLEID'];
				//自动存储控制器、方法到数据库——开发完成后可删除
				if(!$result){
					$this->db->insert('ADMIN_ROLE',array(
															'M'=>'admin',
															'C'=>$controller,
															'A'=>$method,

														));	
				}

				$backUrl=!empty($_SERVER['HTTP_REFERER']) ?  $_SERVER['HTTP_REFERER'] : base_url('admin/company/comSmall');
				// var_dump($backUrl);
				/*--------------判断访问内容是否为数据库已记录的文件、方法,如果无记录则不允许访问--------------*/
				if(!$result){
					msg('没有权限访问',-1);
				}

				/*--------------判断当前登录人所属组是否有权限访问当前控制器、方法----------------*/
				$roleArr=$this->getRolesByUser();
				if(!in_array($roleid,$roleArr)){
					msg('没有权限访问',-1);
				}
			}
		}


		public function getRolesByUser($uname=''){
			if(empty($uname)){
				$this->load->library('session');
	            $uname=$this->session->userdata('uname');	
			}

            $query_user=$this->db->get_where('ADMIN',array('USERNAME'=>$uname));
            $userInfo=$query_user->result_array();
            $groupid=$userInfo[0]['GROUPID'];

            $query_group 	= $this->db->get_where('ADMIN_GROUP',array('GROUPID'=>$groupid));
            $groupArr 		= $query_group->result_array();

            if(count($groupArr)>0){            	
				$roles 			= $groupArr[0]['ROLEIDS'];
	            $rolesArr 		= explode(',',$roles);
	            //加入公用访问权限

	            $allowArr=$this->getPublicRoleArr();
	            $rolesArr=array_merge($rolesArr,$allowArr);
	            return $rolesArr;
            }else{
	        	return array();
	        }
            
		}
		public function getPublicRoleArr(){
			$query	= $this->db->get_where('ADMIN_ROLE',array('ALLOWALL'=>'1'));
	        $arr	= $query->result_array();
	        foreach($arr as $k=>$v){
	        	$narr[]=$v['ROLEID'];
	        }
	        return $narr;
		}

		public function getRolesByGroupId($groupid=''){
			if($groupid){
				$query_group	= $this->db->order_by('LISTORDER','ASC');
				$query_group	= $this->db->where('GROUPID',$groupid);
	            $query_group	= $this->db->get('ADMIN_GROUP');
	            $groupArr		= $query_group->result_array();
	            if(count($groupArr)>0){
					$roles 			= $groupArr[0]['ROLEIDS'];
            		$rolesArr 		= explode(',',$roles);
            		return $rolesArr;
	            }
	        }else{
	        	return false;
	        }
		}


		
		
		
	}
?>