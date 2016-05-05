<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends userBase{

    public function __construct()
    {
        parent::__construct();
		$this->load->model('upload_model');
		$this->load->helper('file');
    }
		
	public function index()
	{
	// echo $_COOKIE['zhrd_session'];
		// print_r($_POST);
		// print_r($_FILES);exit;
		$_FILES['Filedata']['name'] = str_replace( array("'",'"','/','\\'),'',$_FILES['Filedata']['name'] );
		$file_ext = end( explode( '.', $_FILES['Filedata']['name']) );
		switch($file_ext){
			case 'jpg':
			case 'gif':
			case 'png':
			case 'bmp': $this->imgup(); break;
			case 'flv':
			case 'mp4':
			case 'rmvb': $this->videoup();break;
		}
	}
		
    public function imgup()
    {
// $this->db->where('ID',$form_id)->update('FORM', array('THUMB' => ''));exit;
		if($_POST )
        {
			$form_id = $this->session->userdata('form_id');
			if( $form_id == -1){ show_404();return; }
			//获取DB附件列表
			$query = $this->db->select('ID,CREATETIME,THUMB')->where('ID',$form_id )->get('FORM')->result_array();
			$thumb = $query[0]['THUMB'] ? string2array( $query[0]['THUMB']) : array( );
			unset($query[0]['THUMB'] );

			if( count($thumb) >=5 ) { show_404(); return; }
			$uploadStatus =  $this->upload_model->memberImg($query[0]['CREATETIME'],$form_id);	//上传

            if( $uploadStatus){
				//插入DB
				$data = array(
					'name' => str_cut(    $uploadStatus['client_name'],100,''),
					'path' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadStatus['full_path'])
				);
				$thumb[] = $data;
				$queryUpdateATTACH = $this->db->where('ID',$form_id)->update('FORM',array('THUMB' => array2string($thumb) ));

				if($queryUpdateATTACH){
					foreach( $thumb as $key => $value){}
					echo $key;return;
				}else{	
					@unlink($uploadStatus['full_path']);//插入失败 删除文件
					show_404();
				}
			}
			else 
				show_404(); 
        }else{
            show_404();
        }

    }
	
    public function videoup()
    {
		if($_POST  )
        {
			$form_id = $this->session->userdata('form_id');
																													// $this->db->where('ID',$form_id)->update('FORM', array('THUMB' => ''));exit;
			if( $form_id == -1){ show_404();return; }
			//获取DB附件列表
			$query = $this->db->select('ID,CREATETIME,VIDEO')->where('ID',$form_id )->get('FORM')->result_array();
			$video = $query[0]['VIDEO'] ? string2array( $query[0]['VIDEO']) : array( );
			unset($query[0]['VIDEO'] );

			if( count($video) >=1 ) { show_404(); return; }
			
			$uploadStatus =  $this->upload_model->memberVideo($query[0]['CREATETIME'],$form_id);	//上传
			// var_dump($uploadStatus);
            if( $uploadStatus){
				//插入DB
				$data = array(
					'name' => str_cut($uploadStatus['client_name'],100,''),
					'path' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $uploadStatus['full_path'])
				);
				$video[0] = $data;
				$queryUpdateATTACH = $this->db->where('ID',$form_id)->update('FORM',array('VIDEO' => array2string($video) ));
				if($queryUpdateATTACH){
					echo '-1';return;
				}else{	
					@unlink($uploadStatus['full_path']);//插入失败 删除文件
					echo '1';
					show_404();
				}
			}
			else{
			// echo 'upload';
				show_404(); 
				}
        }else{
		// echo 'psot';
            show_404();
        }

    }

	public function rmattachment()
	{

		$flag = false;
		if($_POST && $this->input->is_ajax_request() )
		{
			$data = array();
			$form_id = $this->session->userdata('form_id');
			$type = $this->input->post('a');				//type 1==thumb or 2==video
			$key = $this->input->post('b');					//key
			
			if( !preg_match('~^[01]{1}$~',$type) || !preg_match('~^[0-9]+$~',$key) ) {show_404();return;}
			$query = $this->db->select('ID,THUMB,VIDEO')->where('ID',$form_id)->get('FORM')->result_array();
			if( $type == '1')
			{	//thumb
				if( !$query[0]['THUMB']) {show_404();return;}
				$data = string2array( $query[0]['THUMB']);
				if( !array_key_exists( $key, $data) ){show_404();return;}
				// echo $data[$key]['path'];exit;
				@unlink( $data[$key]['path']);
				unset( $data[$key]);
				$flag = $this->db->where('ID',$form_id)->update('FORM', array( 'THUMB' => array2string($data)) );
			}
			else
			{	//video
				if( !$query[0]['VIDEO']) {show_404();return;}
				$data = string2array( $query[0]['VIDEO']);
				@unlink( $data[0]['path'] );
				$flag = $this->db->where('ID',$form_id)->update('FORM', array( 'VIDEO' => '') );
			}
			echo '{"data":'.$flag.'}';
			
		}else
			show_404();
	}
	
}