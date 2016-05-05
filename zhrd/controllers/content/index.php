<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->load->library('session');
		$this->index_model->accessBarred();
    }

    public function index(){
    	$data = '';		
		$data['kuaiSuTongdao'] = $this->index_model->kuaiSuTongdao;
		$data['shouyepics']       = $this->index_model->getAD($this->index_model->shouyepics);					//首页图片切换
		
		//用户登录 URL
		$data['login'] = array(
					'company'    => '/member/login',
					'boc'        => '/admin/login',
					'danbao'     => '/admin/login',
					'thirdParty' => '/admin/login',
		);		
		//友情链接
		$query        = $this->db->select('LINKID,NAME,URL,LOGO')->limit(20)->get('LINK')->result_array();
		$data['link'] = $query;
		//联系我们
		$data['contentUs'] = $this->index_model->routeUri['pageUrl'] . $this->index_model->contactUs;
		//关于中行 URL
		$data['aboutBOC']  = array(
								'gaishu'  => $this->index_model->routeUri['pageUrl'] . '65',			//中国银行概述
								'zonglan' => $this->index_model->routeUri['pageUrl'] . '66',			//中国银行公司治理总览
								'zhanlue' => $this->index_model->routeUri['pageUrl'] . '67',			//中国银行发展战略
								'jigou'   => $this->index_model->routeUri['pageUrl'] . '68',			//中国银行机构
								'licheng' => $this->index_model->routeUri['pageUrl'] . '69',			//中行历程
								'rongyu'  => $this->index_model->routeUri['pageUrl'] . '70',			//中行荣誉
								'zhibi'   => $this->index_model->routeUri['pageUrl'] . '71',			//中行纸币
								'kayuan'  => $this->index_model->routeUri['pageUrl'] . '72'				//中行卡苑
		);
		//常见问题
		$data['changjianwenda'] = $this->index_model->routeUri['newsListUrl'].$this->index_model->changjianwenda; //常见问答URL

		//新闻
		$product = array(
					array('name' => 'bocGonggao' ,'catid' => '83'),		//中银公告
					array('name' => 'bocNews'    ,'catid' => '84')		//中银新闻
		);
		foreach( $product as $v)
		{
			$query = $this->db->select('ID,CATID,TYPEID,TITLE,DESCRIPTION,THUMB')
								->where('CATID',$v['catid'])
								// ->where('POSIDS',1)
								->order_by('ID','DESC')
								->limit(4,1)
								->get('CONTENT')
								->result_array();
			$data[$v['name']]		 = $this->index_model->getShowUrl($query,2);
			$data[$v['name'].'More'] = $this->index_model->routeUri['newsListUrl'] . $v['catid'];
		}

		//产品
		$product = array(
					array('name' => 'tongyong' ,'catid' => '85'),		//通用产品
					array('name' => 'keji'     ,'catid' => '86'),		//科技产品
					array('name' => 'shenong'  ,'catid' => '87'),		//涉农产品
					array('name' => 'wenhua'   ,'catid' => '88')		//文化产品
		);
		foreach( $product as $v)
		{
			$query = $this->db->select('ID,CATID,TYPEID,TITLE,THUMB,DESCRIPTION')
								->where('CATID',$v['catid'])
								// ->where('POSIDS',1)
								->order_by('ID','DESC')
								->limit(1,1)
								->get('CONTENT')
								->result_array();
			$data[$v['name']]        = $this->index_model->getShowUrl($query,1);
			$data[$v['name'].'More'] = $this->index_model->routeUri['productListUrl'] . $v['catid'];
		}


		//首页轮播
		$data['lunbo'] = $this->db->where('CATID',261)->get('CONTENT')->result_array();

		//$this->output->cache(2);
		$this->index_model->header();
    	$this->load->view('content/index.html',$data);
		$this->index_model->footer();
	}
	
	function fast(){
		$this->load->model('linkage_model',"linkage");
		$this->load->library('session');
		$data = "";
		switch ($_POST['process']) {
			case '1':
				//步骤1数据写进session
				$this->session->set_userdata($_POST['fast']);
				$this->load->view('content/fast_2.html',$data);
				break;
			case '2':
				$data  				= $_POST['fast'];
				$data['TIME'] 		= $this->session->userdata('TIME');
				$data['PRICE'] 		= $this->session->userdata('PRICE');
				$data['APPLYDATE'] 	= time();
				//sql 插入fast表
				$this->db->insert('FAST',$data);
				if ($data['TRADEID']=='44') {
					$arrs['ID'] = last_id('FAST','ID');   //获取id
					$this->session->set_userdata($arrs);
					$this->load->view('content/fast_ps.html',$data);
				}else{
					msg("恭喜您已完成贷款申请，请等待客户经理与您联系。",-1);
				}
				break;
			case '3':
				//sql 科技类型企业 更新fast表ps字段
				$data = $_POST['fast']['PS'];
				$list['PS'] = array2string($data);
				$id = $this->session->userdata('ID');
				$this->db->where('ID', $id);
				$this->db->update('FAST', $list); 
				msg("恭喜您已完成贷款申请，请等待客户经理与您联系。",'',5,true);
				break;	
			default:
				$this->load->view('content/fast_1.html',$data);
				break;
		}
	}  
}    