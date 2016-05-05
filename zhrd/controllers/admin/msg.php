<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Msg extends adminBase{

	public function __construct()
	{
	    parent::__construct();
	}

	public function Index(){
		$data  = '';
		$where = '';
		$this->load->library('Gotopage');
 		$per_page = '20';
 		$where    = array('PID'=>NULL);
 		$datas = $this->gotopage->page_data('MSG',$per_page,$where,'CREATETIME DESC');
 		$data['msg']   = $datas['data'];
		$data['total'] = $datas['total'];
		$data['per_page'] = $per_page;
		$data['link'] = $this->gotopage->page_p('MSG',site_url('/admin/msg/index/'),$per_page,$where); 

		foreach ($data['msg'] as $k => $val) {
			if($val['TYPE'] == 1){
			    $data['msg'][$k]['USERNAME'] = getVal('MEMBER','USERNAME','USERID',$data['msg'][$k]['USERID']);
			}else{
				$data['msg'][$k]['USERNAME'] = getVal('ADMIN','USERNAME','USERID',$data['msg'][$k]['USERID']);
			}
		}

		foreach ($data['msg'] as $key => $value) {
			$this->db->where('PID',$value['ID']);
			$this->db->order_by("ID", "asc"); 
			$data['msg'][$key]['child'] = $this->db->get('MSG')->result_array();
		}


		$this->load->view('admin/msg_list.html',$data);
	}

	public function show(){
		$id = $this->input->get('id');
		$this->db->where('ID',$id);
		$re = $this->db->get('MSG')->result_array();
		$data['TITLE'] 		= $re[0]['TITLE'];
		$data['CONTENT'] 	= $re[0]['CONTENT'];
		$data['TYPE']		= $re[0]['TYPE'];
		$data['ID']			= $re[0]['ID'];
		if ($data['TYPE'] == 1) {
			$data['USERNAME'] = getVal('MEMBER','USERNAME','USERID',$re[0]['USERID']);
		}else {
			$data['USERNAME'] = getVal('ADMIN','USERNAME','USERID',$re[0]['USERID']);
			$data['GROUP']	  = getVal('ADMIN_GROUP','GROUPNAME','GROUPID',$re[0]['GROUPID']);
		}
		$data['TIME']         = date('Y-m-d',$re[0]['CREATETIME']);

		$this->db->where('PID',$id);
		$child = $this->db->get('MSG')->result_array();
		$data['CHILD'] = $child;

		$this->load->view('admin/msg_show.html',$data);
	}

	public function verify(){

		$id = $this->input->get('id');
		$status = getVal('MSG','STATUS','ID',$id);
		$this->db->where('ID',$id);
		if ($status == '1') {
			$re = $this->db->update('MSG',array('STATUS'=>'0'));
			echo '0';
		}else{
			$re = $this->db->update('MSG',array('STATUS'=>'1'));
			echo '1';
		}
	}

    public function del()
    {
		$id = $this->input->get('id');
		$arr = explode(",", $id);
		foreach ($arr as $key => $val) {
			$this->db->where('ID',$val);
			$this->db->or_where('PID',$val);
			$this->db->delete('MSG');
		}
		msg("删除留言成功！",site_url('/admin/msg/index'));
    }


    public function reply(){
		$data = '';
		$data['pid'] = $this->input->get('id');
		$fid	     = getVal('MSG','PID','ID',$data['pid']);

		if ($fid !== null) {
			$data['cid'] = $data['pid'];
			$data['pid'] = $fid;
			$utype		 = getVal('MSG','TYPE','ID',$data['cid']);
			if ($utype == '1') {
				$uid 		   = getVal('MSG','USERID','ID',$data['cid']);
				$data['uname'] = getVal('MEMBER','USERNAME','USERID',$uid);
			}else{
				$uid 		   = getVal('MSG','USERID','ID',$data['cid']);
				$data['uname'] = getVal('ADMIN','USERNAME','USERID',$uid);
			}
			$data['mcon']      = '回复'.$data['uname'].': ';
		}

		if ($this->input->post('doreply') == '1') {
			$reply = $this->input->post('reply');
			$reply['CREATETIME']		= time();
			$reply['STATUS'] 			= 1;		
			if ($this->session->userdata('USERID')) {
				$reply['USERID'] 	= $this->session->userdata('USERID');
				$reply['TYPE']   	= 2;
				$reply['GROUPID']   = $this->session->userdata('GROUPID');
			}else if($this->session->userdata('member_id')){
				$reply['USERID'] 	= $this->session->userdata('member_id');
				$reply['TYPE']		= 1;				
			}else{
				msg('请登录后再留言。','-1');
			}

			unset($reply['CID']);

			$re = $this->db->insert('MSG', $reply); 

			if ($re) {
				msg('留言成功！',$_SERVER['HTTP_REFERER']);
			}
		}


    	$this->load->view('admin/msg_reply.html',$data);
    }
		

}

?>