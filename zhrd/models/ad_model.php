<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Ad_model extends CI_Model{
		function __consturct(){
			parent::__construct();
			
		}


		function get_ad_list(){

			$query	= $this->db->get('AD');
			$result = $query->result_array();
			return $result;
		}




/*
 *	通过广告位ID来获取该ID下的广告图片
 *
 */
		function get_aditem_urls($adid,$limit=''){

			$this->db->where('ADID',$adid);
			if ($limit !== '') {
				$this->db->limit($limit+1);
			}
						
			$this->db->select('URL');
			$query = $this->db->get('AD_ITEM');
			$result = $query->result_array();
			foreach ($result as $row){
				$arr[] = $row['URL'];
			}
			return $arr;
		}



}