<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 前台 企业用户 member  Model
 *
 */
class member_model extends CI_Model{

	public function __consturct(){
		parent::__construct();
		
	}


	public function categoryLeft(){
	// var_dump( $this->session->all_userdata() );
		$this->load->model('message_model');
		$data = array();
		
		$data['categoryList'] = array(
									array('action' => 'statics', 'CATNAME' => '申请状态', 'URL' => 'member/'),
									array('action' => 'formlist','CATNAME' => '申请记录', 'URL' => 'member/formlist'),
									array('action' => 'message','CATNAME' => '站内消息', 'URL' => 'member/message'),
									array('action' => 'changepwd','CATNAME' => '修改密码', 'URL' => '/member/changepwd/'),
									array('action' => 'logout','CATNAME' => '退出登录', 'URL' => '/member/logout/')
									);
									
		$query = $this->message_model->findNew( $this->session->userdata('member_id') );
		
		$data['messageStatic'] = array(
									'enable' => (int)$query>0 ? TRUE : FALSE,
									'number' => $query,
		);
		$data['changjianwenda'] = $this->index_model->routeUri['newsListUrl'].$this->index_model->changjianwenda; //常见问答URL

		return $this->load->view('member/left.html',$data,true);
	}	







}