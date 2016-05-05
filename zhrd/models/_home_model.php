<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
	}
	
    public function header()
    {
		
		$this->load->view('content/header.html');
	}

    public function footer()
    {
		
		$this->load->view('content/footer.html');
	}


}