<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//控制节点
class processstatus extends CI_Controller{

	private $is_member;
	private $is_admin;
	public function __construct()
	{
	    parent::__construct();
		//不允许直接访问controller 需要通过路由访问
		if( $this->uri->segment(1,0) == 'member' ){
			show_404();
		}
		
		//权限控制 必须企业用户登录 或者 后台用户登录
		$this->load->library('session');
		$this->load->model('process_model');
		$this->is_admin = $this->session->userdata('adminLogged');
		$this->is_member = $this->session->userdata('member_logged_in');
		if( ! ($this->is_admin || $this->is_member ) ){
			show_404();exit;
		}
		
		
	}

	public function Index($id){
		$data = array();
		

		$flag = $this->db->select('*')->where('ID',$id)->get('FORM')->result_array();	
		//企业用户只能查看自己的
		if($this->is_member && ( ! $this->is_admin) ){
			// print_r($flag[0]['USERID']);exit;
			if( $this->session->userdata('member_id') !=  $flag[0]['USERID']){
				show_404();
				return;
			}
		}
		$data['message'] = $flag[0];
		
		$data['process'] = $this->process_model->getconfig($id);
		
		$this->load->view('member/processstatus.html',$data);
	}

		

}

?>