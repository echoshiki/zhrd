<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Company extends adminBase{

    public function __construct(){
        parent::__construct();
        $this->load->model('linkage_model',"linkage");
        $this->load->model('Company_model',"company");
        $this->load->model('apply_model',"apply");
        $this->load->model('Admin_model',"admin");
        $this->load->library('PHPExcel');
        $this->load->model('Excel_model',"excel");

        $this->load->library('form_validation');
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
    }

    public function index(){
    	// $data='';
        // $udata  = $this->session->all_userdata();
        // print_r( $udata);
		// echo '<br/>';
		// var_dump( $this->role->getRolesByUser());
		return;
    }

//注释
    public function note(){
        //post
        if($_POST && $this->input->post('dopost') == 'note'){
            $note_text = $this->input->post('note_text');
            $formid       = $this->input->post('formid');

            if( ! is_numeric($formid) ){  msg('非法提交，参数不正确',-1); }
            $note_text = str_cut( $note_text ,  '145','');

            $re = $this->db->where('ID',$formid)->update('FORM',
                            array(
                                'NOTE' => $note_text
                                )
                            );

            if( $re)
                msg('备注添加成功',site_url('/admin/company/comSmall'));
            else
                msg('备注添加失败，请重试',site_url('/admin/company/comSmall'));
            return;
        }

        //load view
        $formid = $this->uri->segment(4);
        if( ! is_numeric($formid) ){  die('非法提交，参数不正确'); }
            
        $query = $this->db->select('NOTE')->where('ID',$formid)->get('FORM')->result_array();
        $data['note_text'] = $query[0]['NOTE'];
        $data['formid'] = $formid;

        $this->load->view('admin/company_note.html',$data);
    }

//审核
    public function verify(){
        
        $dopost     = $this->input->post('dopost');
        if($dopost=='verify'){
            $cid         = $this->input->post('cid');
            $val         = $this->input->post('val');
            $memberid    = $this->input->post('memberid');
            $error      = $this->input->post('error');
            $agreeDemand = $this->input->post('agreeDemand');
            if( $agreeDemand){
                
                $query = $this->db->select('ID,DEMAND')->where('ID', $cid )->get('FORM')->result_array();
                $demand = $query[0]['DEMAND'] ;
                unset($query);
                // echo (int)$demand ;echo  (int)$agreeDemand;
                // var_dump( (int)$demand > (int)$agreeDemand);exit;
                if( (int)$demand >= (int)$agreeDemand)
                    $this->db->where('ID',$cid)->update('FORM', array('SHENHEDEMAND ' => (int)$agreeDemand ) );
                else{
                    msg('同意的授信量不得大于原授信量',-1);
                    return;
                    }
            }
            $re     =$this->company->verifltype($cid,$val);
            // if( $memberid == '1'){ //WTF
                // $referer    = $_SERVER['HTTP_REFERER'];
                // redirect($referer);
                // return;
                // }
            $this->load->model('message_model');
            $this->load->model('sms_model');
            
            if($re){
                $messageTitle = '';
                $errorString  = '';
                if($error){
                    $errorString = '由于贵公司&nbsp;'.$error.'&nbsp;的原因，';
                }
                $userIdArr = $this->db->select('ID,USERID,COMPANY,DEMAND')->where('ID',$cid)->get('FORM')->result_array();
                    switch ($val) {
                        case '58':
                            $messageTitle = '中国银行苏州分行中小企业融资申请受理书';
                            $msgStr = $userIdArr[0]['COMPANY'].'：<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;贵公司的融资申请已受理，我行客户经理将在三个工作日之内与您取得联系，感谢贵公司选择<br/>中国银行作为您的合作伙伴。中国银行苏州分行愿与您携手同行，共创美好明天！</div><div style="float:right; padding-right:5px;">中国银行苏州分行中小企业业务部</div><div style="float:right; padding-right:60px; clear:both;">'.date("Y年m月d日",time()).'</div>';
                            // $msgStr = $userIdArr[0]['COMPANY'].'：<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;经审查，拟同意为贵公司核定 '.$userIdArr[0]['DEMAND'].'万元授信总量。我行客户经理将在两个工作日之内与您取得联系，<br/>办理贷款审批和发放的各项手续。正式审批结论以我行最终出具的批复通知书为准。</div><div style="float:right;">中国银行苏州分行中小企业业务部</div><div style="float:right;clear:both">'.date("Y年m月d日",time()).'</div>';
                            $status = '3';
                            break;

                        case '59':
                            $messageTitle = '贵公司未能通过智慧网贷服务审核';
                            $msgStr = $userIdArr[0]['COMPANY'].'：<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;经审查，由于'.$errorString.'的原因，我行暂时不能受理贵公司的融资申请。</div><div style="float:right;">中国银行苏州分行中小企业业务部</div><div style="float:right;clear:both">'.date("Y年m月d日",time()).'</div>';
                            $status = '3';
                            break;

                        case '60':
                            $messageTitle = '贵公司需要担保公司担保';
                            $msgStr = $userIdArr[0]['COMPANY'].'<div style="margin-top: 10px;margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;经审查，贵公司需要由专业的担保公司提供担保，我行才能审核通过。（请到状态信息栏目里选择担保公司）</div>';
                            $status = '9';
                            break;

                        // case '63':
                            // $msgStr = '恭喜您，已经通过审核。';
                            // break;
                        // case '209':
                            // $msgStr = '很抱歉，您的企业不符合申请条件。';
                            // break;
                    }
                    $this->db->where( array('MEMBERID'=>$userIdArr[0]['USERID'], 'TYPE'=> '3') )->delete('MESSAGE');
                    $this->sms_model->send('member',
                                    array(
                                        'memberId'=>$memberid,
                                        'formId'=>$cid
                                    ));//发送短信
                    $this->message_model->createNewMessage($memberid , $messageTitle, $msgStr , $status);

                }
                $referer    = $_SERVER['HTTP_REFERER'];
                redirect($referer);

        }else{
            $data['cid']         = $this->uri->segment(4,0);
            $query = $this->db->select('ID,DEMAND')->where('ID',$data['cid'])->get('FORM')->result_array();
            // print_r($query);
            $data['DEMAND'] = $query[0]['DEMAND'];
            
            $data['memberid']    = $this->uri->segment(5,0);
            $this->load->view('admin/company_verify.html',$data);
        }
    }

//分配 
	public function allot($formid = '', $CompanyAreaId ='')	{
	
		if($_POST && $this->input->post('dopost') == 'allot'){
				$this->load->model('sms_model');
			$customerManager = $this->security->xss_clean($this->input->post('customerManager'));
			$areaid 		 = $this->security->xss_clean($this->input->post('areaid'));
			$formid			 = $this->security->xss_clean($this->input->post('formid'));
			
			if( $customerManager == '')
				msg('请选择客户经理','-1');
			if( $formid == '')
				msg('不存在的表单','-1');
			$allottime = time();
			$re = $this->db->where('ID',$formid)->update('FORM', array('ADMINID' => $customerManager , 'STATUS' => '226','ALLOTTIME' => $allottime) );
			// var_dump($customerManager);
			// var_dump($formid);
			// exit;
			$this->sms_model->send('manager',
									array(
										'managerId'=>$customerManager,
										'formId'=>$formid
									));//发送短信
			if( $re)
				msg('分配成功','-1');
			else
				msg('分配失败。请重试','-1');
	
			return;
		}

		$data = array();
		$data['beAlloted']['status'] = FALSE;
		
		if( $CompanyAreaId == '')
			msg('地区不存在','-1');
		if( $formid == '')
			msg('不存在的表单','-1');
		
		$query = $this->db->select('ID,ADMINID,STATUS')->where('ID',$formid)->get('FORM')->result_array();

		if( $query[0]['STATUS'] == '226' && !empty( $query[0]['ADMINID'] )){
			$data['beAlloted']['status'] = TRUE;
			$data['beAlloted']['CustomerManager'] = $query[0]['ADMINID'];
		}
		
	
		$data['areaid'] = $CompanyAreaId;
		$data['formid'] = $formid;
        //按照 汉语拼音排序
        $sql = " SELECT USERID,USERNAME FROM Z_ADMIN WHERE AREAID = {$CompanyAreaId} and GROUPID = 10 ORDER BY NLSSORT(USERNAME,'NLS_SORT=SCHINESE_PINYIN_M')";
        $data['customerManager'] = $this->db->query($sql)->result_array();
        // $data['customerManager'] = $this->db->select('USERID,USERNAME')->where('AREAID',$CompanyAreaId)->where('GROUPID','10')->get('ADMIN')->result_array();
		$this->load->view('admin/company_allot.html',$data);
	}

// 报批 小企业 使用 其他修改姚修改专挑链接   
    public function baopi(){
        //post
        if($_POST && $this->input->post('dopost') == 'baopi'){
            $baopi_status = $this->input->post('baopiStatus');
            $formid       = $this->input->post('formid');

            if( ! is_numeric($formid) ){  msg('非法提交，参数不正确',-1); }
            if( ! in_array($baopi_status, array(0,1)) ){  msg('请设置报批状态',-1); }

            switch ($baopi_status) {
                case '1':  //已通过
                    $baopi_status = '388';
                    break;
                
                case '0':  //未通过
                    $baopi_status = '389';
                    break;
                
            }

            $query = $this->db->select('STATUS')->where('ID', $formid)->get('FORM')->result_array();
            if ( ! in_array($query[0]['STATUS'], array('226','209','63') ) ) {
                msg('非法提交',-1);
                return;
            }

            $re = $this->db->where('ID',$formid)->update('FORM',
                            array(
                                'STATUS' => $baopi_status,
                                'BAOPITIME' => time()
                                )
                            );

            if( $re)
                msg('报批设置成功',site_url('/admin/company/comSmall'));
            else
                msg('报批设置失败。请重试',site_url('/admin/company/comSmall'));
    
            return;
        }

        //load view
        $formid = $this->uri->segment(4);
        if( ! is_numeric($formid) ){  die('非法提交，参数不正确'); }
            
        $query = $this->db->select('STATUS,BAOPITIME')->where('ID',$formid)->get('FORM')->result_array();
        if($query[0]['BAOPITIME'] !== null){
            if ($query[0]['STATUS'] == '388') {
                $baopi_status = '已通过报批';
            } else {
                $baopi_status = '未通过报批';
            }
            echo "<P>已经设置过报批状态(" . $baopi_status . ")</p>";
            return;
        }

        $data['formid'] = $formid;
        $this->load->view('admin/company_baopi.html',$data);
    }
    
//批复 小企业 使用 其他修改姚修改专挑链接
    public function pifu(){

        //post
        if($_POST && $this->input->post('dopost') == 'pifu'){

            $pifu_status = $this->input->post('pifuStatus');
            $formid       = $this->input->post('formid');

            if( ! is_numeric($formid) ){  msg('非法提交，参数不正确',-1); }
            if( ! in_array($pifu_status, array(0,1,2)) ){  msg('请设置报批状态',-1); }

            switch ($pifu_status) {
                case '1': //pass
                    $pifu_status = '390';
                    break;
                case '2': //需要优化方案
                    $pifu_status = '391';
                    break;
                case '0': //not pass
                    $pifu_status = '392';
                    break;
            }

            $pifudemand = null;
            if ($pifu_status == '390') {
                $pifudemand = $this->input->post('pifudemand');
                if(! ( is_numeric($pifudemand) && $pifudemand >= 0 ) ){
                    msg('请输入正确的金额数',-1);
                    return;
                }
            }


            $query = $this->db->select('STATUS')->where('ID',$formid)->get('FORM')->result_array();
            if (!($query[0]['STATUS'] == '388' || $query[0]['STATUS'] == '391') ) {
                msg('请按照流程审核申请流程',-1);
                return;
            }

            $re = $this->db->where('ID',$formid)->update('FORM',
                array(
                    'STATUS' => $pifu_status,
                    'PIFUTIME' => time(),
                    'PIFUDEMAND' => $pifudemand
                    )
                );

            if($re){
                msg('批复设置成功',site_url('/admin/company/comSmall'));
            }else{
                msg('报批设置失败。请重试',site_url('/admin/company/comSmall'));
            }
            return;
        }

        //load view
        $data = array();
        $formid = $this->uri->segment(4);
        if( ! is_numeric($formid) ){  die('非法提交，参数不正确'); }
        $query = $this->db->select('STATUS,BAOPITIME,PIFUTIME')->where('ID',$formid)->get('FORM')->result_array();

        //兼容以前写的view页面
        if ($query[0]['STATUS'] == '390') {
            $data['status']['PIFU'] = '1';
        }
        if ($query[0]['STATUS'] == '391') {
            $data['status']['PIFU'] = '2';
        }
        if ($query[0]['STATUS'] == '392') {
            $data['status']['PIFU'] = '0';
        }

        $data['status']['PIFUTIME'] = $query[0]['PIFUTIME'];
        $data['formid'] = $formid;
        $this->load->view('admin/company_pifu.html',$data);
    }

//推送
    public function push(){
        if ($this->input->post('dopost') == 'push') {
            $id         = $this->input->post('id');
            $type       = $this->input->post('TYPE');
            $type_old   = $this->company->getType($id);
            switch ($type_old) {
                case '1':
                    $url = site_url('admin/company/comLarge');
                    break;
                case '2':
                    $url = site_url('admin/company/comMedium');
                    break;
                case '3':
                    $url = site_url('admin/company/comSmall');
                    break;
                case '4':
                    $url = site_url('admin/company/comPersonal');
                    break; 
                case '5':
                    $url = site_url('admin/company/comDel');
                    break; 
                default:
                    $url = '-1';
                    break;
            }
            $this->db->where('ID',$id);
            $re = $this->db->update('FORM',array('TYPE'=>$type,'STATUS'=>'43'));
            if(!$re){
                msg('推送失败，请检查数据库链接配置！','-1');
            }
            msg('推送成功！',$url);
        }

        $data = '';
        $id = $this->uri->segment(4,0);
        $data['ID'] = $id;
        $this->load->view('admin/company_push.html',$data);
		return;
    }

//导出
    public function export(){
        $data='';
        $dataType=$this->input->post('dataType');

        if($this->input->post('dopost')=='selected'){
            $checkboxs=$this->input->post('checkbox');
            if($checkboxs){
                $inids=implode(',',$checkboxs);
                        
                $where=" and ID in($inids) ";
                $data=$this->company->getMyData($dataType,$where);
            }else{
                msg('没有数据','-1');exit();
            }
            
        }
        if($this->input->post('dopost')=='all'){
            $where   = $this->input->post('ex_where');
            $data    = $this->company->getMyData('',$where,'1000000');
        }

        /*-----------------------数据获取完成，完善优化数据-----------------------------------*/

            //$data['list']['data']
            
        /*-----------------------开始制作excel-----------------------------------*/    

        $t[]=array('客户信息导出报表');
        $t[]=array('ID','企业名称(COMPANY)','所在地区(AREAID)','行业(TRADEID_I)','企业类型(TRADEID)','上年销售收入(SALE_LAST)','授信需求(DEMAND)','联系电话(TEL)');
        
        $ndata  =   array();
        foreach($data['list']['data'] as $k=>$v){
            $nsArr['ID']        = $v['ID'];
            $nsArr['COMPANY']   = $v['COMPANY'];
            $nsArr['AREAID']    = $this->linkage->getLinkageName($v['AREAID']);

            if ($v['TRADE_I']) {
                //判断行业是否为自行填写的行业
                $num = (int)$v['TRADE_I'];
                //如果是非数字情况下变量num为零
                if($num == 0){
                    $v['TRADE_I'] = $v['TRADE_I'];
                }else{
                    $v['TRADE_I']= get_area($v['TRADE_I']);
                }                    
            }


            // $tiArr=string2array($v['TRADE_I']);
            
            $nsArr['TRADEID_I']     = $v['TRADE_I'];
            $nsArr['TRADEID']       = $this->linkage->getLinkageName($v['TRADEID']);
            $nsArr['SALE_LAST']     = $v['SALE_LAST'];
            $nsArr['DEMAND']        = $v['DEMAND'];
            $nsArr['TEL']           = $v['TEL'];
            $ndata[]=$nsArr;
            
        }
        if(count($ndata)<=0){
           msg('没有数据','-1');exit();
        }
        $data=array_merge($t,$ndata); 
        //$fieldArr=array_keys($data[0]);
        $header='客户信息导出报表!';
        

        $this->excel->export($data,$header);
    }

//显示列表 大 中 小 个 废弃
    public function comLarge(){

        $data=$where='';
        $data                   = $this->company->getDataVal();
        $data['dataType']       = 1;
        $data['companyList']    = $this->company->getMyData();
        $data['arrIUsers']      = $this->company->getImportusers();   //导入者列表
        $this->load->view('admin/company_list.html',$data);
    }

    public function comMedium(){
        $data=$where='';
        $data                   = $this->company->getDataVal();
        $data['dataType']       = 2;
        $data['companyList']    = $this->company->getMyData();
        $data['arrIUsers']      = $this->company->getImportusers();   //导入者列表
        $this->load->view('admin/company_list.html',$data);
    }

    public function comSmall(){
        $data=$where='';
        $data                   = $this->company->getDataVal();
        $data['dataType']       = 3;
        $data['companyList']    = $this->company->getMyData();
        $data['arrIUsers']      = $this->company->getImportusers();   //导入者列表
        $this->load->view('admin/company_list.html',$data);
    }

    public function comPersonal(){
        $data=$where='';
        $data                   = $this->company->getDataVal();
        $data['dataType']       = 4;
        $data['companyList']    = $this->company->getMyData();
        $data['arrIUsers']      = $this->company->getImportusers();   //导入者列表
        $this->load->view('admin/company_list.html',$data);
    }

    public function comDel(){
        $data=$where='';
        $data                   = $this->company->getDataVal();
        $data['dataType']       = 5;
        $data['companyList']    = $this->company->getMyData();
        $data['arrIUsers']      = $this->company->getImportusers();   //导入者列表
        $this->load->view('admin/company_list.html',$data);
    }

//详细内容
    public function show(){

        $id      = $this->uri->segment(4,0);

        if ($id == '') {
            show_404();
        }
		$comData['formId'] = $id;
        //取得包含所有公式字段的数组
        $mainArr = $this->company->mainField($id);
        $mainArr = $mainArr[0];
        //获取包含各种计算结果的数组
        $mainField              = $this->apply->get_exp($mainArr);
        $comData['mainField']   = $mainField;
       
        foreach ($comData['mainField'] as $key => $value) {
            $comData['mainField'][$key] = number_format($value,2);
        }

        //通过id获取主附表数据并合并为一个数组
        $comData['list']    = $this->company->showData($id);

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
                $this->load->view('admin/show_comMedium.html',$comData);
                break;
            case 'FORM_A':
                $this->load->view('admin/show_comSmall_1.html',$comData);
                break;
            case 'FORM_B':
                $this->load->view('admin/show_comSmall_2.html',$comData);
                break;
            case 'FORM_C':
                $this->load->view('admin/show_comSmall_3.html',$comData);
                break;
            case 'FORM_D':
                $this->load->view('admin/show_comSmall_4.html',$comData);
                break;
            case 'FORM_T':
                $this->load->view('admin/show_comSmall_5.html',$comData);
                break;
            
            default:
                $this->load->view('admin/show_comLarge.html',$comData);
                break;
        }
    }

