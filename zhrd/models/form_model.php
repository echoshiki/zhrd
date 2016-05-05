<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Form_model extends CI_Model{
	
	function __consturct(){
		parent::__construct();
		
	}
		
	public function getList($cid=''){
			$result='';
			 $query	= $this->db->order_by('ID','ASC');
			 $query	= $this->db->get_where('CONTENT',array('CATID'=>$cid));	
			 $result = $query->result_array();
			return $result;	

	}
	
	//多次申请 flag
	//2013-11-7 从新计算目前正在进行的表单申请 时间排序 修改此处 同时修改 public function reform()对应的部分
	public function formCanNew()
	{
		//每个月只能发起三次需求 COMPANY 和 LICENCE一样
		/*
		上三次 一个月  COMPANY 和 licence 一样  表单填写结束
		上三次 距离现在6个月
		*/
		$flag = false;
		$queryForm = $this->db->select('ID,COMPANY,LICENCE,STATUS,FINISH,USERID,CREATETIME')
							->where('USERID', $this->session->userdata('member_id') )->order_by('CREATETIME','desc')->limit(10)->get('FORM')->result_array();

							
		$lastFormFinish = ($queryForm[0]['FINISH'] == '1');		//前次表单填写完成
		
		$wtf_licencs = ( $queryForm[0]['LICENCE'] == $queryForm[1]['LICENCE'] ) 
					and ($queryForm[1]['LICENCE'] == $queryForm[2]['LICENCE'] );	//上三次营业执照一样
		 

		
		$wtf_y		 = ( date('Y',$queryForm[0]['CREATETIME']) == date('Y',$queryForm[1]['CREATETIME']) )
					&& ( date('Y',$queryForm[1]['CREATETIME']) == date('Y',$queryForm[2]['CREATETIME']) );	//上三次时间 年 一样

		
		$wtf_m		 = ( date('m',$queryForm[0]['CREATETIME']) == date('m',$queryForm[1]['CREATETIME']) )
					&& ( date('m',$queryForm[1]['CREATETIME']) == date('m',$queryForm[2]['CREATETIME']) );	//上三次时间 月 一样
					
		
		
		//计算大于六个月
		$now_y = date('Y',time());
		$now_m = date('m',time());
		$last_y = date( 'Y' , $queryForm[0]['CREATETIME']);
		$last_m = date( 'm' , $queryForm[0]['CREATETIME']);
		
		$wtf_6month = ( ( ($now_y - $last_y ) * 12 + $now_m -$last_m  ) < 6 );
		

// exit;
		if(
			$lastFormFinish
			&& !( $wtf_licencs && $wtf_m && $wtf_y && $wtf_6month)  //要可以多次申请 必须满足 上次填写完成 &&  不满足距离现在6个月
		)	
		{
			$flag = true;
		}
		$this->session->set_userdata('form_can_new', $flag);
		return true;
	}
	
}