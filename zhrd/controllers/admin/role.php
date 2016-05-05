<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Role extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Role_model',"role");
    }

    public function index(){
    	$data='';
    	$this->load->view('admin/role_list.html',$data);
    }

    public function add(){

    }

    public function edit(){
    	$data='';
        $data['groupid']    = $this->uri->segment(4,0);
        
        /*--------获取当前组权限-------*/
        $data['roleArr']=$this->role->getRolesByGroupId($data['groupid']);

        $info=$this->input->post('info');
        $dopost=$this->input->post('dopost');

        //$data['list']=$this->role->getRoleList();
    	
        /*----------------*/
        
        
        
        if($dopost==1){
            $roleArr=$info['roleid'];
            $roles=implode(',',$roleArr);
            
            $groupid=$info['groupid'];
            
            $re=$this->db->update('ADMIN_GROUP',array('ROLEIDS'=>$roles),array('GROUPID'=>$groupid));
            if($re){
                msg('修改成功！',site_url('admin/group'));
            }
        }else{
           $data['treeList']=$this->role->getTreeList(); 
        }
        $this->load->view('admin/role_edit.html',$data);
    }

    public function del(){

    }



}