//编辑页面
    public function edit(){

        if($this->input->post('dopost') == 'edit'){

            $id         = $this->input->post('id');
            $form_main  = $this->input->post('form_main');
            $form       = $this->input->post('form');
            $formType   = $this->company->getForm($id);
            $trade      = $this->input->post('trade');


            if ($this->form_validation->run('edit_com') == FALSE){
                // 返回验证错误的信息
                $str = validation_errors();
                echo $str;
                return;
            }

            if ($trade) {
                if (end_id($trade) !== '') {
                    $form_main['TRADE_I'] = end_id($trade);
                }
            }

            if(!empty($form_main['PS'])){
                $form_main['PS'] = array2string($form_main['PS']);
            }

            if ($formType == 'FORM_M') {

                $form_main['PROFIT']            =   $form['PROFIT']['1'];
                $form_main['DEBT' ]             =   $form['DEBT' ]['1'];
                $form_main['ASSET']             =   $form['ASSET']['1'];
                $form_main['ACCOUNT_LAST_C' ]   =   $form['ACCOUNT_C']['1'];
                $form_main['ACCOUNT_BEFORE_C']  =   $form['ACCOUNT_C']['2'];
                $form_main['STOCK_LAST']        =   $form['STOCK']['1'];
                $form_main['STOCK_BEFORE']      =   $form['STOCK']['2'];
                $form_main['ACCOUNT_LAST_P']    =   $form['ACCOUNT_P']['1'];
                $form_main['ACCOUNT_BEFORE_P']  =   $form['ACCOUNT_P']['2'];
                $form_main['COST']              =   $form['COST']['1'];
                $form_main['ASSET_FIXED']       =   $form['ASSET_FIXED']['1'];
            }
              

            $warrant    = $this->input->post('warrant');

            $warrant2   = $this->input->post('warrant_2');
            $warrant3   = $this->input->post('warrant_3');

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


            $this->db->where('ID', $id);
            $re = $this->db->update('FORM',$form_main);

            if (trim($formType) !== '') {
                $form               = arr2strEx($form);   
                $form['WARRANT']    = array2string($warrant);

                if (!empty($warrant2[0])) {
                $form['WARRANT_2']  = array2string($warrant2);
                }
                if (!empty($warrant3[0])) {
                $form['WARRANT_3']  = array2string($warrant3);
                }

                $this->db->where('ID', $id);
                $re2    = $this->db->update($formType,$form);  
                if(!$re2){
                    msg('请检查数据库配置。','-1');
                }
            }
            
            if(!$re){
                msg('请检查数据库配置。','-1');
            }
            msg('修改表单成功',$_SERVER['HTTP_REFERER']);

        }

		$id  = $this->uri->segment(4,0);
        if ($id == '') {
            show_404();
        }
        //通过id获取主附表数据并合并为一个数组
        $comData['list']    = $this->company->showData($id);

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
		//附件
        $queryattach = $this->db->select('ID,THUMB,VIDEO')->where('ID',$id)->get('FORM')->result_array();
		$comData['attach']['thumb'] = string2array( $queryattach[0]['THUMB'] );
		$comData['attach']['video'] = string2array( $queryattach[0]['VIDEO'] );
		unset($queryattach);
		// print_r($comData);exit;
        //判断选取哪个模板
        switch ($form) {
            case 'FORM_M':
                $this->load->view('admin/edit_comMedium.html',$comData);
                break;
            case 'FORM_A':
                $this->load->view('admin/edit_comSmall_1.html',$comData);
                break;
            case 'FORM_B':
                $this->load->view('admin/edit_comSmall_2.html',$comData);
                break;
            case 'FORM_C':
                $this->load->view('admin/edit_comSmall_3.html',$comData);
                break;
            case 'FORM_D':
                $this->load->view('admin/edit_comSmall_4.html',$comData);
                break;
            case 'FORM_T':
                $this->load->view('admin/edit_comSmall_5.html',$comData);
                break;      
            default:
                $this->load->view('admin/edit_comLarge.html',$comData);
                break;
        }
    }

