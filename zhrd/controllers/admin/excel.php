<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Excel extends adminbase{

    public function __construct(){
        parent::__construct();
        $this->load->library('PHPExcel');
        $this->load->model('Excel_model',"excel");
        $this->load->model('apply_model',"apply");
    }

    public function index(){       
        
    }

    public function importCompany(){


    }

    // public function exportCompany(){
    //     $this->load->model('Company_model',"company");
    //     $data=$this->company->getMydata();


    // }
    
    public function adminimport()
	{
		if( $_POST){
			 $xls['url']  = $this->security->xss_clean( $this->input->post('excelPath') );
			 $xls['name'] = $this->security->xss_clean( $this->input->post('excelName') );

            $table      = 'ADMIN';
            $fieldArr   = array('USERNAME','PASSWD','GROUPID','AREAID','TEL');
            $filepath   = $_SERVER['DOCUMENT_ROOT'] . $xls['url'];
            $startRow   = '2';
			$re=$this->excel->importAdmin($table,$fieldArr,$filepath,$startRow);
			//print_r($re);exit();
        }else{
        	$data 	= array();
        	$this->load->view('admin/adminimport.html',$data);
        }
        
        
	}


    public function CompanyImport(){
        
        $this->load->library('session');

        $importuserid = $this->session->userdata('USERID');

        if($_POST){
            $excelPath=$this->input->post('excelPath');
            $excelName=$this->input->post('excelName');
            $nums=count($excelName);
            $n=$i=0;
            $msg='';
            
            for($i=0;$i<$nums;$i++){
                $xls['path']  = $excelPath[$i];
                $xls['name'] = $excelName[$i];
                $filepath   = $_SERVER['DOCUMENT_ROOT'].$xls['path'];
                $data=$this->excel->read($filepath);
                if ($data[3]['B'] !== 'FORM') {
                    msg('请上传正确格式的excel导入文件。','-1',3);
                }
                $re=$this->excel->insertExcel($data,$xls['name'],$importuserid);
                //判断返回的是数字还是字符串，数字为成功，字符串为报错
                if ((int)$re !== 0) {
                    $n = $n + $re;
                }                
                $msg  .=  ((int)$re!==0) ? '' : $re;
            }

            $referer =  $_SERVER['HTTP_REFERER'];
            
            msg('成功导入'.$n.'条！'.$msg,'-1',8);

        }else{
            $data   = array();
            $this->load->view('admin/companyImport.html',$data);
        }
    }

    public function MemberImport(){
        if($_POST){
            $excelPath=$this->input->post('excelPath');
            $excelName=$this->input->post('excelName');
            $nums=count($excelName);
            $n=$i=0;
            $msg='';
            
            for($i=0;$i<$nums;$i++){
                $xls['path']  = $excelPath[$i];
                $xls['name'] = $excelName[$i];
                $filepath   = $_SERVER['DOCUMENT_ROOT'].$xls['path'];
                $data=$this->excel->read($filepath);
                if($data[1]['A']=='用户账号'){
                    unset($data[1]);
                }
                $re=$this->excel->insertMemberExcel($data);
                //判断返回的是数字还是字符串，数字为成功，字符串为报错
                if ((int)$re !== 0) {
                    $n = $n + $re;
                }                
                $msg  .=  ((int)$re!==0) ? '' : $re;
            }

            $referer =  $_SERVER['HTTP_REFERER'];
            
            if($n==0){
                msg('用户名或执照号重复,当前没有执行导入'.$msg,'-1',8);
            }else{
                msg('成功导入'.$n.'条！'.$msg,'-1',8);
            }
            

        }else{
            $data   = array();
            $this->load->view('admin/memberImport.html',$data);
        }

    }
    

}