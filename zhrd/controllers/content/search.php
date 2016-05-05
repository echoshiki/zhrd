<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class search extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->load->library('session');
		$this->index_model->accessBarred();
    }
	
	public function index()
	{
		$q = $this->security->xss_clean($this->input->get('q'));
		$q = strAddslashes($q);
		if($q == '') show_404();
		//字符串过滤
			$q = str_replace("and","&#97;nd",$q);
			$q = str_replace("execute","&#101;xecute",$q);
			$q = str_replace("update","&#117;pdate",$q);
			$q = str_replace("count","&#99;ount",$q);
			$q = str_replace("chr","&#99;hr",$q);
			$q = str_replace("mid","&#109;id",$q);
			$q = str_replace("master","&#109;aster",$q);
			$q = str_replace("truncate","&#116;runcate",$q);
			$q = str_replace("char","&#99;har",$q);
			$q = str_replace("declare","&#100;eclare",$q);
			$q = str_replace("select","&#115;elect",$q);
			$q = str_replace("create","&#99;reate",$q);
			$q = str_replace("delete","&#100;elete",$q);
			$q = str_replace("insert","&#105;nsert",$q);
			$q = str_replace("'","&#39;",$q);
			$q = str_replace('"',"&#34;",$q);
		$data = array();
				
		//总条数
		// $count_all_rows       = $this->db/*->where('CATID',$n)*/->where_in('CATID',$array_child)->count_all_results('CATEGORY');
		// if($count_all_rows == 0){ show_404(); }
		$data['categoryOfThisId'] = array( 'CATNAME'=>'搜索结果');
		//左侧信息栏目
		$data['categoryLeft'] = $this->index_model->categoryLeft();
		//面包屑导航
		$data['breadcrumbs']  =array(array('CATNAME' => '搜索','URL' => ''));
		//列表信息
		$queryContent         = $this->db->select('ID,CATID,TYPEID,TITLE,LISTORDER,UPDATETIME')
										->like('TITLE',$q)
										// ->limit($this->pre_page, ( 1+($pages-1) * $this->pre_page) )
										->order_by('UPDATETIME','DESC')
										->get('CONTENT')
										->result_array();
		
		$data['lists']        = $this->index_model->getShowUrl( $queryContent ); //根据当前id type属性构造URL

		//分页配置
		$data['paginationLink'] = '';
		// $config = array();
		// $config['total_rows'] = $count_all_rows;
		// $config['per_page'] = $this->pre_page;
		// $config['base_url'] = site_url().$this->index_model->routeUri['productListUrl'].$n;
		// $config['num_links'] = 10;
		// $config['uri_segment'] = 3;
		// $config['use_page_numbers'] = true;		
		// $config['prev_link'] = '上一页';
		// $config['next_link'] = '下一页';
		// $config['full_tag_open'] = '<div class="pages">';
		// $config['full_tag_close'] = '</div>';
		// $this->pagination->initialize($config);
		// $data['paginationLink'] = $this->pagination->create_links();

		//header.html load
		$meta = array(
					'contentTitle' => $q,
					'categoryTitle' => '搜索',
					'keywords' => '搜索 '.$q,
					'description' => '搜索内容  '.$q
		);
		$this->index_model->header($meta);
    	$this->load->view('content/list_newss.html',$data);
		$this->index_model->footer();
	}
	
    
}    
