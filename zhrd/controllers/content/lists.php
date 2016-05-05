<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class lists extends CI_Controller{

	//åˆ†é¡µæ¡æ•°
	private $pre_page     = 50;

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->load->model('content_model');
		$this->load->library('session');
		$this->index_model->accessBarred();
    }
	
	
	//åˆ—è¡¨é¡?
	public function index ($n,$pages = 1)
	{
		$this->load->library('pagination');
		$type = $this->uri->segment(1);
		$pid  = $this->uri->segment(2);
		$pArr = $this->db->select('PARENTID')->where('CATID',$pid)->get('CATEGORY')->result_array();
		$pid  = $pArr[0]['PARENTID'] ? $pArr[0]['PARENTID'] : $pid;

		$typeUrl = '';
		switch( $type){
			case 'product': $typeUrl = $this->index_model->routeUri['productListUrl'];break; 
			case 'news': $typeUrl = $this->index_model->routeUri['newsListUrl'];break; 
			case 'activities': $typeUrl = $this->index_model->routeUri['activitiesListUrl'];break; 
		}
		unset($type);
		$data = array();
		
		//å½“å‰idçš„cateogryä¿¡æ¯
		$queryCategory = $this->db->select('CATID,CATNAME,TYPE,PARENTID,ISBANNER,LISTORDER,THUMB')->where('TYPE','1')->or_where('TYPE','2')->or_where('TYPE','3')->get('CATEGORY')->result_array();
		foreach($queryCategory as $k => $v){
			if($v['CATID'] == $n){
				$data['categoryOfThisId'] = $v;
				break;
			}
		}
		
		//å­æ ç›®CATID
		$array_child = $this->index_model->addChindKey($queryCategory);
		foreach($array_child as $k => $v){
			if($v['CATID'] == $n)
				$array_child = $v['CHILD'];
		}
		$array_child = explode(' ',$array_child);
		array_pop($array_child);

		$array_child[] = $data['categoryOfThisId']['CATID'];
		//æ€»æ¡æ•?
		$count_all_rows       = $this->db/*->where('CATID',$n)*/->where_in('CATID',$array_child)->count_all_results('CONTENT');
		if($count_all_rows == 0){ show_404(); }
		//å·¦ä¾§ä¿¡æ¯æ ç›®
		$data['categoryLeft'] = $this->index_model->categoryLeft($pid);
		//é¢åŒ…å±‘å¯¼èˆ?
		$data['breadcrumbs']  = $this->index_model->breadcrumbs($queryCategory,$n);
		//åˆ—è¡¨ä¿¡æ¯
		$queryContent         = $this->db->select('ID,CATID,TYPEID,TITLE,LISTORDER,UPDATETIME,THUMB,DESCRIPTION')
										// ->where('CATID',$n)
										->where_in('CATID',$array_child)
										->order_by('ID','DESC')
										->get('CONTENT',$this->pre_page, ( 1+($pages-1) * $this->pre_page) )
										->result_array();

		$data['lists']        = $this->index_model->getShowUrl( $queryContent,$data['categoryOfThisId']['TYPE'] ); //æ ¹æ®å½“å‰id typeå±žæ€§æž„é€ URL




		//åˆ†é¡µé…ç½®
		$config = array();
		$config['total_rows'] = $count_all_rows;
		$config['per_page'] = $this->pre_page;
		$config['base_url'] = site_url().$typeUrl.$n;
		$config['num_links'] = 10;
		$config['uri_segment'] = 3;
		$config['use_page_numbers'] = true;		
		$config['prev_link'] = '上一页';
		$config['next_link'] = '下一页';
		$config['full_tag_open'] = '<div class="pages">';
		$config['full_tag_close'] = '</div>';
		$this->pagination->initialize($config);
		$data['paginationLink'] = $this->pagination->create_links();

		//header.html load
		$meta = array(
					// 'contentTitle' => '1',
					'categoryTitle' => $data['categoryOfThisId']['CATNAME'],
					// 'keywords' => '3',
					// 'description' => '4'
		);
		$data['sid'] = $pid;   //判断前台是否是金融频道
		$data['shouyepics']   = $this->index_model->getAD($this->index_model->shouyepics);	
		$this->index_model->header($meta);
    	$this->load->view((preg_match( '~product~', uri_string()) || preg_match( '~activities~', uri_string()))?'content/list_product.html':'content/list_news.html',$data);
		$this->index_model->footer();
	}
	
	
	
	public function category_product($n)
	{

		$data = array();

		//å½“å‰idçš„cateogryä¿¡æ¯
		$queryCategory = $this->db->select('CATID,CATNAME,TYPE,PARENTID,ISBANNER,LISTORDER,THUMB')->where('TYPE','1')->or_where('TYPE','2')->get('CATEGORY')->result_array();
		foreach($queryCategory as $k => $v){
			if($v['CATID'] == $n){
				$data['categoryOfThisId'] = $v;
				break;
			}
		}		
		//å·¦ä¾§ä¿¡æ¯æ ç›®
		$data['categoryLeft'] = $this->index_model->categoryLeft();
		//é¢åŒ…å±‘å¯¼èˆ?
		$data['breadcrumbs']  = $this->index_model->breadcrumbs($queryCategory,$n);
		
		
		//å­æ ç›®CATID
		$array_child = $this->index_model->addChindKey($queryCategory);
		foreach($array_child as $k => $v){
			if($v['CATID'] == $n)
				$array_child = $v['CHILD'];
		}
		$array_child = explode(' ',$array_child);
		array_pop($array_child);

		//èŽ·å–å­åˆ—è¡?
		$queryCategory = $this->db->select('CATID,CATNAME,TYPE')->where_in('CATID',$array_child)->get('CATEGORY')->result_array();
		$queryCategory = $this->index_model->getCategoryUrl($queryCategory);
		
		//èŽ·å–å­åˆ—è¡¨çš„å†…å®¹ 4ä¸?
		foreach($queryCategory as $k => $v){

			$queryList = $this->db->select('ID,CATID,TYPEID,TITLE,THUMB,UPDATETIME,DESCRIPTION')->where('CATID',$v['CATID'])->limit(554,1)->order_by('ID','desc')->get('CONTENT')->result_array();
			$queryList = $this->index_model->getShowUrl($queryList);
			$queryCategory[$k]['contentList'] = $queryList;
		}
		$data['categoryList'] = $queryCategory;
		unset( $queryCategory);
		
		//header.html load
		$meta = array(
					// 'contentTitle' => '1',
					'categoryTitle' => $data['categoryOfThisId']['CATNAME'],
					// 'keywords' => '3',
					// 'description' => '4'
		);
		$data['shouyepics']   = $this->index_model->getAD($this->index_model->shouyepics);
		$this->index_model->header($meta);
    	$this->load->view('content/category_product.html',$data);
		$this->index_model->footer();
	}
	
	//å†…å®¹æ˜¾ç¤ºé¡?
	public function show($n)
	{		
	
		if ($this->input->post('dopost') == '1') {    		
    		
			$form = $this->input->post('form');
			$form['CREATETIME']		= time();
			$form['STATUS'] 		= 0;
			if (strlen($form['TRUENAME']) =='') {
			 	msg("姓名不得为空。","-1");	
			}
			if (strlen($form['TEL']) =='') {
			 	msg("手机号码不得为空。","-1");	
			}
			
			//验证 手机号码是否被占用
			$count_all_rows = $this->db->where('TEL',$form['TEL'])->count_all_results('BAOMING');
			if($count_all_rows >= 1){
				//$this->session->unset_userdata('checkCode');
				msg('手机号码已被占用',"-1" );return;
			}
			unset($count_all_rows);

			/*if ($this->session->userdata('USERID')) {
				$form['USERID'] 	= $this->session->userdata('USERID');
				$form['TYPE']   	= 2;
				$form['GROUPID']   	= $this->session->userdata('GROUPID');
			}else if($this->session->userdata('member_id')){
				$form['USERID'] 	= $this->session->userdata('member_id');
				$form['TYPE']		= 1;				
			}else{
				msg('请登录后再留言。','-1');
			}*/

			$re = $this->db->insert('BAOMING',$form);
			if ($re) {
				msg('活动报名成功！',site_url('activities/show/'.$form['AID']));
			}
		}
	
	
	$data = array();
		$count_all_rows       = $this->db->where('ID',$n)->count_all_results('CONTENT');
		if($count_all_rows == 0){ show_404(); }
		
		// CONTENT and CONTENT_DATA èŽ·å–å†…å®¹
		$query = $this->db->select('ID,CATID,TYPEID,TITLE,KEYWORDS,DESCRIPTION,POSIDS,ISPAGE,INPUTTIME,UPDATETIME')->where('ID',$n)->get('CONTENT')->result_array();
		$data['content']          = $query[0];
	
		$num = $this->db->where('ID',$n)->count_all_results('CONTENT_DATA');
		if($num>0){			
			$query2 = $this->db->select('ID,CONTENT')->where('ID',$n)->get('CONTENT_DATA')->result_array();
			if ($query2[0]['CONTENT']) {
				$data['content']['CONTENT'] = $query2[0]['CONTENT']->load();
			}else{
				$data['content']['CONTENT'] = '';
			}			
		}else{
			$data['content']['CONTENT'] = '';
		}
		$data['content']['CONTENT'] = htmlspecialchars_decode( $data['content']['CONTENT'] , ENT_QUOTES);
		
		//读取文件内容，通过文件名（ID） shiki
			// 由于服务器配置问题，只能采取curl方式读取文本（已失效 2014/11/27）
	 		// $filename = 'localhost/uploads/content/'.$n.".txt";
   			// $ch = curl_init();
   			// curl_setopt ($ch, CURLOPT_URL, $filename);
   			// curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			// curl_setopt ($ch, CURLOPT_ENCODING ,'utf8'); 
   			// $contentD = curl_exec($ch);
   			// curl_close($ch);
			$filename = 'uploads/content/'.$n.".txt";
			$contentD = file_get_contents($filename);
		//读取文件内容结束

		$data['content']['CONTENT'] = htmlspecialchars_decode($contentD);

		// $queryCategory = $this->db->select('CATID,CATNAME,TYPE,PARENTID,ISBANNER,LISTORDER')->where('CATID',$query[0]['CATID'])->get('CATEGORY')->result_array();
		$queryCategory = $this->db->select('CATID,CATNAME,TYPE,PARENTID,ISBANNER,LISTORDER,THUMB')->get('CATEGORY')->result_array();
		// print_r($queryCategory);
		$data['categoryOfThisId'] = $queryCategory[0];
		// var_dump( $data['categoryOfThisId']);
		//å³ä¾§æ ç›®
		$data['showRight']        = $this->index_model->showRight();
		//é¢åŒ…å±‘å¯¼èˆ?
		$data['breadcrumbs']      = $this->index_model->breadcrumbs($queryCategory,$data['content']['CATID']);

		//header.html load
		$meta = array(
					'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => $data['categoryOfThisId']['CATNAME'],
					'keywords'      => isset($query[0]['KEYWORDS'])?$query[0]['KEYWORDS']:'',
					'description'   => isset($query[0]['DESCRIPTION'])?$query[0]['DESCRIPTION']:''
		);
		
		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
		
		$data['content']['TYPEID']==3 ? $this->index_model->header($meta, $jscss) : $this->index_model->header($meta);
		//$this->index_model->header($meta);
		
		$view=$data['content']['TYPEID']==3 ? 'content/showa.html' : 'content/show.html';
		
    	$this->load->view($view,$data);
		$this->index_model->footer();
	}
	
	
    
}    