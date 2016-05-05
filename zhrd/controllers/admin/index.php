<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends adminBase{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('Admin_model','admin');
    }

    public function index()
    {	
       $data['uname']       = $this->session->userdata('uname');
       $data['groupid']     = $this->session->userdata('GROUPID');
       $data['groupname']   = $this->session->userdata('GROUPNAME');
       

	   $data['areaid']=$this->session->userdata('AREAID');
       $udata=$this->session->all_userdata();
       $rpage=$this->session->userdata('rpage');
       $groupid=$udata['GROUPID'];
       $right_f='';
       if(trim($rpage)){
            $right_f=$rpage;
       }else{
        switch ($groupid){
            case 1: $right_f='admin/category';break;
            case 2: $right_f='admin/category';break;
            case 3: $right_f='admin/company/comLarge';break;
            case 4: $right_f='admin/company/comMedium';break;
            case 5: $right_f='admin/company/comSmall';break;
            case 6: $right_f='admin/company/comPersonal';break;
            case 7: $right_f='admin/company/comMedium';break;
            case 9: $right_f='admin/index/welcome/';break;
            case 10: $right_f='admin/company/comMedium';break;
            default: $right_f='admin/index/welcome/';
       }
       }
       
       //echo $groupid;
       $data['right_f']=$right_f;
      
	   $this->load->view('admin/main.html',$data);
	   
	  
    }
	
	public function left()
    {  
        $data='';
        $this->load->model('role_model','role');
        $query  = $this->db->order_by('LISTORDER','ASC');
        $query  = $this->db->where('DISABLE is NULL');
        $query  = $this->db->get_where('ADMIN_ROLE',array('ISMENU'=>'1'));
        $leftArr=$query->result_array();
        
        foreach($leftArr as $k=>$v){
            if($v['PID']==0){
                $narr[$v['ROLEID']]=$v;
                foreach($leftArr as $kk=>$vv){
                    if($vv['PID']==$v['ROLEID']){
                        $narr[$v['ROLEID']]['son'][]=$vv;
                    }
                }
            }
            
        }

        $data['roleArr']=$this->role->getRolesByUser();
        
        $data['list']=$narr;
        
        $this->load->view('admin/left.html',$data);
    }

    public function dismenu(){
        $menuid=$_GET['menuid'];
        if($menuid){
            $this->session->set_userdata('menuid', $menuid);    
        }
    }

    public function right()
    {   
        $data='';

        $this->load->view('admin/right.html',$data);
    }
    public function welcome()
    {   
        $data='';

        $this->load->view('admin/welcome.html',$data);
    }
}