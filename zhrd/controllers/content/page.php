<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class page extends CI_Controller{

    public function __construct()
    {        parent::__construct();
		$this->load->model('index_model');
		$this->load->library('session');
		$this->index_model->accessBarred();


    }


	public function index ($n)
	{
		$data = array();
	
		$query = $this->db->select('CATID,TITLE,KEYWORDS,DESCRIPTION,CONTENT,UPDATETIME')
					->where('CATID',$n)
					->get('PAGE')
					->result_array();

		if( !empty($query[0]['CONTENT']) ){
			$query[0]['CONTENT'] = htmlspecialchars_decode( $query[0]['CONTENT']->load() , ENT_QUOTES);
		}
		$data['content']          = $query[0];

		$queryCategory            = $this->db->select('CATID,CATNAME,TYPE,PARENTID,ISBANNER,LISTORDER,THUMB')->where('CATID',$query[0]['CATID'])->get('CATEGORY')->result_array();
		$data['categoryOfThisId'] = $queryCategory[0];
		$data['showRight']        = $this->index_model->showRight();
		$data['breadcrumbs']      = $this->index_model->breadcrumbs($queryCategory,$n);

		
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => $data['categoryOfThisId']['CATNAME'],
					'keywords'      => $query[0]['KEYWORDS'],
					'description'   => $query[0]['DESCRIPTION']
		);
		$this->index_model->header($meta);
    	$this->load->view('content/show.html',$data);
		$this->index_model->footer();
	}
}    