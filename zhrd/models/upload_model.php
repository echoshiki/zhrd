<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: boc
 * Date: 13-7-8
 * Time: 下午4:00
 * To change this template use File | Settings | File Templates.
 */

class upload_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
	}



    public function excelUp( $inputFile = 'Filedata')
    {
		$path = $this->dir_string_create('excel');
		$this->dir_create( $path );

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'xls|xlsx';
        $config['file_name'] = md5( time().sprintf('%02d', rand(0,99)) );
        $config['max_size'] = '2048';
        $this->load->library('upload', $config);

        if ( $this->upload->do_upload( $inputFile ))
        {
			return $this->returnFileMessage( $this->upload->data() );
        }
        else
        {
            return FALSE;
        }
    }
	
	
    public function imageUp( $inputFile = 'Filedata')
    {
		$path = $this->dir_string_create('thumb');
		$this->dir_create( $path );

        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|bmp';
        $config['file_name'] = md5( time().sprintf('%02d', rand(0,99)) );
        $config['max_size'] = '1024';
        $this->load->library('upload', $config);

        if ( $this->upload->do_upload( $inputFile) )
        {
			 return $this->returnFileMessage( $this->upload->data() );
        }
        else
        {
            return FALSE;
        }
		
    }
	/**
     *
     * 去除 $_SERVER['DOCUMENT_ROOT']
     * 构造返回数组
     *
     */
	private function returnFileMessage($data)
	{
        //return  str_replace($_SERVER['DOCUMENT_ROOT'], '', $data['full_path']);
        $a =   str_replace($_SERVER['DOCUMENT_ROOT'], '', $data['full_path']);
        return str_replace('/data', '', $a);

		// $data['file_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $data['file_path']);
        // $data['full_path'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', $data['full_path']);
        // return $data;
	}
	
	//生成路径
	private function dir_string_create($type)
	{
		return $_SERVER['DOCUMENT_ROOT'] .DIRECTORY_SEPARATOR
				.'uploads'               .DIRECTORY_SEPARATOR
				.$type                   .DIRECTORY_SEPARATOR
				.date( 'Y',time() )      .DIRECTORY_SEPARATOR
				.date( 'm',time() )      .DIRECTORY_SEPARATOR
				.date( 'd',time() )      .DIRECTORY_SEPARATOR;
	}
	
	//返回 创建 真实的文件夹
	private function dir_create($path, $mode = 0755) 
	{
        if(is_dir($path)) return true;
        $path = str_replace('\\', '/', $path);
        if(substr($path, -1) != '/') $path = $path.'/';

        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for($i=0; $i<$max; $i++) {
            $cur_dir .= $temp[$i].'/';
            if (@is_dir($cur_dir)) continue;
            @mkdir($cur_dir, $mode,true);
            @chmod($cur_dir, $mode);
        }
        return is_dir($path);
    }
	
    
	//member生成路径
	public function dir_string_create_for_member($createtime,$id)
	{
		// return '/appdata/zhrd/'				
		// 		.date( 'Y',$createtime )	.DIRECTORY_SEPARATOR
		// 		.date( 'm',$createtime )	.DIRECTORY_SEPARATOR
		// 		.$id						.DIRECTORY_SEPARATOR;
				
		return $_SERVER['DOCUMENT_ROOT']	.DIRECTORY_SEPARATOR
				.'uploads'              	.DIRECTORY_SEPARATOR
				.'member'					.DIRECTORY_SEPARATOR
				.date( 'Y',$createtime )	.DIRECTORY_SEPARATOR
				.date( 'm',$createtime )	.DIRECTORY_SEPARATOR
				.$id						.DIRECTORY_SEPARATOR;
	}
	
	
    public function memberImg(  $createtime, $id,$inputFile = 'Filedata')
    {
		$path = $this->dir_string_create_for_member($createtime, $id);
		$this->dir_create( $path );
	
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|bmp';
        $config['file_name'] = md5( time().sprintf('%02d', rand(0,99)) );
        $config['max_size'] = '2048';
        $this->load->library('upload', $config);
        
        if ( $this->upload->do_upload( $inputFile ))
        {
			 return  $this->upload->data() ;
			 // print_r($this->upload->data());exit;
        }
        else
        {
            return FALSE;
        }
    }
	
    public function memberVideo( $createtime, $id,$inputFile = 'Filedata')
    {
		$path = $this->dir_string_create_for_member($createtime, $id);
		$this->dir_create( $path );
// var_dump($_FILES['Filedata']);
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'mp4|flv|rmvb';
        $config['file_name'] = md5( time().sprintf('%02d', rand(0,99)) );
        $config['max_size'] = '20480';
        $this->load->library('upload', $config);

        if ( $this->upload->do_upload( $inputFile ))
        {
			 return  $this->upload->data() ;
        }
        else
        {
		    return FALSE;
        }
    }
	
	
}