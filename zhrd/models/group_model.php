<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Group_model extends CI_Model{
		function __consturct(){
			parent::__construct();
			
		}
		public function getList(){
			$query	= $this->db->order_by('GROUPID','ASC');
			$query	= $this->db->get('ADMIN_GROUP');
			$result = $query->result_array();
			return $result;
		}
		

		public function getGroupIdByUser($uname=''){
			if(empty($uname)){
				$this->load->library('session');
		        $uname=$this->session->userdata('uname');	
			}
			
			$uQuery=$this->db->get_where('ADMIN',array('USERNAME'=>$uname));
			$uArr=$uQuery->result_array();
			if($uArr){
				$uinfo=$uArr[0];
				return $uinfo['GROUPID'];	
			}else{
				$this->load->library('session');
				$this->session->sess_destroy();
				msg('用户不存在',site_url('admin/index'));
			}
			
		}

		public function getGroupByUser($uname=''){
			if(empty($uname)){
				$this->load->library('session');
		        $uname=$this->session->userdata('uname');	
			}
			$uQuery=$this->db->get_where('ADMIN',array('USERNAME'=>$uname));
			$uArr=$uQuery->result_array();
			$uinfo=$uArr[0];
			$gid = $uinfo['GROUPID'];
			$gQuery = $this->db->get_where('ADMIN_GROUP',array('GROUPID'=>$gid));
			$gArr=$gQuery->result_array();
			$ginfo=$gArr[0];
			return $gname = $gArr[0]['GROUPNAME'];
		}


		public function getGroupById($uid=''){
			if(empty($uid)){
				$this->load->library('session');
		        $uid=$this->session->userdata('USERID');	
			}

			

			$uQuery=$this->db->get_where('ADMIN',array('USERID'=>$uid));
			$uArr=$uQuery->result_array();
			
			$uinfo=$uArr[0];
			$gid = $uinfo['GROUPID'];
			$gQuery = $this->db->get_where('ADMIN_GROUP',array('GROUPID'=>$gid));
			$gArr=$gQuery->result_array();
			$ginfo=$gArr[0];
			
			return $gname = $gArr[0]['GROUPNAME'];
		}		

		
		
		
	}
?>