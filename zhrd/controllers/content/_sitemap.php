<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sitemap extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->index_model->accessBarred();
    }

    public function index(){
		echo 1;
	}

}    