//删除
    public function del(){  
        if($_POST and $this->input->is_ajax_request() )
        {
            $this->load->helper('file');
            $this->load->model('upload_model');

            $form_id = $this->security->xss_clean($this->input->post('formid'));
            $form_id = explode(',',$form_id);
            
            $query = $this->db->select('ID,CREATETIME,FORM,THUMB,VIDEO')->where('TYPE','5')->where_in('ID',$form_id)->get('FORM')->result_array();
            
            foreach( $query as $key => $value){
                delete_files( $this->upload_model->dir_string_create_for_member($value['CREATETIME'], $value['ID']), true,1);
                if ($value['FORM']) {
                    
                    $this->db->where('ID', $value['ID'])->delete($value['FORM']);
                }
                
            }
            $query = $this->db->where_in('ID',$form_id)->delete('FORM');
            if($query){
                echo '{"code":"1"}';
            }else{
                echo '{"code":"0"}';
            }
        }else{
            show_404();
        }       
    }

//下面两个是非小型企业才有 

//客户经理反馈列表
  public function manageview($formid = ''){
        $data = array();
        if($formid == '')
            msg('不存在的表单','-1');
        $data['status'] = FALSE;
        $query = $this->db->select('ID,STATUS,MANAGERIDEA')->where('ID',$formid)->get('FORM')->result_array();
        if( $query[0]['STATUS'] == '209' || $query[0]['STATUS'] == '63' ){
            $data['status']      = TRUE;
            $data['STATUS']      = $query[0]['STATUS'];
            $data['MANAGERIDEA'] = $query[0]['MANAGERIDEA'];
        }       
        $this->load->view('admin/company_manageView.html',$data);
    }
    
