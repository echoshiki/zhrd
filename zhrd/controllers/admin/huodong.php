<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Huodong extends adminBase{

    public function __construct()
    {
        parent::__construct();
    }
	
    public function index()
    {
		$this->load->library('Gotopage');
		$AID=$this->input->get('AID');
		if($str=$this->uri->segment(4,0)){

	        	$parr=explode('_',$str);
	        	//$now_page  	= $parr[0];
	        	$AID  		= $parr[1];
	        }
		
		//$parr=array('AID'=>$AID);
		$per_page = '6';
 		$datas = $this->gotopage->page_data('BAOMING',$per_page,array('AID'=>$AID),'ID DESC','ID,TRUENAME,TEL,COMPANY,SALES');
    	$data['result1'] = $datas['data'];    
      	$data['total'] = $datas['total'];	
      	$data['link'] = $this->gotopage->page_p('BAOMING',site_url('/admin/huodong/index/'),$per_page,array('AID'=>$AID),$parr); 	
      	$data['per_page'] = $per_page;
		// $this->output->cache(5/60);
		$this->load->view('admin/huodong_list.html',$data);	
		
    }


	
    public function edit()
    {
		if($_POST and $this->input->is_ajax_request() )
		{
			$code = 0;
			$data = array(
				'LINKTYPE' => '1',
				'NAME'     => $this->security->xss_clean($this->input->post('linkname')),
				'URL'      => $this->security->xss_clean($this->input->post('linkurl')),
				'LOGO'     => $this->security->xss_clean($this->input->post('linklogo')),
			);
			$linkid = $this->security->xss_clean($this->input->post('linkid'));
			if( ! preg_match( '~^[0-9]+$~',$linkid) ) show_404();
			$query = $this->db->where('LINKID',$linkid)->update('LINK',$data);
			if($query){
				$code = 1;
			}
			echo '{"code":'.$code.'}';			
		}else{
			show_404();
		}
    }

    public function add()
    {

		if($_POST and $this->input->is_ajax_request() )
		{
			$code = 0;
			$data = array(
				'LINKTYPE' => '1',
				'NAME'     => $this->security->xss_clean($this->input->post('linkname')),
				'URL'      => $this->security->xss_clean($this->input->post('linkurl')),
				'LOGO'     => $this->security->xss_clean($this->input->post('linklogo')),
				'ADDTIME'  => time()
			);
			
			
			
			$query = $this->db->insert('LINK',$data);
			if($query){
				$code = 1;
			}
			echo '{"code":'.$code.'}';
		}else{
			show_404();
		}

    }

    public function del()
    {	
		if($_POST and $this->input->is_ajax_request() )
		{
			$code = 0;
			
			$linkid = $this->security->xss_clean($this->input->post('linkid'));
			if( ! preg_match( '~^[0-9,]+$~',$linkid) ) show_404();
			$linkid = explode(',',$linkid);

			$query = $this->db->where_in('LINKID',$linkid)->delete('LINK');
			if($query){
				$code = 1;
			}

			echo '{"code":'.$code.'}';
		}else{
			show_404();
		}		

	}
}