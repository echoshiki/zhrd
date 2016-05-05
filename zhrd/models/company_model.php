<?php
    class Company_model extends CI_Model{
        function __consturct(){
            parent::__construct();
            $this->load->library('session');
            
    	}


    	public function search(){
    	   $area=$trade=$cType=$IUsers=$keys=$status=$parr='';
	       if($this->input->post('dopost')=='search'){
	            $area   	= $this->input->post('area');
	            $parr[0]    = '0';
	            $parr[1]	= $area   = $area[0];
	            $trade  	= $this->input->post('trade');
	            $parr[2]	= $trade  = $trade[0];
	            $parr[3]	= $cType  = $this->input->post('cType');
	            $parr[4]	= $keys   = $this->input->post('keys');
	            $status 	= $this->input->post('status');
	            $parr[5]	= $status = $status[0];
	            $IUsers 	= $this->input->post('IUsers');
	            $parr[6]	= $IUsers;
	        }

	        if($str=$this->uri->segment(4,0)){
	        	$parr		= explode('_',$str);
	        	$now_page  	= $parr[0];
	        	$area  		= $parr[1];
	        	$trade 		= $parr[2];
	        	$cType 		= $parr[3];
	        	$keys  		= $parr[4];
	        	$status		= $parr[5];
	        	$IUsers		= $parr[6];
	        }

	        switch ($cType){
	            case 1: $filed ='COMPANY'; break;
	            case 2: $filed ='LICENCE'; break;
	            case 3: $filed ='TEL'; break;
	            default : $filed='';
	        }

	        $parr[0]  = empty($parr[0]) ? '0' : $parr[0];
	        $parr[1]  = empty($parr[1]) ? '0' : $parr[1];
	        $parr[2]  = empty($parr[2]) ? '0' : $parr[2];
	        $parr[3]  = empty($parr[3]) ? '0' : $parr[3];
	        $parr[4]  = empty($parr[4]) ? '0' : $parr[4];
	        $parr[5]  = empty($parr[5]) ? '0' : $parr[5];
	        $parr[6]  = empty($parr[6]) ? '0' : $parr[6];

	        $keys   = str_replace(array('\'','"'),'',$keys);
	        $keys   = urldecode($keys);
	        $area_where   	= empty($area) ? '' : " and AREAID = '{$area}' " ;
	        $trade_where  	= empty($trade) ? '' : " and TRADEID = '{$trade}' " ; 
	        $keys_where   	= empty($keys) ? '' : " and $filed like '%{$keys}%' " ; 
	        $status_where   = empty($status) ? '' : " and STATUS  in ($status) " ;
	        $IUsers_where   = empty($IUsers) ? '' : " and IMPORTUSERID = '{$IUsers}' " ;
	        $data['where']  = $area_where.$trade_where.$IUsers_where.$status_where.$keys_where;
	        $data['parr']	=  $parr;
	        //print_r($data);exit();
	        return $data;
	    }

    	public function getMydata($typeId='',$export_where='',$per_page='15'){
	        $this->load->library('Gotopage');
	        $sqlArr 		= $this->getSql();
	        $sql_where		= empty($sqlArr['where']) ? '' : $sqlArr['where'];
	        $search 		= $this->search();
	        $search_where 	= empty($search['where']) ? '' : $search['where'];

	        $where 	= $sql_where.$search_where.$export_where;

	        $data['where'] = $search_where;
	        $parr	= $search['parr'];

	        $area  	= $parr[1];
        	$trade 	= $parr[2];
        	$ctype 	= $parr[3];
        	$keys  	= $parr[4];
        	$status	= $parr[5];
        	$IUsers = $parr[6];
			
			//print_r ($parr);

        	// var_dump($sqlArr['table']);
	        $data['list'] = $this->gotopage->page_data($sqlArr['table'],$per_page,$where,'ID DESC','*',TRUE);
	        $action=$this->uri->segment(3,0);

	        $data['page'] = $this->gotopage->page_p($sqlArr['table'],site_url('admin/company').'/'.$action,$per_page,$where,$parr);
	        $data['per_page'] = $per_page;
	        return $data;
	    }



	    public function getSql(){

	    	$udata      = $this->session->all_userdata();	       	        
	        $userid     = $udata['USERID'];
	        $groupid    = $udata['GROUPID'];

	        $typeWhere 	= $this->typeWhere();   //获取当前用户组可以访问的企业类型
	        
	        $area_where = $this->areaWhere();   //根据当前用户获取可以访问的区域

			if(!$groupid){ exit('用户没有任何组权限！'); }
			if(!in_array($groupid,array(1,3,4,5,6,7,9,10,11,12))){exit('当前用户组没有查看权限！');}

			/*
			*		$val 1:通过，2:不通过，3:需要担保
			*

.			*		43	等待审核		system	
			*		58	通过			总行审核人员操作	
			*		59	不通过			总行审核人员操作	
			*		60	需要担保		总行审核人员操作

			*		61	接受担保		担保公司操作	
			*		62	拒绝担保		担保公司操作	
			*		63	通过			客户经理操作	
			*		209	不通过			客户经理操作	
			*		210	废弃数据		system	
			*		226 支行已分配	
			*/
	        $whereArr  = array(
	                                '1' => $typeWhere,		  			//超级管理员组		=> 所有数据
	                                '3' => $typeWhere.$area_where,		//大企用户组		=> 大企数据类型
	                                '4' => $typeWhere.$area_where,		//中企用户组		=> 中企数据类型
	                                '5' => $typeWhere.$area_where,		//小企用户组		=> 小企数据类型
	                                '6' => $typeWhere.$area_where,		//个金用户组		=> 个企数据类型

	                                '7' => $typeWhere.$area_where." and	STATUS in (58,61,63,226)	",		//支行后台人员
	                                '9' => $typeWhere." and STATUS in (60) 				",						//担保公司用户,暂时不允许访问所有数据
	                                '10'=> $typeWhere.$area_where." and STATUS in (63,209,226) and ADMINID = '{$userid}' ",		//银行客户经理
	                                '11'=> $typeWhere.$area_where." and IMPORTUSERID = '{$userid}'					",		//银行合作伙伴
	                                '12'=> $typeWhere.$area_where." and 1=2 						",		//其他组别	
	                            );


	        $where      = $whereArr[$groupid];
	        //....
	        $where      = "FINISH = '1' ".$where;


	        $sql['table']	  = 'FORM';
	        $sql['where']	  = $where;
	        return $sql;
	    }



	    public function getDataVal(){
	     	$data['action']=$str=$this->uri->segment(3,0);
	        $area   = $this->input->post('area');
	        $data['area']   = $area   = $area[0];
	        $trade          = $this->input->post('trade');
	        $data['trade']   = $trade  = $trade[0];
	        $data['ctype']  = $cType  = $this->input->post('cType');
	        $data['keys']   = $keys   = $this->input->post('keys');
	        $statusArr          = $this->input->post('status');
	        $data['status']     = $status= $statusArr[0];
	        return $data;
	     }

	     public function typeWhere(){						//获取当前用户组可以访问的企业类型
	     	$udata      = $this->session->all_userdata();	       	        
	        $groupid    = $udata['GROUPID'];
	        $typeArr	= array(
			        				'1'=> "and TYPE in (1,2,3,4,5) ",       //管理员

			        				'3'=> "and TYPE in (1) ",				//大
			        				'4'=> "and TYPE in (2) ",				//中
			        				'5'=> "and TYPE in (3) ",				//小
			        				'6'=> "and TYPE in (4) or TYPE not in (1,2,3,5)",	//个金
			        				'7'=> "and TYPE in (1,2,3,4) ",				//支行后台

			        				'9'=> "and TYPE in (0) ",				//担保公司
			        				'10'=>"and TYPE in (1,2,3,4) ",				//银行客户经理
			        				'11'=>"and TYPE in (0) ",				//银行合作伙伴
			        				'12'=>"and TYPE in (0) ",				//其他
	        				   );

	        $action 		= $this->input->post('post_action') ? $this->input->post('post_action') : $this->uri->segment(3,0)  ;
	        $actionArr 		= array('comLarge','comMedium','comSmall','comPersonal','comDel');
	        $actionArrVal 	= array('comLarge'=>1,'comMedium'=>2,'comSmall'=>3,'comPersonal'=>4,'comDel'=>5);

	        if(in_array($action,$actionArr)){
	        	$type 	 = $actionArrVal[$action];
	        	$typeStr = "and TYPE = '".$type."' ";
	        	
	        	if($type==4){
	        		$typeStr = "and TYPE in (4) or TYPE not in (1,2,3,5) ";
	        	}
	        }else{
	        	$typeStr=$typeArr[$groupid];
	        }
	        return $typeStr;
	     }


	     public function areaWhere(){					//根据当前用户获取可以访问的区域
	     	$udata      = $this->session->all_userdata();	       	        
	        $areaid     = $udata['AREAID'];

	        if($areaid){
	        	$this->load->model('linkage_model',"linkage");
	        	$areaids=$this->linkage->getChildrenIds($areaid);
	        }
	        return $area_where = empty($areaids) ? ' ' : "and AREAID in(".$areaids.")";
	     }


	    public function verifltype($cid,$val){    //验证、审核
	    	$udata      = $this->session->all_userdata();	       	        
	        $userid     = $udata['USERID'];
	        $areaid     = $udata['AREAID'];
	        $groupid    = $udata['GROUPID'];
	        $role_array = array(1,3,4,5,6,7,10,11);	
	        if(!in_array($groupid,$role_array)){
	        	exit('没有权限');
	     	}

			/*
			*		$val 1:通过，2:不通过，3:需要担保
			*
			*		43	等待审核		system

			*		58	通过			总行审核人员操作	
			*		59	不通过			总行审核人员操作	
			*		60	需要担保		总行审核人员操作

			*		61	接受担保		客户操作	
			*		62	拒绝担保		客户操作

			*		63	通过			客户经理操作	
			*		209	不通过			客户经理操作	

			*		210	废弃数据		system		
			*/
	        if(in_array($val,array(43,58,59,60,61,62,63,209,210))){
	        	if($groupid==3){//大
		        	if(!in_array($val,array(58,59,60))){exit('非法操作');}
		        }


		        if($groupid==4){//中
		        	if(!in_array($val,array(58,59,60))){exit('非法操作');}
		        }


		        if($groupid==5){//小
		        	if(!in_array($val,array(58,59,60))){exit('非法操作');}
		        }


		        if($groupid==6){//个金
		        	if(!in_array($val,array(58,59,60))){exit('非法操作');}
		        }

		        if($groupid==7){//支行_一期不需要审核功能
		        	//if((int)$val==1){$status=61;}
		        	//if((int)$val==2){$status=62;}
		        }

		        if($groupid==10){//担保公司
		        	
		        }


		        if($groupid==11){//客户经理
		        	if(!in_array($val,array(63,209,210))){exit('非法操作');}
		        }

	        	$status =(int)$val;
	        }else{
	        	exit('非法操作！');
	        }
	        
	        if($cid&&$val){
	        	$ftime = time();
	        	if ($val == '59') {
	        		//审核不通过的推送给个金
	        		$re=$this->db->where('ID',$cid)->update('FORM', array('STATUS'=>$status,'TYPE'=>'4','VERIFYTIME'=>$ftime));
	        	}else{
	        		$re=$this->db->where('ID',$cid)->update('FORM', array('STATUS'=>$status,'VERIFYTIME'=>$ftime));
	        	}
	        	return $re;	
	        }else{
	        	msg('参数不正确！');
	        }
	        
	        //header("location:".$_SERVER['HTTP_REFERER']);
	    }


	    public function getType($id){

	    	$this->db->where('ID',$id);
	    	$this->db->select('TYPE');

	    	$query = $this->db->get('FORM');
	    	$arr   = $query->result_array();

	    	return $arr[0]['TYPE'];
	    }

	    public function getTrade($id){

	    	$this->db->where('ID',$id);
	    	$this->db->select('TRADEID');

	    	$query = $this->db->get('FORM');
	    	$arr   = $query->result_array();

	    	return $arr[0]['TRADEID'];
	    }

	    public function getForm($id){

	    	$this->db->where('ID',$id);
	    	$this->db->select('FORM');

	    	$query = $this->db->get('FORM');
	    	$arr   = $query->result_array();

	    	return $arr[0]['FORM'];
	    	
	    }


	    public function mainField($id){
	    	$this->db->where('ID',$id);
	    	$query_main = $this->db->get('FORM');

	    	$mainArr 	= $query_main->result_array();
	    	if (count($mainArr) == 0) {
	    		show_404();
	    	}
	    	
	    	return $mainArr;
	    }

	    public function showData($id){
			$sql = $this->getSql();
	    	//echo $sql['where'];

	    	$this->db->where('ID',$id);
	    	$query_main	= $this->db->get('FORM');

	    	$mainArr	= $query_main->result_array();
	    	if (count($mainArr) == 0) {
	    		show_404();
	    	}
			// $mainArr[0]['THUMB'] = string2array( $mainArr[0]['THUMB'] ); 
			// $mainArr[0]['VIDEO'] = string2array( $mainArr[0]['VIDEO'] ); 
			// print_r( $mainArr);exit;
			
	    	$form 		= $this->getForm($id);
	    	$arr[0]     = array();

	    	if ($form != '') {

		    	$this->db;

		    	$arr = $this->db->where('ID',$id)->get($form)->result_array();

		    	if (count($arr) == 0) {
		    		$arr[0] = array();
		    	}
	    	}

	    	if(!empty($arr[0]['SALE_THIS'])){
	    		unset($arr[0]['SALE_THIS']);
	    	}

	    	//合并主表与附表的数据
	    	$comArr = array_merge($mainArr[0],$arr[0]);
	    	return $comArr;

	    }

	    public function showData_2($id){

	    	$this->db->where('ID',$id);
	    	$query_main	= $this->db->get('FORM');

	    	$mainArr	= $query_main->result_array();
	    	if (count($mainArr) == 0) {
	    		show_404();
	    	}
			
	    	$form 		= $this->getForm($id);
	    	$arr[0]     = array();

	    	if ($form != '') {
		    	$this->db;
		    	$arr = $this->db->where('ID',$id)->get($form)->result_array();
		    	if (count($arr) == 0) {
		    		$arr[0] = array();
		    	}
	    	}

	    	if(!empty($arr[0]['SALE_THIS'])){
	    		unset($arr[0]['SALE_THIS']);
	    	}

	    	//合并主表与附表的数据
	    	$comArr = array_merge($mainArr[0],$arr[0]);
	    	return $comArr;
	    }

	   	public function getImportusers(){
	   		$this->load->model('admin');
	   		$this->db->select('IMPORTUSERID');
	   		$this->db->where('IMPORTUSERID !=','0');
	   		$this->db->group_by('IMPORTUSERID');
	   		$arrUsers = $this->db->get('FORM')->result_array();
	   		foreach ($arrUsers as $key => $value) {
	   			$info = $this->admin->userInfo_id($value['IMPORTUSERID']);
	   			$arrUsers[$key]['name'] = $info['USERNAME'] ? $info['USERNAME'] : "未知";
	   		}   		
	   		return $arrUsers;
	   	}

	   	public function getFast($export_where='',$per_page='20'){
	   		$this->load->library('Gotopage');
			$search 			= $this->search();
	  	    $search_where 		= empty($search['where']) ? '' : $search['where'];
	        $where 				= "ID != '0'".$search_where.$export_where;
	        $data['where'] 		= $search_where;
	   		$parr				= $search['parr'];
			$data['list'] 		= $this->gotopage->page_data('FAST',$per_page,$where,'ID DESC','*',TRUE);
	        $action 			= $this->uri->segment(3,0);
	        $data['page'] 		= $this->gotopage->page_p('FAST',site_url('admin/company').'/'.$action,$per_page,$where,$parr);
	        $data['per_page'] 	= $per_page;
	        return $data;
	   	}

    } 
