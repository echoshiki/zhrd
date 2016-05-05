<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Admin_model extends CI_Model{

		function __consturct(){
			parent::__construct();
			$this->load->library('session');
		}

		public function getList(){
			$query	= $this->db->order_by('USERID','ASC');
			$query	= $this->db->get('ADMIN');
			$result = $query->result_array();
			return $result;
		}

		public function checkLogin(){
			//判断是否登录
			$uname=$this->session->userdata('uname');
			if( empty($uname) && $this->session->userdata('adminLogged') !== true){
				redirect( site_url('admin/login'),200);
				// msg('请登录',site_url('admin/login'),'1',true);
			}else{
				return true;	
			}		
		}

		public function userInfo_id($uid){
			$query	= $this->db->where('USERID',$uid);
			$query	= $this->db->get('ADMIN');
			$result = $query->result_array();
			if($result){
				return $result[0];	
			}
		}

		public function userInfo_name($name){
			$query	= $this->db->where('USERNAME',$name);
			$query	= $this->db->get('ADMIN');
			$result = $query->result_array();
			if($result){ return $result[0]; }
		}

		

		
		
	}
?>