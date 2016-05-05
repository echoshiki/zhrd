<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Link extends adminBase{

    public function __construct()
    {
        parent::__construct();
    }
	
    public function index()
    {
		$this->load->library('Gotopage');
		
		$per_page = '10';
 		$datas = $this->gotopage->page_data('LINK',$per_page,'','LINKID DESC','LINKID,NAME,URL,LOGO');
    	$data['result'] = $datas['data'];    
      	$data['total'] = $datas['total'];	
      	$data['link'] = $this->gotopage->page_p('LINK',site_url('/admin/link/index/'),$per_page); 	
      	$data['per_page'] = $per_page;
		// $this->output->cache(5/60);
		$this->load->view('admin/link_list.html',$data);	
		
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