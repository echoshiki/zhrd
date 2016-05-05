<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Category_model',"cate");
		$this->load->model('Content_model',"content");
    }

    public function Index()
    {	
		$data = array();
		$this->load->model('index_model');
		$data['tree']=$this->cate->getList();
		$data['tree'] = $this->index_model->getCategoryUrl($data['tree']);
		//print_r ($data);
		
		$this->load->view('admin/category_list.html',$data);
    }

    public function add()
    {
		if( $_POST  && $this->input->post('dopost') ==1)
		{
			$info=$this->input->post('info');
			$info['ISBANNER'] = ( isset($info['ISBANNER']) ) ? 1:0;

			// foreach( $info as $v){
			// 	if( $v == '')
			// 	msg('栏目名必填','-1');
			// }

			if ($info['CATNAME'] == '') {
				msg('栏目名必填','-1');
			}

			$re=$this->db->insert('CATEGORY',array( 'CATNAME'  => str_cut( $info['CATNAME'] , '30',''),
												    'PARENTID' => $info['PARENTID'],
													'TYPE'     => $info['TYPE'],
													'LISTORDER'=> 0,
													'ISBANNER' => $info['ISBANNER'],
													'THUMB'    => $info['THUMB']
													)
								);
		    if ($info['TYPE'] == 0) {
		    	$cid_page = last_id('CATEGORY','CATID');
		    	$re2 = $this->db->insert('PAGE',array( 'CATID'  => $cid_page,
				));
		    }

			if($re){
				redirect( site_url('/admin/category/'), 200);
			}else{
				// msg('添加失败，请重试','-1');
			}

		}else{
			$data=array();
			$data['tree']=$this->cate->getList();
			if($cid = $this->uri->segment(4,0)){
				$data['pid']=$cid;
				$cats=$this->cate->category();
				foreach($cats as $k=>$v){
					if($v['CATID']==$cid){
						$data['pname']=	$v['CATNAME'];
					}
				}			
			}
			$this->load->view('admin/category_add.html',$data);
			
		}
		
    }

    public function edit()
    {	
		if( $_POST && $this->input->post('dopost') == '1'){

			$info			= $this->security->xss_clean( $this->input->post('info') );

			$info['ISBANNER'] = ( isset($info['ISBANNER']) ) ? 1:0;
			// foreach( $info as $v){
				// if( $v == '')
					// msg('提交错误','-1');
			// }
			
			if ($info['CATNAME'] == '') {
				msg('栏目名必填','-1');
			}


			$re=$this->db->update('CATEGORY',array( 
													'PARENTID'	=> $info['PARENTID'],
													'CATNAME'	=> str_cut( $info['CATNAME'] , '30',''),
													'TYPE'		=> $info['TYPE'],
													'ISBANNER' => $info['ISBANNER'],
													'THUMB'		=> $info['THUMB']
												),
											array('CATID'=>$info['CATID'])
							 		);


			if($re){
				redirect(site_url('/admin/category/'),200);
			}else{
				msg('修改失败，请重试','-1');
			}
			
		}else{
		
			$data			= array();	
			$data['cid']	= $this->uri->segment(4,0);
			$data['tree']	= $this->cate->getList();
			$cids=$this->cate->getAllChild($data['cid']);

			$data['pid_edit']=$this->uri->segment(5,0);
			$data['child']=$this->cate->getAllChild($data['cid']);
			$this->cate->getAllChild();
			$cid 		= $this->uri->segment(4,0);
			$query		= $this->db->get_where('CATEGORY',array('CATID'=>$cid));
			$result = $query->result_array();
			$data['info']= $result['0'];
			// print_r($data);return;
			$this->load->view('admin/category_edit.html',$data);
		}
    }


	//page 单页修改
    public function set(){
		if( $_POST && $this->input->post('dopost') == 'edit'){
			$info			= $this->security->xss_clean( $this->input->post('info') );
			// var_dump( $info['TITLE']);exit;
			if( empty($info['TITLE'])  ){
				msg('标题不能为空',-1);
			}
			$arr = array(

						'TITLE'			=> $info['TITLE'],
						'KEYWORDS'		=> $info['KEYWORDS'],
						'THUMB'			=> $info['THUMB'],
						'CONTENT' 		=> '', 
						'DESCRIPTION' 	=> $info['DESCRIPTION'],
						'UPDATETIME'	=> time(),
            );
// var_dump($arr['CONTENT']);
			$re = $this->db->where('CATID',$info['CATID'])->update('PAGE',$arr);
			
			$content_data  = $this->content->encode_html($info['CONTENT']); 
            $re2 	  = $this->content->updatePage($content_data,$info['CATID']);

			if($re){msg('更新成功！',site_url('admin/category/set').'/'.$info['CATID'].'/'.$info['CATNAME']);}

		}else{
			$data			= 	array();	
			$data['cid']	= 	$this->uri->segment(4,0);
			$data['tree']	= 	$this->cate->getList();
			$catname        =	urldecode($this->uri->segment(5,0));
			
			$id 	= 	$data['cid'];
			$arr 	= 	$this->db->where('CATID',$id)->get('PAGE') ->result_array();
			//获取ora数据库clob类型的值
			$obj 		= $arr[0]['CONTENT'];
			$content = '';
			if (!empty($obj)) {
				$content 	= $obj->load();
			}
			

			$data['CATID'] 			= $arr[0]['CATID'];
			$data['TITLE'] 			= $arr[0]['TITLE'];
			$data['THUMB'] 			= $arr[0]['THUMB'];
			$data['KEYWORDS'] 		= $arr[0]['KEYWORDS'];
			$data['DESCRIPTION'] 	= $arr[0]['DESCRIPTION'];
			$data['CONTENT'] 		= htmlspecialchars_decode( $content, ENT_QUOTES);
			$data['CATNAME']		= $catname;
// var_dump($data['CONTENT']);

			$this->load->view('admin/category_set.html',$data);

		}
    }



	
    public function del()
    {	
		$data='';
		$cid=$this->uri->segment(4,0);
		//$this->db->get('CATEGORY');
		$query	= $this->db->get_where('CATEGORY',array('PARENTID'=>$cid));
		$result = $query->result_array();
		if($result){
			msg('请先删除子栏目!！','-1');	
		}elseif($this->db->select('ID')->where_in('CATID',$cid)->count_all_results('CONTENT') != 0){
			msg('请先进入内容管理删除相关内容！','-1');	
		}else{
		
			$re=$this->db->delete('CATEGORY',array('CATID'=>$cid));
			if($re){
				go(site_url('/admin/category/'));
				//msg('删除成功！','-1');				
			}
			//header("location:".site_url('admin/category'));
		}

    }

	
	/**
	  *多选删除
	  * -1有内容 ;-2 有子栏目 code
	  */
	public function delall()
    {	
		if($_POST and $this->input->is_ajax_request() )
		{
			$categoryid = $this->security->xss_clean($this->input->post('linkid'));
			$categoryid = explode(',',$categoryid);

			//验证是否有子栏目 有子栏目返回 -2
			$allCategory = $this->db->select('CATID,PARENTID')->get('CATEGORY')->result_array() ;
			$selectedQuery = $this->db->select('CATID,PARENTID')->where_in('CATID',$categoryid)->get('CATEGORY')->result_array();
			$selectedQuery = $this->cate->childid($allCategory, $selectedQuery); //每个添加个childid
			foreach($selectedQuery as $value){
				if($value['childid'] == '')
					continue;
				if( ! in_array( $value['childid'], $categoryid )){
					echo '{"code":"-2"}';return ;
				}
			}
			
			//验证是否有内容 有内容返回 -1
			$query = $this->db->select('ID')->where_in('CATID',$categoryid)->count_all_results('CONTENT');
			if( $query ){
				echo '{"code":"-1"}';return ;
			}

			//删除栏目
			$query = $this->db->where_in('CATID',$categoryid)->delete('CATEGORY');
			if($query){
				echo '{"code":"1"}';
			}else{
				echo '{"code":"0"}';
			}

		}else{
			show_404();
		}		

	}
	
	
   /**
	 *  排序 action 
     *  ( 返回的$_POST['changelistor']  == json ) ===>array
	 */
	public function listorder()
	{
		if($_POST and $this->input->is_ajax_request() )
		{
			// $_POST['changelistor']  json => array
			$changelistor = json_decode( $this->input->post('changelistor') );
			//update  ..... 出错返回 0
			foreach($changelistor as $value){
				$booleanUpdate = $this->db->where('CATID',$value->catid)->update('CATEGORY', array('LISTORDER'=>$value->listorder) );
				if( ! $booleanUpdate ){
					echo '{"code":"0"}';
					return ; 
				}
			}
			echo '{"code":"1"}';
		}else{
			show_404();
		}
	
	}
	
}