//客户经理判断通过
  public function adopt($formid = '') {   
        if($_POST && $this->input->post('dopost') == 'adopt'){
            $this->load->model('message_model');
            $formid          = $this->security->xss_clean($this->input->post('formid'));
            $adoptStatus     = $this->security->xss_clean($this->input->post('adoptStatus'));
            $manageview      = $this->security->xss_clean($this->input->post('manageview'));
            
            if ($adoptStatus === '1') {
                $demand_s    = $manageview;
                $manageview  = '授信总量'.$manageview.'万元';
            }

            if( $adoptStatus == '')
                msg('请确认是否通过','-1');
            
            $adoptStatus = ($adoptStatus === '1' )? '63' : '209'; 
            $manageview = htmlspecialchars( $manageview, ENT_QUOTES);
            
            $formdata = array(
                    'MANAGERIDEA' => $manageview ,
                    'STATUS' => $adoptStatus 
            );

            if ($adoptStatus == '63') {
                $formdata['DEMAND'] = $demand_s;
            }
            $re = $this->db->where('ID',$formid)->update('FORM', $formdata);
            if( $re){
                msg('成功',$_SERVER['HTTP_REFERER']);
                }
            else
                msg('失败，请重试','-1');
        }
        
        $data = array();
        $data['beAdopted']['status'] = FALSE;
        if($formid == '')
            msg('不存在的表单','-1');
        
        $query = $this->db->select('ID,STATUS,MANAGERIDEA')->where('ID',$formid)->get('FORM')->result_array();
        if( $query[0]['STATUS'] == '209' || $query[0]['STATUS'] == '63' ){
            $data['beAdopted']['status']      = TRUE;
            $data['beAdopted']['STATUS']      = $query[0]['STATUS'];
            $data['beAdopted']['MANAGERIDEA'] = $query[0]['MANAGERIDEA'];
        }
        
        $data['formid'] = $formid;
        $this->load->view('admin/company_adopt.html',$data);
    }

