<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		// $this->index_model->accessBarred();
		$this->load->library('session');
    }


	public function index ()
	{
		if($_POST)
		{
			$this->load->model('message_model');
			//$_POST array
			$info = array();
			$info['username']   = $this->security->xss_clean( $this->input->post('username')    );
			$info['password']   = $this->security->xss_clean( $this->input->post('password')    );
			$info['password2']  = $this->security->xss_clean( $this->input->post('password2')   );
			$info['email']      = $this->security->xss_clean( $this->input->post('email')       );
			$info['company']    = $this->security->xss_clean( $this->input->post('companyName') );
			$info['licence']    = $this->security->xss_clean( $this->input->post('licence')     );
			$info['captcha']    = $this->security->xss_clean( $this->input->post('captcha')     );
			$info['mobile']     = $this->security->xss_clean( $this->input->post('mobile')     );
			$areaId             = $this->security->xss_clean( $this->input->post('areaid')      );
			$info['areaId']     = $areaId[0];
			unset($areaId);
			
			// echo $info['company'];exit;
			
			//验证 验证码
			$captcha = $info['captcha'];
			if($captcha!=$this->session->userdata('checkCode') 
				and ( strtolower($captcha) != strtolower($this->session->userdata('checkCode') ) ) 
			){
				$this->session->unset_userdata('checkCode');
				msg('验证码不正确',-1);return;
			}
			
			if( $info['areaId'] == ''){
				msg('请选择企业所在地区','-1');
			}
			
			//验证 数据格式 （长度，类型，是否NULL）
			if( 
				$info['username'] == ''
				|| $info['password'] == '' 
				|| $info['password2'] == '' 
				|| $info['password'] != $info['password2'] 
				|| !preg_match('~^[a-zA-Z0-9]{3,12}$~',$info['username']) 
				|| !preg_match('~^[a-zA-Z0-9]{4,16}$~',$info['password']) 
				|| !preg_match('~^[a-zA-Z0-9]{4,16}$~',$info['password2']) 
				//|| ! preg_match('~^(?:[a-z0-9]+[_\-+.]?)*[a-z0-9]+@(?:([a-z0-9]+-?)*[a-z0-9]+.)+([a-z]{2,})+$~', $info['email']) 
				//|| strlen($info['email']) > 30 || strlen($info['email']) < 7  
				//|| !preg_match('~[0-9]{15}~',$info['licence'] ) 
				|| !preg_match('~[0-9]+~', $info['areaId'] )
				|| !preg_match('~[0-9]{11}~', $info['mobile'] )
			)
			{
				$this->session->unset_userdata('checkCode');
				msg('非法提交',-1);return;
			}
			
			$info['company'] = strAddslashes( $info['company'] );
			
			//username 不区分大小写
			$info['username'] = strtolower( $info['username']);
			
			//验证 用户名是否被占用
			$count_all_rows = $this->db->where('USERNAME',$info['username'])->count_all_results('MEMBER');
			if($count_all_rows >= 1){
				$this->session->unset_userdata('checkCode');
				msg('用户名已被注册',site_url('member/register') );return;
			}
			unset($count_all_rows);
			
			//验证 组织机构代码证号是否被占用
			$count_all_rows = $this->db->where('LICENCE',$info['licence'])->count_all_results('MEMBER');
			if($count_all_rows >= 1){
				$this->session->unset_userdata('checkCode');
				msg('组织机构代码证号已被注册',site_url('member/register') );return;
			}
			unset($count_all_rows);
			//截取长度
			if( strlen( $info['company']) > 100)
				$info['company'] = str_cut($info['company'], 100, '' );
			if( strlen( $info['licence']) > 50)
				$info['licence'] = str_cut($info['licence'], 50, '' );
//PHP5.3
			$_rand_str = function($length){
				$str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
				return substr(str_shuffle($str),0,$length);
			};

			$encrypt = $_rand_str(4);
			$insertData = array(
					'USERNAME'   => $info['username'],
					'PASSWD'     => md5( $info['password'].$encrypt ),
					'ENCRYPT'    => $encrypt,
					'COMPANY'    => $info['company'],
					'REGDATE'    => time(),
					'REGIP'      => $this->input->ip_address(),
					'EMAIL'      => $info['email'],
					'LICENCE'    => $info['licence'],
					'AREAID'     => $info['areaId'],
					'MOBILE'     => $info['mobile'],
					'STATUS'     => '0',
			); 

			$boo = $this->db->insert('MEMBER',$insertData);
			echo "0";
			if(!$boo){
				$this->session->unset_userdata('checkCode');
				msg('注册失败，请重试',site_url('member/register') );return;
			}

			echo "1";
			$queryMember = $this->db->select('USERID')->where('USERNAME',$info['username'])->get('MEMBER')->result_array();
			echo "2";
			//发送站内信
			$this->message_model->createNewMessage(
											$queryMember[0]['USERID'],
											'欢迎您成为智慧网贷的注册用户',
											'<div style="width:540px">尊敬的用户，您好！<p style="text-indent:2em;">欢迎您成为中国银行苏州分行智慧网贷平台的注册用户。智慧网贷平台旨在为您提供随<br/>心、便捷、高效的在线融资服务，同时为您提供丰富、全面的综合服务资讯。</p><p style="text-indent:2em;">登录本站后，您可以随时点击 “我要融资”按钮在线申请融资，我们会在第一时间处理您的服务申请。您还可以浏览网站“智慧简介”、“智慧产品”、“智慧资讯”、“智慧回答”，了解我们的产品和服务。如有任何疑问和建议，您可以通过“客户留言”模块给我们留言，还可以查看“联系我们”联系您所在区域的中行网点，我们将尽力解答您的疑惑，竭诚为您服务！</p><p style="text-indent:2em;">祝愿您的事业蒸蒸日上，中行苏州分行愿与您携手向前，共创美好明天！</p></div>'
											);
			echo "3";
			$this->session->sess_destroy();
			echo "4";
			msg("<p style='width:430px;'>尊敬的用户：恭喜您注册成功！<br />您登录智慧网贷平台的用户名为：<span style='font-weight:blod;color:#ad0a19'>".$info['username']."</span>，您可以随时用此户名享受随心、方便的智慧融资服务!<p>",base_url('member/login'),8);
			echo "5";
			
		}else{
		
			$this->load->model('linkage_model',"linkage"); 
			$this->load->helper('captcha');
			$data = array();
		
			//验证码
			$vals = array(
						'word' =>rand_str(4,TRUE),		//第二个参数去除0oO难以识别的字符
						'img_path' => './captcha/',
						'img_url' => base_url().'captcha/',
						'font_path' => APPPATH . 'fonts' . DIRECTORY_SEPARATOR . 'HandVetica.ttf',
						'img_width' => '100',
						'img_height' => 30,
						'expiration' => 1200
			);
			$cap = create_captcha($vals);
			$this->session->set_userdata('checkCode',$vals['word']); 
			$data['checkCode']=$cap['image'];    
		
			$data['breadcrumbs']  =array(array('CATNAME' => '企业用户注册','URL' => ''));
			//header.html load
			$meta = array(
						// 'contentTitle'  => $query[0]['TITLE'],
						'categoryTitle' => '用户注册',
						'keywords'      => '用户注册-企业用户注册',
						'description'   => '企业用户注册'
			);
			$jscss = array(
					// 'jquery' => 'statics/js/password/jquery-1.8.0.min.js',
					'css' => array('statics/js/validator/jquery.validator.css', 'statics/js/password/jquery.password.css'),
					'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js', 'statics/js/password/jquery.password.js' ),
				);

			$this->index_model->header($meta, $jscss);
			$this->load->view('member/register.html',$data);
			$this->index_model->footer();
		}
	}
}    