<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 前台 企业用户 member  Model
 *
 */
class process_model extends CI_Model{

	public function __consturct(){
		parent::__construct();
		
	}


	public function getconfig($id = '')
	{
		//数据
		$query = $this->db->select('ID,COMPANY,USERID,FINISH,DEMAND,STATUS,PIFUTIME,BAOPITIME')->where('ID',$id)->get('FORM')->result_array();
		$query = $query[0];
		// $data['form'] = $query;
		
		$wtfarr = array(
				array('name'=>'注册',	'flag' => true),  //默认注册 0
				array('name'=>'申请',	'flag' => false), 		//1
				array('name'=>'预审核',	'flag' => false),		//2
				array('name'=>'通知客户经理',	'flag' => false),		//3
				
				array('name'=>'未通过',	'flag' => false),		//2_1 4
				array('name'=>'通过',	'flag' => false),		//2_2 5
				array('name'=>'需要担保',	'flag' => false),	//2_3 6
				
				array('name'=>'已通知',	'flag' => false),		//3_1 7
				array('name'=>'未通知',	'flag' => false),		//3_2 8
				
//----------------------------------------------------------------------------------
				array('name'=>'报批',	'flag' => false,'time'=>false),		//4 9
				array('name'=>'批复',	'flag' => false,'time'=>false),		//5 10

				array('name'=>'未报批',	'flag' => false),		//4_1 11
				array('name'=>'已报批',	'flag' => false),		//4_2 12

				array('name'=>'未批复',	'flag' => false),		//5_1 13
				array('name'=>'需优化方案',	'flag' => false),	//5_2 14
				array('name'=>'已批复',	'flag' => false),		//5_3 15
				
		);
		
		//表单未填写完成
		if($query['FINISH'] != 1){
			$wtfarr[1]['flag'] = true;
			return $wtfarr;
		}else{
			//-----------------------------------申请
			$wtfarr[1]['flag'] = true;
		}

		//横着的第一行
		switch($query['STATUS']){
			//-----------------------------------批复 
			case '390': 
			case '391':
			case '392':
						$wtfarr[10]['flag'] = true;
						$wtfarr[10]['time'] = $query['PIFUTIME'];
			//-----------------------------------报批  
			case '388':
			case '389':
						$wtfarr[9]['flag'] = true;
						$wtfarr[9]['time'] = $query['BAOPITIME'];
			//-----------------------------------通知客户经理
			case '226': 
			case '63':
			case '209':
						$wtfarr[3]['flag'] = true;
			//-----------------------------------预审核
			case '43':
			case '58':
			case '59':
			case '60': 
			case '61':
			case '62': 
			case '63':
			case '209':
			case '210': 
			 			$wtfarr[2]['flag'] = true;break;
		}

		//状态第二行
		switch($query['STATUS']){
			case '59': 
			case '62': 
						$wtfarr[4]['flag'] = true;break;	//不通过
			case '60': 
						$wtfarr[6]['flag'] = true;break;	//需要担保
			case '58': 
			case '61':
						$wtfarr[5]['flag'] = true;break;	//通过
						$wtfarr[8]['flag'] = true;break;	//未通知客户经理

			case '209': 
			case '63': 
			case '226': 
						$wtfarr[7]['flag'] = true;			//已通知客户经理
						$wtfarr[5]['flag'] = true;
						break;
			case '388':
						$wtfarr[7]['flag'] = true;
						$wtfarr[5]['flag'] = true;
						$wtfarr[12]['flag'] = true;			//已经报批
						break;
			case '389':
						$wtfarr[7]['flag'] = true;
						$wtfarr[5]['flag'] = true;
						$wtfarr[11]['flag'] = true;			//未报批
						$wtfarr[13]['flag'] = true;			//未批复
						break;

			case '390':	
						$wtfarr[7]['flag'] = true;
						$wtfarr[5]['flag'] = true;
						$wtfarr[12]['flag'] = true;
						$wtfarr[15]['flag'] = true;			//已批复
						break;
			case '391':
						$wtfarr[7]['flag'] = true;
						$wtfarr[5]['flag'] = true;
						$wtfarr[12]['flag'] = true;
						$wtfarr[14]['flag'] = true;			//需要优化方案
						break;
			case '392':
						$wtfarr[8]['flag'] = true;
						$wtfarr[5]['flag'] = true;
						$wtfarr[12]['flag'] = true;
						$wtfarr[13]['flag'] = true;
						break;
					}
		//特殊 下发客户经理自动为 未报批 
		if ( in_array($query['STATUS'], array(226,63,209) ) ) {
			$wtfarr[9]['flag'] = true;
			$wtfarr[11]['flag'] = true;
		}

		return $wtfarr;
	} 


