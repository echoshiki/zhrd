<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends adminBase{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
	{
		if($_POST){
		//表单提交
			$info    = $this->security->xss_clean($this->input->post('info'));
			$changed = $this->security->xss_clean($this->input->post('changed'));
			if( empty($changed) ){  return ;} 

		//仅对修改过的数据update
			foreach( $changed as $k => $val ){
				if( $val != 1){
					unset( $info[$k] );
				}
			}
			if( empty($info) )   {  msg('未修改过内容','-1');} 
			foreach($info as $k =>$val){
				if( ! $this->db->where('KEY',$k)->update( 'SETTING',array( 'VALUE' => $val)) )
					msg('更新失败，请重试','-1','2');
			}
			msg('更新成功','-1');
		}else{
		//载入视图
			$data = $list = array();
			$query        = $this->db/*->select('KEY,VALUE,KEYCN,SETTYPE')*/->where('TYPE','1')->get('SETTING')->result_array();
			foreach($query as $value){
				$list[ $value['KEY'] ] = $value['VALUE'];
			}
			$data['list'] = $list;
			$this->load->view('admin/setting.html',$data);
		}
	}
	
	

	
}