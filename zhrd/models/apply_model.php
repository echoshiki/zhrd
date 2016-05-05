<?php
	class Apply_model extends CI_Model{
		function __consturct(){
			parent::__construct();			
		}

/*	
 *	判断企业类型
 *	tradeid,sale_last,demand
 */
		
		public function is_type($tradeid='',$sale_last='',$demand=''){
			$type = '';

			//行业筛选条件
			$mArr = array('123','124','125','126','127','128','118');
			$hArr = array('130','131');

			//销售收入筛选条件
			$mSale_max 	= '100000';
			$mSale_min 	= '10000';
			$mSale_min2 = '15000';
			$hSale_min	= '100000';

		
			//授信筛选条件
			$mDemand	= '2000';

			//开始筛选

			if ($sale_last >= $hSale_min) {
				$type = '1';
				return $type;
			}

			foreach ($hArr as $val) {
				if ($val == $tradeid) {
					$type = '1';
					return $type;
				}
			}

			foreach ($mArr as $val) {
				if ($val == $tradeid) {
					$type = '2';
					return $type;
				}
			}

			if ($tradeid == '92') {
				if ($sale_last >= $mSale_min2  && $sale_last <= $mSale_max) {
					$type = '2';
					return $type;
				}
			}else{
				if ($sale_last >= $mSale_min && $sale_last <= $mSale_max) {
					$type = '2';
					return $type;
				}
			}

			if ($demand > $mDemand) {
				$type = '2';
				return $type;
			}

			$type = '3';
			return $type;
		}	


/*	
 *	返回错误文本
 *	key
 */
		public function msg_str($t=''){

			 $msg[] 					= '';
			 $msg['end_1'] 				= "<p style='width:430px;'>恭喜您已成为中国银行融资服务的目标客户,<br />如有融资意向，请填写以下详细信息，并注意信息的真实性、完整性，以便我们更好地为您服务，谢谢！<p>";
			 $msg['end_2'] 				= "提交表单成功，我们的客户经理会尽快联系您。";
			 $msg['end_3']				= "对不起，您已超出“智慧网贷”网络融资的服务范围，请留下您的联系方式，我们将在2个工作日内与您取得联系。";
			 $msg['end_4']				= "您的融资申请已经提交成功，请耐心等待审核，我们将尽快向您反馈审核结果。";
			 $msg['error_demand']  		= "请填写您的授信需求。";
			 $msg['error_sale_last']  	= "请填写您上年度销售收入。";
			 $msg['error_area']			= "请选择您所在的地区。";
			 $msg['error_area2']		= "请选择分地区。";
			 $msg['error_trade']		= "请选择您所属于的行业。";
			 $msg['error_db']			= "数据库操作失败，请检查你的数据库配置。";
			 $msg['error_go']			= "非法操作！";
			 $msg['error_null']			= "表单公式参数不能为空。";
			 $msg['error_tel']			= "请重新选择行业或销售收入，以便填写您的联系方式。";
			 return $msg[$t];
		}


/*	
 *	返回验证错误信息
 *	type,sale_last,demand,tradeid,areaid
 */
		public function is_error($type='',$sale_last='',$demand='',$tradeid='',$areaid='',$tel){

			if ($areaid == '0') {
				$msg = $this->msg_str('error_area');
				return msg($msg,'-1',2);
			}

			if ($areaid == '35') {
				$msg = $this->msg_str('error_area2');
				return msg($msg,'-1',2);
			}

			if ($tradeid == '0') {
				$msg = $this->msg_str('error_trade');
				return msg($msg,'-1',2);
			}

			if ($type !== '1') {
				if ($sale_last == '') {
					$msg = $this->msg_str('error_sale_last');
					return msg($msg,'-1',2);
				}
				if ($demand == '') {
					echo "123";
					echo $type;
					$msg = $this->msg_str('error_demand');
					return msg($msg,'-1',2);
				}
			}

			if ($type == '1') {
				if ($tel == '') {
					$msg = $this->msg_str('error_tel');
					return msg($msg,'-1',2);					
				}
			}
		}

		public function get_view($trade=''){
			$vArr = array(	
							'0'  => 'common',
							'44' => 'science',
							'45' => 'cultural',
							'46' => 'business',
							'47' => 'farming',
							'48' => 'common');
			return $vArr[$trade];
		}

		public function get_exp($form_main){

			if ($this->check_exp($form_main,'1') == false) {
				return $arr = array('不能测算','','','','','','','','','','','');
			}else{

				//净利润率=净利润÷预计本年度销售收入×100%
				$profit 	= (int)$form_main['PROFIT'];
				$sale_this 	= (int)$form_main['SALE_THIS'];
				$p_margin 	= ($profit*100/$sale_this)."%";


				//资产负债率=企业负债总额÷资产总额×100%
				$debt 		= (int)$form_main['DEBT'];
				$asset 		= (int)$form_main['ASSET'];
				$d_margin 	= ($debt*100/$asset)."%";


				//应收账款占比=去年末应收账款余额/资产总额
				$account_last_c = (int)$form_main['ACCOUNT_LAST_C'];
				$ac_margin      = ($account_last_c*100/$asset)."%";


				//存货占比=去年末存货余额/资产总额
				$stock_last	= (int)$form_main['STOCK_LAST'];
				$s_margin	= ($stock_last*100/$asset)."%";


				//固定资产占比=固定资产/资产总额
				if (!empty($form_main['ASSET_FIXED'])) {
					$asset_fixed	= (int)$form_main['ASSET_FIXED'];
					$f_margin		= ($asset_fixed*100/$asset)."%";
				}else{
					$f_margin		= "%";
				}


				//应收账款平均余额=（去年末应收账款余额+前年末应收账款余额）÷2
				$account_before_c	= (int)$form_main['ACCOUNT_BEFORE_C'];
				$avg_ac				= ($account_last_c + $account_before_c)/2;


				//平均存货余额=（去年末存货余额+前年末存货余额）÷2
				$stock_before	= (int)$form_main['STOCK_BEFORE'];
				$avg_s			= ($stock_last + $stock_before)/2;


				//应付装款平均余额=（去年末应付账款余额+前年末应付账款余额）÷2
				$account_last_p		= (int)$form_main['ACCOUNT_LAST_P'];
				$account_before_p	= (int)$form_main['ACCOUNT_BEFORE_P'];
				$avg_ap				= ($account_last_p + $account_before_p)/2;


				//应收账款的周转天数=应收账款平均余额÷预计本年度销售收入×360
				$day_ac	= ($avg_ac/$sale_this)*360;


				//存货余额的周转天数=平均存货余额÷销售成本×360
				$cost   = (int)$form_main['COST'];
				$day_s	= ($avg_s/$cost)*360;


				//应付账款的周转天数=应付账款平均余额÷销售成本×360
				$day_ap = ($avg_ap/$cost)*360;


				//测算资金需求=预计本年度销售收入÷360×（应收账款周转天数+存货周转天数-应付账款周转天数）-预计本年度销售收入×净利润率-目前在外融资金额
				$f_this	= (int)$form_main['FINANCE_THIS'];

				$need	= ($sale_this/360)*($day_ac + $day_s - $day_ap) - $profit - $f_this;
				$arr   	= array($need,$p_margin,$d_margin,$ac_margin,$s_margin,$f_margin,$avg_ac,$avg_s,$avg_ap,$day_ac,$day_s,$day_ap);

				return $arr;

			}



		}

		public function check_exp($form_main,$type = ''){
			$field = array('PROFIT','SALE_THIS','DEBT','ASSET','ACCOUNT_LAST_C','STOCK_LAST','ASSET_FIXED','ACCOUNT_BEFORE_C','STOCK_BEFORE','ACCOUNT_LAST_P','ACCOUNT_BEFORE_P','FINANCE_THIS');

			foreach ($field as $key => $value) {
				if($form_main[$value] == '' || empty($form_main[$value])){
					if ($type == 1) {
						return false;
					}else{
						msg('涉及测算公式的某些选项不能为空','-1');
					}					
				}else{
					return true;
				}
			}
		}

		public function get_subs($id='',$form_main=''){
			$arr 	= $this->get_exp($form_main);
			$need 	= $arr[0];
			$this->db->where('ID',$id);
			$this->db->select('DEMAND');
			$re     = $this->db->get('FORM');
			$re 	= $re->result_array();
			$demand = $re[0]['DEMAND'];
			$result = (int)$demand - (int)$need;
			return $result;
		}

/*
 *	验证步骤session是否与数据库中的相一致
 *
 */
		public function is_step($id='',$step=''){
			$this->db->select('STEP');
			$this->db->where('ID',$id);
			$re = $this->db->get('FORM');
			if (!$re) {
				echo "数据库出错！";
			}

			$re 		= $re->result_array();
			if (count($re) > 0) {
				$step_tmp 	= $re['0']['STEP'];
				if ($step_tmp == $step) {
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}

		}
		
	}
?>