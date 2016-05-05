<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 站内信 message_model
 */
class message_model extends CI_Model{

	public function __consturct(){
		parent::__construct();
		
	}

	/**
	 * 创建新的message
	 * @param string member 用户ID
	 * @param string title
	 * @param string content 
	 * 
	 * @return boolean 
	 */
	public function createNewMessage( $member_id , $title , $content ,$type = 0)
	{	
		$data = array(
				'MEMBERID'   => $member_id,
				'TITLE'      => $title,
				'CONTENT'    => $content,
				'ISREAD'     => '0',
				'CREATETIME' => time(),
				'TYPE'		 => $type,
		);
		$query = $this->db->insert('MESSAGE', $data);
		return $query;
	}

	/**
	 * 删除message
	 * @param string member_id  用户ID
	 * @param string||array message_id 信息ID
	 * 
	 * @return boolean 
	 */
	public function delMessage( $member_id , $message_id )
	{
		if(is_array( $message_id))
			$this->db->where_in('ID',$message_id);
		else
			$this->db->where('ID', $message_id);
			
		return $this->db->where('MEMBERID', $member_id)->delete('MESSAGE');

	}

	
	/**
	 * 修改 是否已将阅读状态 0 =>1
	 * @param string member_id  用户ID
	 * @param string message_id 信息ID
	 * 
	 * @return boolean
	 */
	public function readStatus( $member_id , $message_id ){
		if(is_array( $message_id))
			$this->db->where_in('ID',$message_id);
		else
			$this->db->where('ID', $message_id);
			
		return $this->db->where('MEMBERID', $member_id)->update('MESSAGE', array('ISREAD'=>'1') );

	}

	
	/**
	 * 获取message列表
	 * @param string member 用户ID
	 * @param boolean showRead 是否需要筛选是否需要显示企业用户没有查看过的内容 all isread not read 
	 * 
	 * @return false || array 
	 */
	public function getList( $member_id , $showRead = 'all'){
		if($showRead === 'notread')
			$this->db->where('ISREAD' , '0');
		if( $showRead == 'isread')
			$this->db->where('ISREAD','1');
		
		
		$query = $this->db->select('ID,MEMBERID,TITLE,ISREAD,CREATETIME,TYPE')->where( 'MEMBERID', $member_id )->order_by('CREATETIME','desc')->get('MESSAGE')->result_array();
		if( empty($query) )
			return false;
		return $query;
	}
	
	/**
	 * 获取message内容
	 * @param string member_id  用户ID
	 * @param string message_id 信息ID
	 * 
	 * @return false || array 
	 */
	public function getContent( $member_id , $message_id ){
	
		$query = $this->db->where( array( 'ID'=>$message_id, 'MEMBERID' => $member_id ) )->get('MESSAGE')->result_array();
		
		if( empty( $query) )
			return false;
		return $query;
	}
	
	
	
	/**
	 * 获取是否有新的message
	 * @param string member 用户ID
	 * 
	 * @return false || int 
	 */
	public function findNew($member_id = false){
		if($member_id === false)
			return false;
			
		$query = $this->db->where( array('MEMBERID'=>$member_id, 'ISREAD' => '0'))->count_all_results('MESSAGE');
		return $query;
	}
	


}