		//流程
		// $query = $this->db->select('LINKAGEID,NAME')->where('PARENTID', '42')->get('Z_LINKAGE')->result_array();
		// print_r($query);exit;
		/*
		$wtfarr = array(
			'43' => array( 'name' => '等待审核',  'flag' => false ),
			'58' => array( 'name' => '审核通过',  'flag' => false ),
			'59' => array( 'name' => '审核不通过',  'flag' => false ),
			'60' => array( 'name' => '需要担保',  'flag' => false ),
			'61' => array( 'name' => '客户接受担保',  'flag' => false ),
			'62' => array( 'name' => '客户拒绝担保',  'flag' => false ),
			'226' => array( 'name' => '支行分配完成',  'flag' => false ),
			'63' => array( 'name' => '接洽通过',  'flag' => false ),
			'209' => array( 'name' => '接洽不通过',  'flag' => false ),
			'210' => array( 'name' => '废弃数据',  'flag' => false ),
		);
		*/
		
		// var_dump($query); exit;
		
		/*
		if( $query['STATUS'] == 43){			//等待审核
			$wtfarr['43']['flag'] = true;
		}
		elseif( $query['STATUS'] == 58 ){		//审核通过
			$wtfarr['43']['flag'] = true;
			$wtfarr['58']['flag'] = true;
		}
		elseif( $query['STATUS'] == 59 ){		//审核不通过
			$wtfarr['43']['flag'] = true;
			$wtfarr['59']['flag'] = true;
			$wtfarr['210']['flag'] = true;
		}
		elseif( $query['STATUS'] == 60 ){		//需要担保
			$wtfarr['43']['flag'] = true;
			$wtfarr['60']['flag'] = true;
		}
		elseif( $query['STATUS'] == 61 ){		//客户接受担保
			$wtfarr['43']['flag'] = true;
			$wtfarr['60']['flag'] = true;
			$wtfarr['61']['flag'] = true;
		}
		elseif( $query['STATUS'] == 62 ){		//客户拒绝担保
			$wtfarr['43']['flag'] = true;
			$wtfarr['60']['flag'] = true;
			$wtfarr['62']['flag'] = true;
			$wtfarr['210']['flag'] = true;
		}
		elseif( $query['STATUS'] == 226 ){		//支行分配完成
			$wtfarr['43']['flag'] = true;
			if( $query['TIMEARRAY']){	//是否需要担保
				$wtfarr['60']['flag'] = true;
				$wtfarr['61']['flag'] = true;
			}else{
				$wtfarr['58']['flag'] = true; 
			}
			$wtfarr['226']['flag'] = true;
		}
		elseif( $query['STATUS'] == 63 ){		//支行分配完成
			$wtfarr['43']['flag'] = true;
			if( $query['TIMEARRAY']){	//是否需要担保
				$wtfarr['60']['flag'] = true;
				$wtfarr['61']['flag'] = true;
			}else{
				$wtfarr['58']['flag'] = true; 
			}
			$wtfarr['226']['flag'] = true;
			$wtfarr['63']['flag'] = true;
		}
		elseif( $query['STATUS'] == 63 ){		//接洽通过
			$wtfarr['43']['flag'] = true;
			if( $query['TIMEARRAY']){	//是否需要担保
				$wtfarr['60']['flag'] = true;
				$wtfarr['61']['flag'] = true;
			}else{
				$wtfarr['58']['flag'] = true; 
			}
			$wtfarr['226']['flag'] = true;
			$wtfarr['63']['flag'] = true;
		}
		elseif( $query['STATUS'] == 63 ){		//接洽不通过
			$wtfarr['43']['flag'] = true;
			if( $query['TIMEARRAY']){	//是否需要担保
				$wtfarr['60']['flag'] = true;
				$wtfarr['61']['flag'] = true;
			}else{
				$wtfarr['58']['flag'] = true; 
			}
			$wtfarr['226']['flag'] = true;
			$wtfarr['209']['flag'] = true;
		}
		*/
}