<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends userBase {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->index_model->accessBarred();
    }

    public function index(){
		$data = array();
    	
				//header.html load

		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					// 'categoryTitle' => $data['categoryOfThisId']['CATNAME'],
					// 'keywords'      => $query[0]['KEYWORDS'],
					// 'description'   => $query[0]['DESCRIPTION']
		);
		$this->index_model->header($meta);
    	$this->load->view('member/index.html',$data);
		$this->index_model->footer();
		
    }

    public function applyList(){
    	$data='';
    	$this->load->view('member/apply_list.html',$data);
    }

    





}