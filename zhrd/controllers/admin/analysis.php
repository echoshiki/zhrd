<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analysis extends adminBase{

    public function __construct()
    {
        parent::__construct();
	}
	
    public function index()
    {

		$data = array();
		$startTime = '';
		$endTime = '';
		
		//$_GET
		$pie			= !empty( $_GET['pie'])			?$_GET['pie']			: ''; 
		$company		= !empty( $_GET['company'])		?$_GET['company']		: 'small';
		$start_year		= !empty( $_GET['start_year'])	?$_GET['start_year']	: date('Y',time());
		$start_month 	= !empty( $_GET['start_month'])	?$_GET['start_month']	: date('m',time());
		$end_year 		= !empty( $_GET['end_year'])	?$_GET['end_year']		: date('Y',time());
		$end_month 		= !empty( $_GET['end_month'])	?$_GET['end_month']		: date('m',time())+1;

		//12月的bug
		if($start_month == 12){
			$end_year = $start_year+1;
			$end_month = 1;
		}
		
		//数据验证
		if( 
			!preg_match('~^[0-9]*$~',$start_year) 
			|| !preg_match('~^[0-9]*$~',$start_month) 
			|| !preg_match('~^[0-9]*$~',$end_year) 
			|| !preg_match('~^[0-9]*$~',$end_month)  
		)
			show_404();

		$startTime = mktime(0,0,0, $start_month,1, $start_year);
		$endTime = mktime(0,0,0, $end_month,1, $end_year);
		
		//如果选择的结束时间小于开始时间 从新计算 更新 end_year end_month
		if($endTime <= $startTime){
			$endTime = $startTime + 2592000;
			if($start_month == 12){
				$end_year = $start_year +1;
				$end_month = '1';
			}else{
				$end_year = $start_year;
				$end_month = $end_month;
			}
		}


		$data['select']['company']		= $company;
		$data['select']['start_year']	= $start_year;
		$data['select']['start_month']	= $start_month;
		$data['select']['end_year']		= $end_year;
		$data['select']['end_month']	= $end_month;
				
		if($pie === 'pie'){			//权限弄的太复杂
			$this->_pie($data,$startTime,$endTime);
			return;
		}
		// echo $company;
		switch($company){
			case 'medium': $type = '2';break;
			case 'small' : 
			default: $type = '3';
		}
		//地区
		$query = $this->db->select('LINKAGEID,NAME')->where('PARENTID','35')->get('LINKAGE')->result_array();
		$data['x'] = $query;
		//数量
		$data['y'] = array();
		foreach( $query as $v){
				$data['y'][0][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE',$type)->where('AREAID',$v['LINKAGEID'])->count_all_results('FORM');
				$data['y'][1][]=$this->db->where('FINISH','1')->where_in('STATUS',array('58','63','209','226') )->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE',$type)->where('AREAID',$v['LINKAGEID'])->count_all_results('FORM');
				$data['y'][2][]=$this->db->where('FINISH','1')->where('STATUS','63')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE',$type)->where('AREAID',$v['LINKAGEID'])->count_all_results('FORM');

			$z = 0;$y = 0;$c = 0;
			$sql = "select SUM(DEMAND) AS S,SUM(SHENHEDEMAND) AS SS,SUM(PIFUDEMAND) AS PS,STATUS FROM Z_FORM WHERE FINISH=1 AND TYPE = {$type} AND ENDTIME >={$startTime} AND ENDTIME<={$endTime} AND AREAID = {$v['LINKAGEID']} GROUP BY STATUS";
			$demand_youhua = $this->db->query($sql)->result_array();
			foreach ($demand_youhua as  $zyc) {
				if ( ! in_array($zyc['STATUS'], array('53','62','59' ,'210') ) ) {
					$y += $zyc['SS'];
				}

				if (in_array($zyc['STATUS'], array('390') ) ) {
					$c += $zyc['PS'];
				}
				$z += $zyc['S'];
			}
			$data['y2'][0][] = $z;		//申请demand 合计
			$data['y2'][1][] = $y;		//审核通过
			$data['y2'][2][] = $c;		//批复通过
		}
		// print_r($data['y2']);
		unset($query);
		//2013-11-11 强化分析统计功能
		$tableX = array(
					'1'  => true,								//完成申请 所有的都统计
					//  ,226,63,209, 43,60,58,59,210,226
					'3'  => array(43,60      ),					//审核中
					'5'  => array(58,226 ,388,389, 390,391,392    ,209,63),			//通过 209 63是兼容以前
					'7'  => array(59,62,210  ),				//不通过
					//  ,226,63,209
					'9'  => array(389,226),							//未报批
					'11'  => array(388, 390,391,392),			//已报批
					'13' => array(390),							//通过
					'15' => array(391,392),						//不通过
		);						//表单列表
		$tableY = array();
		foreach( $data['x'] as $k => $v){		//data['x'] 地区
			$tableY = array();
			$tableY[0] = $this->db->where('STATUS', '1')->where('AREAID', $v['LINKAGEID'])->where('REGDATE >= ',$startTime)->where('REGDATE <= ',$endTime)->count_all_results('MEMBER'); //用户数
			
			//对于多次提交申请融资的客户，在统计分析时采集最近一次的申请记录。
			$query = $this->db->query('SELECT DEMAND,SHENHEDEMAND,PIFUDEMAND,STATUS FROM Z_FORM WHERE FINISH = 1 AND ID IN ( SELECT MAX (ID) FROM Z_FORM GROUP BY USERID ) AND FINISH = 1 AND AREAID = ' . $v['LINKAGEID'] . ' AND ENDTIME <= ' . $endTime . ' AND ENDTIME >= ' . $startTime . ' ORDER BY STATUS') -> result_array();
			// print_r($query);
			foreach( $tableX as $key => $value){

				$tempDemand = 0;
				$tempNum 	= 0;
				foreach( $query as $lj){
					if ($key == 1 ) {  //申请的全部统计 稍微优化下
						$tempDemand += (int)$lj['DEMAND'];
						$tempNum ++;
						continue;
					}
					if( in_array($lj['STATUS'], $value) ){	//每一项的统计 除了第一项
						if ($key == 3) {
							$tempDemand += (int)$lj['DEMAND'];
						}elseif ($key == 13) {
							$tempDemand += (int)$lj['PIFUDEMAND'];
						} else {
							$tempDemand += (int)$lj['SHENHEDEMAND'];
						}
						
						$tempNum ++;
					}
				}	
				$tableY[$key]     = $tempNum;
				$tableY[ $key+1 ] = $tempDemand ;
			}
			$data['table'][] = $tableY;
		}
		// print_r($data['table']);
		//合计总数
		$count_area = count($data['x']);		//地区数量
		for ($i=0; $i <= 16; $i++) { 			//16是tablex的数量
				$temp_sum = 0;
				for ($j=0; $j < $count_area; $j++) { 
					$temp_sum += $data['table'][$j][$i];
				}
				$data['sum'][] = $temp_sum;
		}

		// exit();
		$this->load->view('admin/analysis.html', $data);	
		
    }

	private function _pie($data,$startTime,$endTime)
	{
		//1
		$data['x'] = array(
						'500万元以下(含)',
						'500-1000万元(含)',
						'100-1500万元(含)',
						'1500-2000万元(含)',
						'2000万元以上',
		);
		$data['y'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('DEMAND <=','500')->count_all_results('FORM');
		$data['y'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('DEMAND <=','1000')->where('DEMAND >=','500')->count_all_results('FORM');
		$data['y'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('DEMAND <=','1500')->where('DEMAND >=','1000')->count_all_results('FORM');
		$data['y'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('DEMAND <=','2000')->where('DEMAND >=','1500')->count_all_results('FORM');
		$data['y'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('DEMAND >','2000')->count_all_results('FORM');
		//2
		$data['x2'] = array(
						'大型企业',
						'中型企业',
						'小型企业',
						'微型企业',
		);
		$data['y2'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','1')->count_all_results('FORM');
		$data['y2'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','2')->count_all_results('FORM');
		$data['y2'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','3')->where('SALE_LAST >=','2500')->count_all_results('FORM');
		$data['y2'][]=$this->db->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','3')->where('SALE_LAST <','2500')->count_all_results('FORM');
		//3
		$data['y3'][]=$this->db->select('DEMAND')->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','1')->get('FORM')->result_array();
		$data['y3'][]=$this->db->select('DEMAND')->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','2')->get('FORM')->result_array();
		$data['y3'][]=$this->db->select('DEMAND')->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','3')->where('SALE_LAST >=','2500')->get('FORM')->result_array();
		$data['y3'][]=$this->db->select('DEMAND')->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TYPE','3')->where('SALE_LAST <','2500')->get('FORM')->result_array();
		
		foreach( $data['y3'] as $k => $value){	//合计demand
				$tempDemand = 0;
				foreach( $value as $a => $b){
					$tempDemand += (int)$b['DEMAND'];
				}
				$data['y3'][$k]= $tempDemand;
		}
// print_r( $data['y3']);exit;
		//4
		$data['x4'] =  $this->db->select('LINKAGEID,NAME')->where('PARENTID','81')->get('LINKAGE')->result_array();
// print_r($data['x4']);exit;
		foreach( $data['x4'] as $k => $v){
			$data['y4'][]=$this->db->select('DEMAND')->where('FINISH','1')->where('ENDTIME >= ',$startTime)->where('ENDTIME <= ',$endTime)->where('TRADE_I',$v['LINKAGEID'])->get('FORM')->result_array();
		}
		foreach( $data['y4'] as $k => $value){	//合计demand
				$tempDemand = 0;
				foreach( $value as $a => $b){
					$tempDemand += (int)$b['DEMAND'];
				}
				$data['y4'][$k]= $tempDemand;
		}
// print_r($data['y4']);exit;
		$this->load->view('admin/analysis_pie.html', $data);
	}

}