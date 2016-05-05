<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class getuname extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }
	
	public function index(){
		if( $this->input->is_ajax_request() ){
			$arr = array('enable' => 0);
			$this->load->library('session');
			$uname = $this->session->userdata('member_username');
			if( $uname != false){
				$arr['uname']  = $uname;
				$arr['enable'] = 1;
			}
			echo json_encode($arr);
		}else{ show_404();}		
    }


}