/* 
 *  2014/8/12 快捷通道 shiki
 *
 */    

    public function fast(){
        //为了与搜索和分页区分，故判断参数为纯数字则为show页面参数
        if(($id = $this->uri->segment(4,0)) && (is_numeric($this->uri->segment(4,0)))){
            $comInfo = $this->db->where('ID',$id)->get('FAST')->result_array();
            $data['list'] = $comInfo[0];
            if ($data['list']['PS']) {
                $data['list']['PS'] = string2array($data['list']['PS']);
            }
            $this->load->view('admin/fast_show.html',$data);
        }else{
            $data = $this->company->getFast();
            $this->load->view('admin/fast_list.html',$data);  
        }
    }

    public function fast_del(){
        if($_POST['formid']){
            $form_id    = $this->security->xss_clean($this->input->post('formid'));
            $form_id    = explode(',',$form_id);            
            $query      = $this->db->where_in('ID',$form_id)->delete('FAST');
            if($query){
                echo '{"code":"1"}';
            }else{
                echo '{"code":"0"}';
            }
        }else{
            show_404();
        }
    }

    public function fast_export() {

        $data='';
        
        if($this->input->post('dopost')=='selected'){
            $checkboxs=$this->input->post('checkbox');
            if($checkboxs){
                $inids=implode(',',$checkboxs);
                        
                $where=" and ID in($inids) ";
                $data=$this->company->getFast($where);
            }else{
                msg('没有数据','-1');exit();
            }
            
        }
        if($this->input->post('dopost')=='all'){
            $where   = $this->input->post('ex_where');
            $data    = $this->company->getFast($where,'1000000');
        }

        /*-----------------------数据获取完成，完善优化数据-----------------------------------*/

            //$data['list']['data']
            
        /*-----------------------开始制作excel-----------------------------------*/    

        $t[]=array('快捷信息导出报表');
        $t[]=array('ID','申请人(NAME)','所在地区(AREAID)','企业类型(TRADEID)','授信需求(PRICE)','授信期限(TIME)','联系电话(TEL)','信用状况(CREDIT)');
        
        $ndata  =   array();
        foreach($data['list']['data'] as $k=>$v){
            $nsArr['ID']        = $v['ID'];
            $nsArr['NAME']      = $v['NAME'];
            $nsArr['AREAID']    = $this->linkage->getLinkageName($v['AREAID']);
            $nsArr['TRADEID']   = $this->linkage->getLinkageName($v['TRADEID']);
            $nsArr['PRICE']     = $v['PRICE'];
            $nsArr['TIME']      = $v['TIME'];
            $nsArr['TEL']       = $v['TEL'];
            $nsArr['CREDIT']    = $this->linkage->getLinkageName($v['CREDIT']);
            $ndata[]=$nsArr;
            
        }
        if(count($ndata)<=0){
           msg('没有数据','-1');exit();
        }
        $data=array_merge($t,$ndata); 
        //$fieldArr=array_keys($data[0]);
        $header='客户信息导出报表!';
        
        //$this->excel->export($data,$header);        

    }


    // public function del(){
        // $checkArr   = $this->input->post('checkbox');
        // foreach($checkArr as $k=>$v){
             // $f=$this->db->where('ID',$v)->select('FORM')->get('FORM')->result_array() ;
             // if(!empty($f[0]['FORM'])){
                // $this->db->where('ID',$v)->delete($f[0]['FORM']);
             // }
             // $this->db->where('ID',$v)->delete('FORM');
        // }
        // $refer=$_SERVER['HTTP_REFERER'];
        // redirect($refer);
    // }



}