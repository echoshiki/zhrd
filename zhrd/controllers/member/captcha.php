<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//控制节点
class captcha extends CI_Controller{

	private $is_member;
	private $is_admin;
	public function __construct()
	{
	    parent::__construct();
		//不允许直接访问controller 需要通过路由访问
		if( $this->uri->segment(1,0) == 'member' ){
			show_404();
		}
		
		$this->load->library('session');
		$this->load->helper('captcha');
		
		
	}

	public function Index($id){
		if($_POST and $this->input->is_ajax_request() ){
			$vals = array(
						'word' => rand_str(4,TRUE),
						'img_path' => './captcha/',
						'img_url' => base_url().'captcha/',
						'font_path' => APPPATH . 'fonts' . DIRECTORY_SEPARATOR . 'HandVetica.ttf',
						'img_width' => '100',
						'img_height' => 30,
						'expiration' => 1200
			);

			$cap = create_captcha($vals);
			$this->session->set_userdata('checkCode',  $vals['word']  ); 

			echo $cap['image'];
			return;
		}
		show_404();
	}

		

}