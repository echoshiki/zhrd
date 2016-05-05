<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Msg extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->load->library('session');
    }

    public function index(){
    	$data = '';
    	$data['categoryLeft'] = $this->index_model->categoryLeft();

    	if ($this->input->post('dopost') == '1') {    		
    		
			$form = $this->input->post('form');
			$form['CREATETIME']		= time();
			$form['STATUS'] 		= 0;
			if (strlen($form['CONTENT']) > 300) {
			 	msg("留言内容不得超出100字。","-1");	
			}
			if (strlen($form['TITLE']) > 90) {
			 	msg("留言标题不得超出30字。","-1");	
			}

			if ($this->session->userdata('USERID')) {
				$form['USERID'] 	= $this->session->userdata('USERID');
				$form['TYPE']   	= 2;
				$form['GROUPID']   	= $this->session->userdata('GROUPID');
			}else if($this->session->userdata('member_id')){
				$form['USERID'] 	= $this->session->userdata('member_id');
				$form['TYPE']		= 1;				
			}else{
				msg('请登录后再留言。','-1');
			}

			$re = $this->db->insert('MSG',$form);
			if ($re) {
				msg('留言成功！',site_url('content/msg/index'));
			}
		}

		$this->load->library('Gotopage');
		$per_page = '10';
		$where    = array('STATUS'=>'1','PID'=>NULL);
		$datas = $this->gotopage->page_data('MSG',$per_page,$where,'CREATETIME DESC');
		$data['msg']   = $datas['data'];
		$data['total'] = $datas['total'];
		$data['per_page'] = $per_page;
		$data['link'] = $this->gotopage->page_p('MSG',site_url('/content/msg/index/'),$per_page,$where); 
		foreach ($data['msg'] as $k => $val) {
			if($val['TYPE'] == 1){
			    $data['msg'][$k]['USERNAME'] = getVal('MEMBER','USERNAME','USERID',$data['msg'][$k]['USERID']);
			}else{
				$data['msg'][$k]['USERNAME'] = getVal('ADMIN','USERNAME','USERID',$data['msg'][$k]['USERID']);
			}	
			//获得回复
			$this->db->where('PID',$val['ID']);
			$this->db->where('STATUS','1');
			$this->db->order_by("ID", "asc"); 
			$data['msg'][$k]['child'] = $this->db->get('MSG')->result_array();

		}
		

		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '客户留言--智慧网贷',
					// 'keywords'      => '表单详细-企业用户登录',
					// 'description'   => ''
		);
		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
		$this->index_model->header($meta, $jscss);
    	$this->load->view('content/msg_list.html',$data);
		$this->index_model->footer();
    }

}