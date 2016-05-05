<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Linkage extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Linkage_model',"linkage");
    }

    public function index(){
    	$data='';
        $data['pid']=$this->uri->segment(4,0);
        $data['list']=$this->linkage->getList($data['pid']);
    	
        $data['select']=$this->linkage->getSelect();

        $this->load->view('admin/linkage_list.html',$data);
    }

    public function add(){
        $data='';
        $info       = $this->input->post('info');
        $dopost     = $this->input->post('dopost');
        $data['pid']=$this->uri->segment(4,0);
        if($dopost==1){
            $re=$this->db->insert('LINKAGE',array( 
                                                    'NAME'          => $info['NAME'],
                                                    'PARENTID'      => $info['PARENTID'],
                                                    'DESCRIPTION'   => $info['DESCRIPTION'],
                                                )
                                    );
            if($re){
                go(site_url('/admin/linkage/index/').'/'.$info['PARENTID'].'/');
            }
        }else{
            $this->load->view('admin/linkage_add.html',$data);   
        }
    }

    public function edit(){
    	$data='';
        

        $info           = $this->input->post('info');
        $dopost         = $this->input->post('dopost');

        
        $data['LINKAGEID']  = $this->uri->segment(4,0);
        $data['pid']        = $this->uri->segment(5,0);

        if($dopost=='1'){
            $re=$this->db->update('LINKAGE',array( 
                                                    'NAME'             => $info['NAME'],
                                                    'DESCRIPTION'      => $info['DESCRIPTION'],
                                                    'DISABLE'          => $info['DISABLE'],
                                                ),
                                            array('LINKAGEID'=>$info['LINKAGEID'])
                                    );
            if($re){
                go(site_url('/admin/linkage/index/').'/'.$info['PARENTID'].'/');
            }
        }else{
            $linkage=$this->linkage->getLinkage($data['LINKAGEID']);
            
            $data['name']       =$linkage['NAME'];
            $data['description'] =$linkage['DESCRIPTION'];
            $data['DISABLE'] =$linkage['DISABLE'];

            $this->load->view('admin/linkage_edit.html',$data);    
        }
    	
    }

    public function del(){

        $data['LINKAGEID']=$this->uri->segment(4,0);
        $data['PARENTID']=$this->uri->segment(5,0);
        $query  = $this->db->get_where('LINKAGE',array('PARENTID'=>$data['LINKAGEID']));
        $result = $query->result_array();

        msg('禁止删除！','-1');exit();
        if($result){
                msg('请先删除子类!！','-1');  
        }else{
            $re=$this->db->delete('LINKAGE',array('LINKAGEID'=> $data['LINKAGEID']));
            if($re){
                go(site_url('admin/linkage/index').'/'.$data['PARENTID'].'/');                
            }
            //header("location:".site_url('admin/category'));
        }
    }

    public function getselect(){
        $pid            = $this->uri->segment(4,0);
        $selected       = $this->uri->segment(5,0);
        $selectname     = $this->uri->segment(6,0);
        $level          = $this->uri->segment(7,0);
        echo $htm=$this->linkage->getSelect($pid,$selected,$selectname,$level,1);
    }



}