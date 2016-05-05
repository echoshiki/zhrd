<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class member extends userBase {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->load->model('message_model');
		$this->load->model('form_model');
		$this->index_model->accessBarred();
    }
	
	public function login ()
	{
		if($this->session->userdata('member_logged_in') === true){ redirect(base_url('/member/'),200);}	//已经登录 转跳member index
		if($_POST){
			$this->load->model('message_model');
			
			$username  = $this->security->xss_clean($this->input->post('username'));
			$password  = $this->security->xss_clean($this->input->post('password'));
			
			$username = strAddslashes( $username);
			$password = strAddslashes( $password);
			
			$captcha  = $this->security->xss_clean($this->input->post('captcha'));
			//验证 验证码
			if($captcha!=$this->session->userdata('checkCode') 
				and ( strtolower($captcha) != strtolower($this->session->userdata('checkCode') ) ) 
			){
				$this->session->unset_userdata('checkCode');
				msg('验证码不正确', site_url('member/login') );return;
			}

			//username 不区分大小写
			$username = strtolower( $username );
			
			//验证 用户名密码
			$count_all_rows = $this->db->where('USERNAME',$username)->count_all_results('MEMBER');
			if($count_all_rows != 1){
				$this->session->unset_userdata('checkCode');
				msg('用户名或密码不正确', site_url('member/login')  );return;
			}
			$query = $this->db->select('USERID,USERNAME,PASSWD,ENCRYPT,AREAID,COMPANY,LICENCE,STATUS,MOBILE')->where('USERNAME',$username)->get('MEMBER')->result_array();
			if( md5($password.$query[0]['ENCRYPT']) != $query[0]['PASSWD']){
				$this->session->unset_userdata('checkCode');
				msg('用户名或密码不正确', site_url('member/login') );return;
			}else{
				$this->db->where('USERNAME',$username)->update('MEMBER',array('LASTDATE'=> time() ) );
				
				$form_num = $this->db->where('USERID', $query[0]['USERID'] )->count_all_results('FORM');

				$queryForm = $this->db->select('ID,TRADEID,TYPE,STATUS,FINISH,USERID,STEP,CREATETIME')
							->where('USERID', $query[0]['USERID'] )->order_by('CREATETIME','desc')->limit(5)->get('FORM')->result_array();

				//1115 多次申请 flag
				$this->form_model->formCanNew();
				$this->session->unset_userdata( array('stepex' => '','form_tradeid' => ''));
				
				$this->session->set_userdata( array( 
													'member_id'				=> $query[0]['USERID'],
													'member_username'		=> $username,
													'member_company'		=> $query[0]['COMPANY'],
													'member_license'		=> $query[0]['LICENCE'],
													'member_AREAID'			=> $query[0]['AREAID'],
													'member_status'			=> $query[0]['STATUS'],
													'member_mobile'			=> $query[0]['MOBILE'],
													'member_logged_in' 		=> TRUE,		//登录验证信息

													'form_id'				=> $form_num == 0 ? -1 : $queryForm[0]['ID'],		//用户表单状态
													'form_type'				=> $form_num == 0 ? -1 : $queryForm[0]['TYPE'],			//企业类型
													'step'					=> $form_num == 0 ? -1 : $queryForm[0]['STEP'],		//填写步骤
													'form_finish'			=> $form_num == 0 ? -1 : $queryForm[0]['FINISH'],		//是否完成

													) );
// var_dump($this->session->all_userdata() );exit;
				//测算过多的情况  添加sessoin
				if( $query[0]['FINISH'] != '1'){
					if($queryForm[0]['TYPE'] == '3' && $queryForm[0]['STEP'] == '2'){
						$this->session->set_userdata('stepex','2ex');
					}
					if( $queryForm[0]['TYPE'] == '2' && $queryForm[0]['STEP'] == '4'){
						$this->session->set_userdata('stepex','2ex');
					}
				}
				$this->session->unset_userdata('checkCode');
				redirect(base_url('/member/'),200); 	//登录成功 转跳:）
				
			}
			
		}else{
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
			$this->session->set_userdata('checkCode',$vals['word'] );
			$data['checkCode']=$cap['image'];    
			
			//register URL
			$data['registerUrl'] = 'member/register';
			//面包屑导航
			$data['breadcrumbs']  =array(array('CATNAME' => '企业用户登录','URL' => '/member/login'));
			
			//header.html load
			$meta = array(
						// 'contentTitle'  => $query[0]['TITLE'],
						'categoryTitle' => '企业用户登录',
						'keywords'      => '用户登录-企业用户登录',
						'description'   => '企业用户登录'
			);
			$jscss = array(
				'jquery' => 'statics/js/jquery-1.7.2.min.js',
				'css' => array('statics/js/validator/jquery.validator.css'),
				'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
			);
			$this->index_model->header($meta, $jscss);
			$this->load->view('member/login.html',$data);
			$this->index_model->footer();
		}
	}

	public function logout()
	{
		$this->session->set_userdata('member_logged_in', FALSE);
		$this->session->sess_destroy();
		redirect('member/login','200');
	}
	
	public function changePwd()
	{
		if($_POST){
			//数据验证
			$oldPwd  = $this->security->xss_clean($this->input->post('oldPwd'));
			$newPwd  = $this->security->xss_clean($this->input->post('newPwd'));
			$newPwd2 = $this->security->xss_clean($this->input->post('newPwd2'));
			if( $oldPwd == '' || $newPwd == '' || $newPwd2 == '' || $newPwd != $newPwd2 || !preg_match('~^[a-zA-z0-9]{4,16}$~',$oldPwd) || !preg_match('~^[a-zA-z0-9]{4,16}$~',$newPwd) || !preg_match('~^[a-zA-z0-9]{4,16}$~',$newPwd2) ){
				msg('非法提交',-1);return;
			}
			//获取原 PASSWD ENCRYPT
			$username = $this->session->userdata('member_username');
			$query    = $this->db->select('PASSWD,ENCRYPT')->where('USERNAME',$username)->get('MEMBER')->result_array();
			$oldPassword = $query[0]['PASSWD'];
			$oldEncrypt  = $query[0]['ENCRYPT'];
			unset($query);
			
			//验证原密码
			if( md5( $oldPwd.$oldEncrypt ) != $oldPassword ){
				msg('原密码不正确', -1);return;
			}
			//重置encryp
			$_rand_str = function($length){
				$str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
				return substr(str_shuffle($str),0,$length);
			};
			$encrypt  = $_rand_str(4);
			
			$this->db->where('USERNAME',$username)->update('MEMBER',array('PASSWD'=>md5($newPwd.$encrypt),'ENCRYPT'=>$encrypt) );
			$this->session->sess_destroy();

			msg('修改成功，请重新登录',site_url('/member/login'),'1');
		}else{
			$this->load->model('member_model');
			//面包屑导航
			$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'), array('CATNAME' => '修改登录密码','URL' => '/member/changepwd'));
			//左侧信息栏目
			$data['categoryLeft'] = $this->member_model->categoryLeft();
			//header.html load
			$meta = array(
						'contentTitle'  => '修改登录密码',
						'categoryTitle' => '企业用户中心',
						'keywords'      => '修改登录密码-企业用户',
						'description'   => '企业用户修改个人登录密码'
			);
			$jscss = array(
				'jquery' => 'statics/js/jquery-1.7.2.min.js',
				'css' => array('statics/js/validator/jquery.validator.css'),
				'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
			);
			$this->index_model->header($meta, $jscss);
			$this->load->view('member/changePwd.html',$data);
			$this->index_model->footer();

		}
	}

	public function index()
	{
		$this->load->model('member_model');
		$this->load->model('process_model');
		$data = array();
		//1115 多次申请 flag
		$this->form_model->formCanNew();
		// var_dump($this->session->all_userdata() );exit;
		$form_num = $this->db->where('USERID', $this->session->userdata('member_id') )->count_all_results('FORM');
		if($form_num == 0){
			$data['existForm'] = FALSE;
		}else{
			$data['existForm'] = TRUE; 
			$query = $this->db->select('ID,COMPANY,AREAID,TRADEID,DEMAND,TYPE,STATUS,FINISH,USERID,CREATETIME,ENDTIME,TIMEARRAY,LICENCE,TRADE_I,STEP,PS,PIFUDEMAND')
								->where('USERID', $this->session->userdata('member_id') )->where('ID',$this->session->userdata('form_id') ) ->get('FORM')->result_array();

			$data['enableReForm'] = $query[0]['STATUS'] == '43' ? TRUE: FALSE;
			$dataLinkAge = array(
								$query[0]['AREAID']	,										//地区
								$query[0]['TRADEID'] !==NULL ?$query[0]['TRADEID'] : NULL,  //小企业分类
								$query[0]['STATUS']	!== NULL ?$query[0]['STATUS'] : NULL,   //审核状态
								);
			$queryLinkAge = $this->db		/* AREAID TRADEID TYPE STATUS */
								->select('LINKAGEID,NAME')
								->where_in( 'LINKAGEID', $dataLinkAge )
								->get('LINKAGE')->result_array();
			unset($dataLinkAge);
			
			$_getLinkAgeName = function($arr,$id){
				foreach( $arr as $v)
					if( $v['LINKAGEID'] == $id )
						return $v['NAME'];
			};
			
			$query[0]['areaName'] = $_getLinkAgeName( $queryLinkAge, $query[0]['AREAID']);
			if( $query[0]['TYPE'] === '3'){
				$query[0]['tradeName'] = $_getLinkAgeName($queryLinkAge, $query[0]['TRADEID'] );
			}
			if( $query[0]['FINISH'] === '1'){
				$query[0]['statusName'] = $_getLinkAgeName($queryLinkAge, $query[0]['STATUS'] );
			}

			if( $query[0]['FINISH'] === '1'){
				$data['status']['form'] = TRUE;
				$data['status']['verify'] = true;
			}else{
				$data['status']['form'] = FALSE;
				$data['status']['verify'] = FALSE;
			}	
			$data['form_message'] = $query[0];
			// print_r($data['form_message']);
			
			$data['all_form_detail'] = $this->db->select('ID,CREATETIME')->where('USERID', $this->session->userdata('member_id') )->order_by('CREATETIME','desc')->get('FORM')->result_array();
			$data['process'] = $this->process_model->getconfig($this->session->userdata('form_id'));
		}
		
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		//面包屑导航
		$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'));
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '企业用户中心',
					'keywords'      => '企业用户中心 苏州 智慧网贷',
					'description'   => '企业用户中心 查看个人信息 修改信息 查看已将添加的表单'
		);
		$this->index_model->header($meta);
    	$this->load->view('member/index.html',$data);
		$this->index_model->footer();
		
    }

	public function addNewForm()
	{
	// var_dump( $this->session->all_userdata());exit;
		if(  $this->session->userdata('form_can_new') === TRUE )
		{
			$this->session->set_userdata( array(
													'form_id'				=> -1,	//用户表单状态
													'form_type'				=> -1,	//企业类型
													'step'					=> -1,	//填写步骤
													'form_finish'			=> -1,	//是否完成
													
													// 'form_can_new'			=> FALSE,
			) );
			$this->session->unset_userdata( array('stepex' => '','form_tradeid' => ''));
			redirect('apply');
		}else{
			msg('您暂时无法从新申请，可能前次申请表单尚未填写完成，或距现在申请时间不足一天',base_url('member'),3);
		}
	}
	
	public function message()
	{
		$this->load->model('message_model');
		$data = array();
		
		$data['messageNotRead'] = $this->message_model->getList( $this->session->userdata('member_id') ,'notread');
		$data['messageIsRead'] = $this->message_model->getList( $this->session->userdata('member_id') , 'isread');
				
		$this->load->model('member_model');
		//面包屑导航
		$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'), array('CATNAME' => '站内消息','URL' => '/member/message'));
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		//header.html load
		$meta = array(
					'contentTitle'  => '站内消息',
					'categoryTitle' => '企业用户中心',
					'keywords'      => '修改登录密码-企业用户',
					// 'description'   => '企业用户修改个人登录密码'
		);
		$jscss = array(
			// 'jquery' => 'statics/js/jquery-1.7.2.min.js',
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/firework.js','statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
		$this->index_model->header($meta, $jscss);
		$this->load->view('member/message.html',$data);
		$this->index_model->footer();
	}
	
	public function showMessage()
	{
		if($_POST and $this->input->is_ajax_request() )
		{
			$this->load->model('message_model');
			$code = 0;
			$member_id = $this->session->userdata('member_id');
			$messageId = $this->security->xss_clean($this->input->post('messageId'));
			if( ! preg_match( '~^[0-9,]+$~',$messageId) ) show_404();
			$query = $this->message_model->getContent( $member_id, $messageId);
			if( $query === FALSE){
				echo '{"code":"0"}';
				return;
			}
			$query[0]['code'] = '1';
			if( $query[0]['ISREAD'] == '0')
				$this->message_model->readStatus( $member_id, $query[0]['ID']);
			echo json_encode($query[0]) ;
		}else{
			show_404();
		}	
	}
	
	public function deletelMessage()
	{
		if($_POST and $this->input->is_ajax_request() )
		{
			$this->load->model('message_model');
			$code = 0;
			$messageId = $this->security->xss_clean($this->input->post('messageId'));
			if( ! preg_match( '~^[0-9,]+$~',$messageId) ) show_404();
			$messageId = explode(',',$messageId);
			
			$query = $this->message_model->delMessage( $this->session->userdata('member_id'), $messageId);
			if($query){
				$code = 1;
			}
			echo '{"code":'.$code.'}';
		}else{
			show_404();
		}	
	}

	public function readMessage()
	{
		if($_POST and $this->input->is_ajax_request() )
		{
			$this->load->model('message_model');
			$code = 0;
			$messageId = $this->security->xss_clean($this->input->post('messageId'));
			if( ! preg_match( '~^[0-9,]+$~',$messageId) ) show_404();
			$messageId = explode(',',$messageId);
			$query = $this->message_model->readStatus( $this->session->userdata('member_id'), $messageId);
			if($query){
				$code = 1;
			}
			echo '{"code":'.$code.'}';
		}else{
			show_404();
		}
	}

	//重置表单
	public function reForm()
	{
	// echo '1212';
		$this->load->helper('file');
		$this->load->model('upload_model');
		
		$member_id = $this->session->userdata('member_id');
		$form_id   = $this->session->userdata('form_id');
		$query = $this->db->select('ID,FORM,CREATETIME')->where(array( 'USERID'=>$member_id,'STATUS'=>'43'))->get('FORM')->result_array();
		// print_r( $query);exit;
		
		//begin delete files databases
		if( !empty($query)){
			delete_files( $this->upload_model->dir_string_create_for_member($query[0]['CREATETIME'], $query[0]['ID']), true,1);

			if( ! empty( $query[0]['FORM'] )){
				$this->db->where('ID', $query[0]['ID'])->delete( $query[0]['FORM'] );
			}
			$query = $this->db->where_in('ID',$query[0]['ID'])->delete('FORM');
			
			//查询剩余的表单量
			$form_num = $this->db->where('USERID', $this->session->userdata('member_id') )->count_all_results('FORM');
			if( $query)
			{ //unset session
				if( $form_num >=1 ){					//1115 多次申请 flag
					$queryForm = $this->db->select('ID,TRADEID,TYPE,STATUS,FINISH,USERID,STEP,CREATETIME') ->where('USERID', $this->session->userdata('member_id') )->order_by('CREATETIME','desc')->limit(5)->get('FORM')->result_array();
					$this->form_model->formCanNew();
					$this->session->unset_userdata( array('stepex' => '','form_tradeid' => ''));

					$this->session->set_userdata( array( 

															'form_id'				=> $form_num == 0 ? -1 : $queryForm[0]['ID'],		//用户表单状态
															'form_type'				=> $form_num == 0 ? -1 : $queryForm[0]['TYPE'],			//企业类型
															'step'					=> $form_num == 0 ? -1 : $queryForm[0]['STEP'],		//填写步骤
															'form_finish'			=> $form_num == 0 ? -1 : $queryForm[0]['FINISH'],		//是否完成
															) );
				}else{
					$this->session->set_userdata( array( 'form_id' => -1, 'form_type' => -1, 'step' => -1 ,'form_finish' => -1)	);
				}
				msg('重置成功',site_url('member'));
			}else{
				msg('删除失败，请重试',site_url('member') );
			}
		}else{
			show_404();
			}
	}

	public function showFormDetail($form_id)
	{

		$this->load->model('company_model','company');
		$this->load->model('apply_model','apply');

        $id = $form_id;
		$query = $this->db->select('ID,USERID')->where('ID',$id)->get('FORM')->result_array();

		if( $query[0]['USERID'] != $this->session->userdata('member_id'))	show_404();
        // $id = $this->session->userdata('form_id');

        if ($id == '') {
            msg('表单不存在，未填写或者已被删除！','-1');
        }

        //取得包含所有公式字段的数组
        $mainArr = $this->company->mainField($id);
        $mainArr = $mainArr[0];
        //获取包含各种计算结果的数组
        $mainField              = $this->apply->get_exp($mainArr);
        $comData['mainField']   = $mainField;

        //通过id获取主附表数据并合并为一个数组
        $comData['list']    = $this->company->showData_2($id);

        //将数据内一些元素转换成数组
        $comData['list']    = str2arrEx($comData['list']);


        if ($comData['list']['TRADE_I']) {
        //判断行业是否为自行填写的行业
            $num = (int)$comData['list']['TRADE_I'];
            //如果是非数字情况下变量num为零
            if($num == 0){
                $comData['list']['TRADE_I'] = $comData['list']['TRADE_I'];
            }else{
                $comData['list']['TRADE_I'] = get_area($comData['list']['TRADE_I']);
            }                    
        }

        if (empty($comData['list']['WARRANT'])) {
            $comData['list']['WARRANT'] = array();
        }

        $form               = $this->company->getForm($id);

        //判断选取哪个模板
        switch ($form) {
            case 'FORM_M':
                $this->load->view('member/show_comMedium.html',$comData);
                break;
            case 'FORM_A':
                $this->load->view('member/show_comSmall_1.html',$comData);
                break;
            case 'FORM_B':
                $this->load->view('member/show_comSmall_2.html',$comData);
                break;
            case 'FORM_C':
                $this->load->view('member/show_comSmall_3.html',$comData);
                break;
            case 'FORM_D':
                $this->load->view('member/show_comSmall_4.html',$comData);
                break;
            case 'FORM_T':
                $this->load->view('member/show_comSmall_5.html',$comData);
                break;            
            default:
                $this->load->view('member/show_comLarge.html',$comData);
                break;
        }
        
	}


	//修改申请表单的担保公司，修改申请表单的状态为'总行审核通过'
	public function setok(){
		$member_id = $this->session->userdata('member_id');
		$form_id = $this->session->userdata('form_id');
		if($this->input->post('dopost')){
			$this->load->model('message_model');
			$this->load->model('sms_model');
			//
			
			$post = $this->security->xss_clean($this->input->post('cks') );
			
			if(is_array($post)&&count($post)>0) {
				
			}else{
				msg('请选择担保公司', site_url( 'member'));
			}
			
			$query = $this->db->where( array( 'USERID'=> $member_id , 'ID'=>$form_id, 'STATUS' => '60') )->count_all_results('FORM');
			if($query == 0) {show_404();return;}
			//
			$form = $this->db->select('ID,FORM')->where( array( 'USERID'=> $member_id , 'ID'=>$form_id, 'STATUS' => '60') )->get('FORM')->result_array();
			$form =  $form[0]['FORM'];
						$arr_db = array(
					0 =>"136",
					'com' => $post
			);
			$arr_db = array2string( $arr_db);
			$flag2 = $this->db->where('ID', $form_id)->update($form, array( 'WARRANT' => $arr_db));
			/*
			 *TIMEARRAY   流程节点中需要的字段 区分是审核要担保 还是直接担保的 企业
			 *	正常是空  通过审核 添加担保公司的FORM表单 会设为 1
			 *
			 */
			$flag = $this->db->where( array( 'USERID'=> $member_id , 'ID'=>$form_id, 'STATUS' => '60') )->update('FORM', array('COM' => ','.implode(',',$post).',', 'STATUS'=> '58', 'TIMEARRAY'=> '1' ) );
			
			if($flag && $flag2){
				$this->sms_model->send('member',
									array(
										'formId'=>$this->session->userdata('form_id'),
										'memberId'=>$this->session->userdata('member_id')
									));//发送短信
				$messageTitle = '中国银行苏州分行中小企业融资申请受理书';
				$msgStr = $this->session->userdata('member_company').'：<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;贵公司的融资申请已受理，我行客户经理将在三个工作日<br/>之内与您取得联系，感谢贵公司选择中国银行作为您的合作伙伴。中国银行苏州分行愿与您携手同行，共创美好明天！</div><div style="float:right; padding-right:5px;">中国银行苏州分行中小企业业务部</div><div style="float:right; padding-right:60px; clear:both;">'.date("Y年m月d日",time()).'</div>';
				$status = '3';
				$this->message_model->createNewMessage($member_id, $messageTitle,$msgStr,$status);

				redirect(site_url( 'member'));
			}else{
				msg('更新失败','-1');
			}
				
			
		}else{

			$data='';
			//获取单表公司列表
			$this->load->model('linkage_model','linkage');
			
			$data['checkboxes']='';
			$this->load->view('member/setok.html',$data);	
		}
		
	}


	public function setno(){
		//修改申请表单为'废弃数据'
		$this->load->model('sms_model');
		$member_id = $this->session->userdata('member_id');
		$form_id = $this->session->userdata('form_id');
		$query = $this->db->where( array( 'USERID'=> $member_id , 'ID'=>$form_id, 'STATUS' => '60') )->count_all_results('FORM');
		if($query == 0) {show_404();return;}
		// $query = 1;
		$query = $this->db->where( array( 'USERID'=> $member_id , 'ID'=>$form_id, 'STATUS' => '60') )->update('FORM',array('STATUS'=>'210'));

		if($query){
			 $this->sms_model->send('member',
									array(
										'formId'=>$this->session->userdata('form_id'),
										'memberId'=>$this->session->userdata('member_id')
									)) ;//发送短信
								
			$this->message_model->createNewMessage($member_id, 
												'贵公司未能通过智慧网贷服务审核',
												$this->session->userdata('member_company').'：<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;经审查，由于未能接受担保的原因，我行暂时不能受理贵公司的融资申请。</div><div style="float:right;">中国银行苏州分行中小企业业务部</div><div style="float:right;clear:both">'.date("Y年m月d日",time()).'</div>'
												);
			redirect(site_url('member'),200);
			}
		else
			msg('更新失败','-1','3');
		// var_dump( $query );
	}

	
	public function sendsms()
	{
		// if($_POST){
		if($_POST and $this->input->is_ajax_request() ){
			if($this->session->userdata('member_status') != '0' ){ //防止恶意提交
				return;
			}

			$action  = $this->security->xss_clean($this->input->post('action'));
			$sessionSMSTime = $this->session->userdata('SMSTime')?$this->session->userdata('SMSTime'):0;
			$sessionSMSCode = $this->session->userdata('SMSCode')?$this->session->userdata('SMSCode'):0;
			$this->load->model('sms_model');
			$status = FALSE;
			
			if( $action == 'sendsms')		//发送短信
			{
				if( (time() - $sessionSMSTime) > 60 ) //验证是否有60秒
				{
					$SMSCode = rand_str(5);
					$SMSTime = time();
					$status = $this->sms_model->send( 'captcha', array( 
																		'captchaCode' => $SMSCode,
																		'memberId' => $this->session->userdata('member_id') 
																		) );				//发送短信 return array('code'=> '','time'=>'')

					if( $status ){					
						$this->session->set_userdata('SMSCode',$SMSCode);
						$this->session->set_userdata('SMSTime',$SMSTime);
						$this->session->unset_userdata('SMSErrorTime');
					}	
					
				}
				
			}
			else if($action == 'pregsms')//验证短信
			{
				$smscode  = $this->security->xss_clean($this->input->post('smscode'));	//获取提交的验证码
				
				if( $this->session->userdata('SMSErrorTime')  === FALSE){				//重新输入验证码次数
					$this->session->set_userdata( 'SMSErrorTime', 3);
					$SMSErrorTime = 3;
				}else{
					$SMSErrorTime = $this->session->userdata('SMSErrorTime');
				}

				if( preg_match('~^[0-9a-zA-Z]{5}$~',$smscode) )
				{
					if( (time() - $sessionSMSTime) < 60   && ( $SMSErrorTime > 0 ) && $sessionSMSCode === $smscode ){		//验证验证码
						$this->session->set_userdata('member_status','1');
						$this->session->unset_userdata('SMSErrorTime');
						$this->session->unset_userdata('SMSTime');
						$this->session->unset_userdata('SMSCode');
						$this->db->where('USERID',$this->session->userdata('member_id') )->update('MEMBER',array('STATUS' => '1') );
						$status = 'TRUE';
					}elseif( $SMSErrorTime > -1 ){																		//验证码错误次数+1
						$this->session->set_userdata('SMSErrorTime' , ($SMSErrorTime -1) );
					}
				}
			}
			
			// echo json_encode( array('status'=> $status ,'action' => $action ,'code' => $this->session->userdata('SMSCode') ) );
			echo json_encode( array('status'=> $status ,'action' => $action  ) );
			return;
		}else{
			show_404();
		}
	}

	public function formList()
	{
		$this->load->model('member_model');
		$data = array();
		// var_dump($this->session->all_userdata() );exit;
		$form_num = $this->db->where('FINISH','1')->where('USERID', $this->session->userdata('member_id') )->count_all_results('FORM');
		if($form_num == 0){
			$data['existForm'] = FALSE;
		}else{
			$data['existForm'] = TRUE; 

			$data['all_form_detail'] = $this->db->select('ID,CREATETIME')->where('FINISH','1')->where('USERID', $this->session->userdata('member_id') )->order_by('CREATETIME','desc')->get('FORM')->result_array();
			
		}
		
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		//面包屑导航
		$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'), array('CATNAME' => '申请记录','URL' => '/member/formlist'));
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '企业用户中心',
					'keywords'      => '表单信息 企业用户中心 苏州 智慧网贷 ',
					'description'   => '企业用户中心 查看个人信息 所有表单信息'
		);
		$this->index_model->header($meta);
    	$this->load->view('member/formlist.html',$data);
		$this->index_model->footer();
		
    }

	


}