<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Download extends adminBase{

    public function __construct()
    {
        parent::__construct();
    }
	
    public function index()
    {
		$local_file = '';
		$download_file = '';
		$fileName = $_GET['fp'];
		$formId   = $_GET['fi'];
		//验证
		if( !preg_match('~^[A-Za-z0-9]*$~',$fileName) || !preg_match('~^[0-9]*$~',$formId)   ) 
			show_404();
		// 获取文件
		$query = $this->db->select('ID,THUMB,VIDEO')->where('ID',$formId)->get('FORM')->result_array();
		$query[0]['THUMB'] = string2array($query[0]['THUMB']);
		$query[0]['VIDEO'] = string2array($query[0]['VIDEO']);
		
		//遍历数组 thumb video
		foreach($query[0]['THUMB'] as $v){
			if( getFileName($v['path']) == $fileName){
				$local_file = $v['path'];
				$download_file = $v['name'];
				break;
			}
		}
		if( getFileName($query[0]['VIDEO'][0]['path']) == $fileName){
			$local_file 	= $query[0]['VIDEO'][0]['path'];
			$download_file	= $query[0]['VIDEO'][0]['name'];
		}
		
		// echo $local_file; echo $download_file; exit();

		if($local_file == '' || $download_file == '')
			show_404();

		//下载		 
		if(file_exists($local_file) && is_file($local_file)) {
			// send headers
			header('Cache-control: private');
			header('Content-Type: application/octet-stream'); 
			header('Content-Length: '.filesize($local_file));
			header('Content-Disposition: filename='.$download_file);
		 
			// flush content
			flush();
			$handle = fopen($local_file, "r");
			while (!feof($handle)) {
				echo fgets($handle, 4096);
			}
			fclose($handle);
		}
		else {
			show_404();
		}
		
    }


}