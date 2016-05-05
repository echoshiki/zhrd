<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	class Gotopage {

		//page_data方法：返回数据库data
		//page_p方法：返回页码
		//$mytable : 数据表
		//$perpage : 每页显示数量 
		//$where : 查询条件
		//$url : 输出页面URL


		public function page_data($mytable,$perpage,$where='',$order='', $select = '*'){

			$CI =& get_instance();						//自定义类调用CI框架资源的实例化对象

			// $CI->db->order_by('LISTORDER','DESC');
			if ($where !== '') {
				$CI->db->where($where);					
			}			
			$querys = $CI->db->get($mytable);			//获取总量
			$num_rows = $querys->num_rows();		
			
			/*------------------------*/
			$p_str    = $CI->uri->segment(4,0);
        	$pstr_arr = explode('_',$p_str);
        	if(count($pstr_arr)>1){
				$page     = $pstr_arr[0];
        		$page = !empty($page) ? $page : 0;

        	}else{
        		$page=$CI->uri->segment(4,0);
        		$page = !empty($page) ? $page : 0;
        	}
        	
        	/*------------------------*/
			

			$page = $page+1;			//获取URL上的起始位置

			if ($where !== '') {
				$CI->db->where($where);	
			}

			if ($order !== ''){
				$CI->db->order_by($order);
			} 
			//$CI->db->order_by('LISTORDER','DESC');
			
			$CI->db->select($select);

			$CI->db->limit($perpage, $page);			//获取limit后的数据，每页显示的数据
			$query = $CI->db->get($mytable);
			$data['total'] = $num_rows;					//数据总量
			$data['data'] = $query->result_array();		//获取数据
			return $data;
	
		}


		public function page_p($mytable,$url,$perpage,$where='',$pArr=''){
			$CI =& get_instance();						//自定义类调用CI框架资源的实例化对象
			$CI->load->library('pagination');			//调用分页类

			if ($where !== '') {
				$CI->db->where($where);					
			}

			$querys = $CI->db->get($mytable);			//查询数据表
			$num_rows = $querys->num_rows();

			$config['base_url'] = $url;					//输出页面
			$config['total_rows'] = $num_rows;			//数据总量
			$config['per_page'] = $perpage; 			//每页数据量
			$config['uri_segment'] = 4;					//截取URL页码段
			$config['num_links'] = 5;  					//显示页码标签数量

			$config['last_link'] = '最后页'; 			//最后页名称
			$config['first_link'] = '首页';
			
			//$config['full_tag_open'] = '<p>';
			//$config['full_tag_close'] = '</p>';


			$CI->pagination->initialize($config);   			//载入分页配置

			return $data = $CI->pagination->create_links($pArr);		//返回页码

		}


	}




 ?>