<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends userBase
{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('index_model');
		$this->index_model->accessBarred();
        $this->load->model('linkage_model',"linkage"); 
        $this->load->library('form_validation');
        $this->load->model('apply_model',"apply");
		$this->load->model('member_model');
		$this->load->model('message_model');
		$this->load->model('form_model');
		
		$this->form_model->formCanNew();
		
        //定义错误信息界定符 表单错误
        $this->form_validation->set_error_delimiters('<!DOCTYPE html>
                <html lang="zh-CN">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>提示信息： </title>
                <link  href="'.base_url("statics/artDialog/skins/blue.css").'" rel="stylesheet" />
                <script src="'.base_url("statics/artDialog/artDialog.source.js").'" type="text/javascript"></script>
                </head>
                <body style="background-color:#E9EAED">
                    <script type="text/javascript">
                      $(function(){
                        art.dialog({title:"提示信息：",content:"', '",cancel:function(){window.history.go(-1)},time:2});
                });
                    </script>
                </body>
                </html>');
        // $this->session->userdata('member_status')='1';
       	// $this->session->set_userdata('member_status','1');
		if($this->session->userdata('member_status') !== '1'){
			msg('请验证您的手机号码，再发起融资申请');
		}
		$form_finish      = $this->session->userdata('form_finish');
		if( $form_finish == '1'){
			msg('您好，您已经完成所有的网上融贷申请流程，请静待申请结果,如若需要提交新的申请，请在用户中心里发起申请',site_url('member'),2 );
			return;
		} 		
    
    }
	
	private function checkForm($step = '-1')
	{

		if( $step == '-1'){ msg('请从申请表单第一步开始填写',site_url('apply')); exit; }

	}
	
	public function freshTradeid()
	{
		$this->session->unset_userdata('form_tradeid');
		redirect(site_url('apply/index'),200);
	
	}

	public function index ()
	{
		// var_dump( $this->session->all_userdata() );
		$form_type = $this->session->userdata('form_type');
		$form_id   = $this->session->userdata('form_id');
		$step      = $this->session->userdata('step');
		$form_finish      = $this->session->userdata('form_finish');
		if( $step != '-1'  )     { 
			msg('请按照表单流程填写表单', site_url('member'),3 ); 
			
			return;
		}

		if ($this->input->post('dopost') == 'form') 
		{
			$form = $this->input->post('form');
			if ($this->form_validation->run('form') == FALSE){
				// 返回验证错误的信息
				$str = validation_errors();
				echo $str;
				return;
			}

			//获取最后一级areaid
			$areaArr			=   $this->input->post('areaid');
			$areaid 			=	end_id($areaArr);
			$tradeArr           =	$this->input->post('trade');

			$tradeid			=	end_id($tradeArr);
			$tradename			=   get_area($tradeid);
			if (!empty($form['TRADE_I']['about'])) {
				$tradeid = $form['TRADE_I']['about'];
			}

			$type = $this->apply->is_type($tradeid,$form['SALE_LAST'],$form['DEMAND']);

			if ($type == '1') {
				$form['DEMAND'] = '';
				$msg = $this->apply->msg_str('end_2');
				$url = site_url();					
			}else{
				$form['TEL'] ='';
				$msg = $this->apply->msg_str('end_1');
				$url = site_url('apply/step1') . '?v=' . time() ;
			}

			//验证表单
			$this->apply->is_error($type,$form['SALE_LAST'],$form['DEMAND'],$tradeid,$areaid,$form['TEL']);
			
			$datas 	= 	array(
								'AREAID' 	=> $areaid,
								'TRADE_I'   => $tradeid,
								'COMPANY'	=> $this->session->userdata('member_company'),
								'LICENCE'	=> $this->session->userdata('member_license'),
								'SALE_LAST' => $form['SALE_LAST'],
								'DEMAND' 	=> $form['DEMAND'],
								'TYPE'		=> $type,
								'STEP'		=> '1',
								'STATUS'    => '43',
								'CREATETIME'=> time(),
								'USERID'	=> $this->session->userdata('member_id'),
						);		
			if ($type == '1') {
				$datas['TEL'] 		= $form['TEL'];
				$datas['FINISH'] 	= '1';				
			}

			//2013117如果是新加 申请的表单 重置session form_can_new = false   并 修改session form 信息 
			if( $this->session->userdata('form_can_new'))	$this->session->set_userdata( 'form_can_new' , FALSE);

			$query = $this->db->insert('FORM',$datas);
								
			if (!$query) {
				msg($this->apply->msg_str('error_db'),'-1');            
			 }
			 if($type == '1'){
				$this->session->set_userdata('form_finish','1');
				//发送站内信
				$this->message_model->createNewMessage(
												$this->session->userdata('member_id'),
												'已完成所有的表单填写',
												'尊敬的用户您好，您已完成所有的线上申请流程，请静待处理结果，我们会第一时间通知您。'
												);
			 }
			 
			$id 	= last_id('FORM','ID');
			// form_id 表单id 数据库里德
			//type  1=大企 2=中企 3=小企
			//step 表单步骤
			$sArr 	= array('form_id'=>$id,'form_type'=>$datas['TYPE'],'step'=>'1');
			$this->session->set_userdata($sArr);

			msg($msg,$url,3);
			return;
		}

		$data = array();	
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		//面包屑导航
		$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'), array('CATNAME' => '表单初筛','URL' => '/apply'));
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '第一阶段-表单初筛-智慧网贷',
					// 'keywords'      => '表单详细-企业用户登录',
					// 'description'   => ''
		);
		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
		$this->index_model->header($meta, $jscss);
		$this->load->view('apply/index.html',$data);
		$this->index_model->footer();	
	}

	public function step1()
	{

		// header('Pragma:nocache');
		// header('Cache-Control:max-age=0');
		$form_type = $this->session->userdata('form_type');
		$form_id   = $this->session->userdata('form_id');
		$step      = $this->session->userdata('step');

		$this->checkForm($step);
		if( $step != '1'){
			if($this->session->userdata('stepex') == '2ex')
				redirect( site_url('apply/stepex'),200);
			else
				msg('请按照表单流程填写表单',site_url('member'));
			return; 
		}

	
		//第一步中型企业 表单POST
		if ($this->input->post('dopost') == 'step1_mid')
		{

			if( $form_type != '2'){ msg('非法提交',site_url('member')); return; }

			if ($this->form_validation->run('form_mid_1') == FALSE){
				// 返回验证错误的信息
				$str = validation_errors();
				echo $str;
				return;
			}
		
			$form 		  = $this->input->post('form');
			
			$form_main 	= 	array(
								'PROFIT' 			=> $form['PROFIT']['1'],
								'SALE_THIS'   		=> $form['SALE_THIS'],
								'DEBT'				=> $form['DEBT']['1'],
								'ASSET'				=> $form['ASSET']['1'],
								'ACCOUNT_LAST_C' 	=> $form['ACCOUNT_C']['1'],
								'ACCOUNT_BEFORE_C' 	=> $form['ACCOUNT_C']['2'],
								'STOCK_LAST'		=> $form['STOCK']['1'],
								'STOCK_BEFORE'		=> $form['STOCK']['2'],
								'ACCOUNT_LAST_P'    => $form['ACCOUNT_P']['1'],
								'ACCOUNT_BEFORE_P'	=> $form['ACCOUNT_P']['2'],
								'COST' 				=> $form['COST']['1'],
								'FINANCE_THIS' 		=> $form['FINANCE_THIS'],
								'STEP'				=> '2',
								'ASSET_FIXED'		=> $form['ASSET_FIXED']['1'],
								'FORM'				=> 'FORM_M',								
						);

			if (!arr_isnull($form_main)) {
				msg($this->apply->msg_str('error_null'),'-1');	
			}

			//取得测算金额存入数据库main
			$needArr = $this->apply->get_exp($form_main);
			$form_main['NEED'] = $needArr[0];

			//update for main table			
			$this->db->where('ID', $form_id);
			$re = $this->db->update('FORM', $form_main); 

			if (!$re) {
				msg($this->apply->msg_str('error_db'),'-1');
			}

			//如果授信需求与测算金额相差大于200万，加入步骤参数的session
			$result	= $this->apply->get_subs($form_id,$form_main);
			if ($result > 200) {
				$this->session->set_userdata('stepex','2ex');					
			}	

			//将数组内所有数组类型元素转换成字符串
			$form 		= arr2strEx($form);
			$form['ID'] = $form_id;
			unset($form['STEP']);
			unset($form['FINANCE_THIS']);

			//Insert into attachment table (FORM_M)
			$re2 = $this->db->insert('FORM_M',$form);
			if (!$re || !$re2) {
				msg($this->apply->msg_str('error_db'),'-1');
			}

			$this->session->set_userdata( array('form_id'=>$form_id,'step'=>'2') );
			redirect(base_url('apply/step2'),200); 
			return;
		}

		//第一步小型企业 表单POST
		if ($this->input->post('dopost') == 'step1') 
		{
			if( $form_type != '3'){ msg('非法提交',site_url('member')); return; }
		
			//step_1 选择企业类型
			$tradeid    = $this->input->post('trade');
			if( !empty( $tradeid[0]) ){
				$this->session->set_userdata('form_tradeid',$tradeid[0]);
				redirect('apply/step1?v=' . time() ,200);
				return;
			}



			$form_tradeid = $this->session->userdata('form_tradeid');
			if( !$form_tradeid){
				show_404();
				return;
			}
			$tradeid[0]    	= $form_tradeid;
			$form 		  	= $this->input->post('form');



			if ($this->form_validation->run($tradeid[0]) == FALSE){
				// 返回验证错误的信息
				$str = validation_errors();
				echo $str;
				return;
			}

			$form_main 		= $this->input->post('form_main');
			$warrant		= $this->input->post('warrant'); //担保方式字段
			$warrant2   	= $this->input->post('warrant2');
			$warrant3  		= $this->input->post('warrant3');

			if (!empty($warrant['com'])) {
			//担保公司数组写入主表
				$com = implode($warrant['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}elseif (!empty($warrant2['com'])) {
				$com = implode($warrant2['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}elseif (!empty($warrant3['com'])) {
				$com = implode($warrant3['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}

			//insert into main table
			$form_main['TRADEID']	= $tradeid[0];
			$form_main['STEP']		= '2';


			//主表更新表单的附表信息
			switch ($tradeid[0]) {
				case '44':
					$form_main['FORM'] = 'FORM_A';
					break;
				case '45':
					$form_main['FORM'] = 'FORM_B';
					break;
				case '46':
					$form_main['FORM'] = 'FORM_C';
					break;
				case '47':
					$form_main['FORM'] = 'FORM_D';
					break;
				case '48':
					$form_main['FORM'] = 'FORM_T';
					break;																		
				default:
					show_404();
					break;
			}

			if ($tradeid[0] == '46') {

				//商贸回退验证
				if ($form['BRAND'] !== '2') {

					if (gettype($form['BRAND']) == 'string') {

						msg('请重新填写您的代理商名称和级别，如无填写框，请先选择<否>再选择<是>来激活填写框。','-1',3);
					}
				}
			}

			$re = $this->db->where('ID', $form_id)->update('FORM', $form_main); 

			//insert into attach table
			$form['ID'] = $form_id;

			//根据tradeid 插入不同德表单
				if ($tradeid[0] == '44') {
					$form['TALLENT']	 = array2string($form['TALLENT']);
					$form['COOPERATION'] = array2string($form['COOPERATION']);
					$form['WARRANT']	 = array2string($warrant);
					if (!empty($warrant2[0])) {
						$form['WARRANT_2'] 	= array2string($warrant2);
					}
					if (!empty($warrant3[0])) {
						$form['WARRANT_3'] 	= array2string($warrant3);
					}

					$in_tb				 = 'FORM_A';
				}
				if ($tradeid[0] == '45') {
					$form['TRADE_2']	 = $form['TRADE_2'][0];
					$form['COOPERATION'] = array2string($form['COOPERATION']);
					$form['WARRANT']	 = array2string($warrant);
					if (!empty($warrant2[0])) {
						$form['WARRANT_2'] 	= array2string($warrant2);
					}
					if (!empty($warrant3[0])) {
						$form['WARRANT_3'] 	= array2string($warrant3);
					}
					$form['COM_HONOR']	 = array2string($form['COM_HONOR']);
					$in_tb				 = 'FORM_B';
				}
				if ($tradeid[0] == '46') {
					$form['COOPERATION'] = array2string($form['COOPERATION']);
					$form['WARRANT']	 = array2string($warrant);
					if (!empty($warrant2[0])) {
						$form['WARRANT_2'] 	= array2string($warrant2);
					}
					if (!empty($warrant3[0])) {
						$form['WARRANT_3'] 	= array2string($warrant3);
					}
					$form['BRAND'] 		 = array2string($form['BRAND']);

					$in_tb				 = 'FORM_C';
				}
				if ($tradeid[0] == '47') {
					$form['COOPERATION'] = array2string($form['COOPERATION']);
					$form['WARRANT']	 = array2string($warrant);
					if (!empty($warrant2[0])) {
						$form['WARRANT_2'] 	= array2string($warrant2);
					}
					if (!empty($warrant3[0])) {
						$form['WARRANT_3'] 	= array2string($warrant3);
					}
					$in_tb				 = 'FORM_D';
				}
				if ($tradeid[0] == '48') {
					$form['COOPERATION'] = array2string($form['COOPERATION']);
					$form['WARRANT']	 = array2string($warrant);
					if (!empty($warrant2[0])) {
						$form['WARRANT_2'] 	= array2string($warrant2);
					}
					if (!empty($warrant3[0])) {
						$form['WARRANT_3'] 	= array2string($warrant3);
					}
					$in_tb				 = 'FORM_T';
				}

			$re2 = $this->db->insert($in_tb,$form);
			if (!$re || !$re2) {
				msg($this->apply->msg_str('error_db'),'-1');
			}

			if (!arr_isnull($form_main)) {
				msg($this->apply->msg_str('error_null'),'-1');	
			}

			//三农无法测算
			if ($tradeid[0] !== '47') {

				//取得测算金额存入数据库main
				$needArr = $this->apply->get_exp($form_main);
				$form_main['NEED'] = $needArr[0];
				$result	= $this->apply->get_subs($form_id,$form_main);
				//如果授信需求与测算金额相差大于200万
				if ($result > 200) {

					// header('Pragma:nocache');
					// header('Cache-Control:max-age=0');
					$this->db->where('ID', $form_id)->update('FORM',array('STEP'=>'2'));
					$this->session->set_userdata( array('step'=>'2', 'stepex' => '2ex')   );

					redirect( site_url('apply/stepex'),200);exit;
				}
			}
			

			//form finish，update main table
			$this->db->where('ID', $form_id)->update('FORM', array( 'ENDTIME'=> time(), 'FINISH' => '1','NEED'=> empty( $form_main['NEED']) ? '' : $form_main['NEED'] ) ); 
			
			$this->session->set_userdata('form_finish','1');
			// $this->session->set_userdata('form_id','-1');
			$this->session->set_userdata('type','-1');
				//发送站内信
			$this->message_model->createNewMessage(
											$this->session->userdata('member_id'),
											'已完成所有的表单填写',
											'尊敬的用户您好，您已完成所有的线上申请流程，请静待处理结果，我们会第一时间通知您。'
											);

			msg($this->apply->msg_str('end_4'), site_url('member'),5 );

			return;
		}



		//load view
		$data = array();
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		$data['breadcrumbs']  =array(array('CATNAME' => '企业用户中心(' . $this->session->userdata('member_company') . ')','URL' => '/member/'),array('CATNAME' => '表单填写','URL' => 'apply/step1') );
		
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '第二阶段-智慧网贷-详细表单',
					// 'keywords'      => '表单详细-企业用户登录',
					// 'description'   => ''
		);

		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
		if ($form_type == '3') {	//小企
			$jscss['css'][] = '/statics/js/swfupload/default.css';
			$jscss['js'][]  = '/statics/js/swfupload/swfupload.js';
			$jscss['js'][]  = '/statics/js/swfupload/handlers.js';
			$jscss['js'][]  = '/statics/js/swfupload/swfupload.queue.js';
			$jscss['js'][]  = '/statics/js/swfupload/fileprogress.js';
		}
		$this->index_model->header($meta, $jscss);
		
		if ($form_type == '3') {	//小企
			//upload image video 显示用户上传附件
			$queryattach = $this->db->select('ID,THUMB,VIDEO')->where('ID',$form_id)->get('FORM')->result_array();
			$dataTrade['attach']['thumb'] = string2array( $queryattach[0]['THUMB'] );
			$dataTrade['attach']['video'] = string2array( $queryattach[0]['VIDEO'] );
			unset($queryattach);
		
			$form_tradeid = $this->session->userdata('form_tradeid');
			if( ! empty( $form_tradeid) )
			{
				$viewHTNL = array();
				//load 不同view trade里面
				// $form_tradeid = 46;
				switch($form_tradeid){
					case 44: $viewHTNL[] = 'science';	$viewHTNL[] = '科技'; break;
					case 45: $viewHTNL[] = 'cultural';	$viewHTNL[] = '文化'; break;
					case 46: $viewHTNL[] = 'business';	$viewHTNL[] = '商贸'; break;
					case 47: $viewHTNL[] = 'farming';	$viewHTNL[] = '三农'; break;
					case 48: $viewHTNL[] = 'common';	$viewHTNL[] = '其他'; break;
					default: show_404();
				}
				$data['tradeHtmlData'] = $this->load->view('apply/trade/'.$viewHTNL[0].'.html',$dataTrade,true);
				$data['companyType'] = $viewHTNL[1];
				$data['breadcrumbs'][1]['CATNAME'] = $viewHTNL[1].'表单';
				$this->load->view('apply/trade/trade.html',$data);
			}else{
				//选择企业类型 提交会加载到session里面
				$query_company_type = $this->db->query('SELECT Z_CONTENT."ID",TITLE,THUMB,LISTORDER,"CONTENT" FROM Z_CONTENT LEFT JOIN Z_CONTENT_DATA ON Z_CONTENT."ID" = Z_CONTENT_DATA."ID" WHERE Z_CONTENT.CATID = 180 ORDER BY LISTORDER ASC')->result_array();
				foreach ($query_company_type as $key => $value) {
					$query_company_type[$key]['CONTENT'] = htmlspecialchars_decode ($query_company_type[$key]['CONTENT']->load(),ENT_QUOTES );
				}
				$data['company_type'] = $query_company_type;
				// var_dump($data['company_type']);
				$this->load->view('apply/step_1.html',$data);
			}
			
		}elseif ($form_type == '2') {	//中企
			$data['breadcrumbs'][1]['CATNAME'] = '中型企业表单';
			$this->load->view('apply/step_mid_1.html',$data);
		}else{
			msg($this->apply->msg_str('error_go'),site_url('member'),5);
		}
		$this->index_model->footer();

	}

	public function step2 ()
	{
		$form_type = $this->session->userdata('form_type');
		$form_id   = $this->session->userdata('form_id');
		$step      = $this->session->userdata('step');
		$this->checkForm($step);
		if( $step != '2')	  { msg('请按照表单流程填写表单',site_url('member')); return; }
		if( $form_type != '2'){ msg('请按照表单流程填写表单',site_url('member')); return; }
		

		if ($this->input->post('dopost') == 'step2_mid') {
			
			if ($this->form_validation->run('form_mid_2') == FALSE){
				// 返回验证错误的信息
				$str = validation_errors();
				echo $str;
				return;
			}

			//将数组内所有数组类型元素转换成字符串
			$form 		= $this->input->post('form');
			$form 		= arr2strEx($form);



			unset($form['STEP']);	
			//Insert into attachment table (FORM_M)
			$re2 = $this->db->where('ID', $form_id)->update('FORM_M',$form);
			if (!$re2) {
				msg($this->apply->msg_str('error_db'),'-1');
			}
			
			//update step
			$this->db->where('ID', $form_id)->update('FORM',array('STEP'=>'3'));
			
			$this->session->set_userdata( array('form_id'=>$form_id,'step'=>'3'));
			redirect( site_url('apply/step3') ,200);			
			return;
		}
		
		
		$data = array();	
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		if ( $this->session->userdata('form_type') != '2' ) show_404();  //msg('非法操作');
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '第二阶段-中型企业',
					// 'keywords'      => '表单详细-企业用户登录',
					// 'description'   => ''
		);
		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css','/statics/js/uploadify/uploadify.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js','/statics/js/uploadify/jquery.uploadify.min.js'),
		);
		$this->index_model->header($meta, $jscss);
		$this->load->view('apply/step_mid_2.html',$data);
		$this->index_model->footer();

	}

	public function step3 ()
	{
		$form_type = $this->session->userdata('form_type');
		$form_id   = $this->session->userdata('form_id');
		$step      = $this->session->userdata('step');
		$this->checkForm($step);

		if( $step != '3')	  { msg('请按照表单流程填写表单',site_url('member')); return; }
		if( $form_type != '2'){ msg('请按照表单流程填写表单',site_url('member')); return; }
		
		if ($this->input->post('dopost') == 'step3_mid') {

			if ($this->form_validation->run('form_mid_3') == FALSE){
				// 返回验证错误的信息
				$str = validation_errors();
				echo $str;
				return;
			}

			$form_main 	= array();
			//将数组内所有数组类型元素转换成字符串
			$form 		= $this->input->post('form');
			$warrant    = $this->input->post('warrant');

			$warrant2   = $this->input->post('warrant2');
			$warrant3   = $this->input->post('warrant3');

			if (!empty($warrant['com'])) {
			//担保公司数组写入主表
				$com = implode($warrant['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}elseif (!empty($warrant2['com'])) {
				$com = implode($warrant2['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}elseif (!empty($warrant3['com'])) {
				$com = implode($warrant3['com'],',');
				$com = ','.$com.',';
				$form_main['COM'] = $com;
			}

			$form 				= arr2strEx($form);
			$tel 				= $form['TEL'];
			$form['WARRANT'] 	= array2string($warrant);

			if (!empty($warrant2[0])) {
				$form['WARRANT_2'] 	= array2string($warrant2);
			}

			if (!empty($warrant3[0])) {
				$form['WARRANT_3'] 	= array2string($warrant3);
			}
		
			unset($form['STEP']);
			unset($form['TEL']);

			//Insert into attachment table (FORM_M)
			$re2 = $this->db->where('ID', $form_id)->update('FORM_M',$form);

			if (!$re2) {
				msg($this->apply->msg_str('error_db'),'-1');
			}

			//update step
			$form_main['STEP'] = '4';
			$form_main['TEL']  = $tel;
			$this->db->where('ID', $form_id)->update('FORM',  $form_main);
			
			$stepex 	= $this->session->userdata('stepex'); 
			if ($stepex == '2ex') {
				redirect(site_url('apply/stepex'),200);
				return;
			}
			
			//form finish，update main table
			$this->db->where('ID', $form_id)->update('FORM',  array( 'ENDTIME'=> time(), 'FINISH' => '1' )); 

			$this->session->set_userdata('form_finish','1');
			// $this->session->set_userdata('form_id','-1');
			$this->session->set_userdata('step','-1');
				//发送站内信
				$this->message_model->createNewMessage(
												$this->session->userdata('member_id'),
												'已完成所有的表单填写',
												'尊敬的用户您好，您已完成所有的线上申请流程，请静待处理结果，我们会第一时间通知您。'
												);
			msg($this->apply->msg_str('end_4'),site_url('member'),5);
			return;

		}
		
		
		$data = array();	
		//左侧信息栏目
		$data['categoryLeft'] = $this->member_model->categoryLeft();
		
		$queryattach = $this->db->select('ID,THUMB,VIDEO')->where('ID',$form_id)->get('FORM')->result_array();
		$data['attach']['thumb'] = string2array( $queryattach[0]['THUMB'] );
		$data['attach']['video'] = string2array( $queryattach[0]['VIDEO'] );
		unset($queryattach);
		
		//header.html load
		$meta = array(
					// 'contentTitle'  => $query[0]['TITLE'],
					'categoryTitle' => '第二阶段-中型企业',
					// 'keywords'      => '表单详细-企业用户登录',
					// 'description'   => ''
		);
		$jscss = array(				
			'css' => array('statics/js/validator/jquery.validator.css'),
			'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
		);
			$jscss['css'][] = '/statics/js/swfupload/default.css';
			$jscss['js'][]  = '/statics/js/swfupload/swfupload.js';
			$jscss['js'][]  = '/statics/js/swfupload/handlers.js';
			$jscss['js'][]  = '/statics/js/swfupload/swfupload.queue.js';
			$jscss['js'][]  = '/statics/js/swfupload/fileprogress.js';
		$this->index_model->header($meta, $jscss);
		$this->load->view('apply/step_mid_3.html',$data);
		$this->index_model->footer();

	}

	public function msg_1()
	{

		$this->load->view('apply/msg_1.html');
	}

	//测算过多 需求
	public function stepex()
	{
		// print_r( $this->session->all_userdata() );exit;

		$form_type = $this->session->userdata('form_type');
		$form_id   = $this->session->userdata('form_id');
		$type      = $this->session->userdata('type');
		$stepex 		= $this->session->userdata('stepex');	 	
		if (!$form_id || $stepex !== '2ex') {
			msg($this->apply->msg_str('error_go'),'-1');	
		}
		

		if ($this->input->post('dopost') == 'ex') {

				if ($this->form_validation->run('stepex') == FALSE){
						// 返回验证错误的信息
						$str = validation_errors();
						echo $str;
						return;
				}

				$form_main 			= $this->input->post('form_main');
				$form_main['PS'] 	= array2string($form_main['PS']);
				$form_main['FINISH'] = '1';
				$form_main['ENDTIME'] = time();
				$this->db->where('ID',$form_id);
				$re = $this->db->update('FORM', $form_main); 
	
				if (!$re) {
					msg($this->apply->msg_str('error_db'),'-1');
				}

				$this->session->set_userdata('form_finish','1');
				// $this->session->set_userdata('form_id','-1');
				$this->session->set_userdata('step','-1');
				$this->session->set_userdata('form_type','-1');
				msg($this->apply->msg_str('end_4'),site_url('member'),5);

		}else{

			$data = array();	
			//左侧信息栏目
			$data['categoryLeft'] = $this->member_model->categoryLeft();
			//header.html load
			$meta = array(
						// 'contentTitle'  => $query[0]['TITLE'],
						'categoryTitle' => '第二阶段-中型企业',
						// 'keywords'      => '表单详细-企业用户登录',
						// 'description'   => ''
			);
			$jscss = array(				
				'css' => array('statics/js/validator/jquery.validator.css'),
				'js' => array('statics/js/validator/jquery.validator.js','statics/js/validator/local/zh_CN.js'),
			);
			$this->index_model->header($meta, $jscss);
	    	$this->load->view('apply/step_1_1.html',$data);
			$this->index_model->footer();
		}

	}

	public function inputview()
	{
			$id = $this->uri->segment(4,0);
			$view = $this->apply->get_view($id).'.html';
			$this->load->view('/apply/trade/'.$view);		
	
	}

	public function ts()
	{

		$this->load->view('apply/step_1_1.html',$data);
	
	}

}    