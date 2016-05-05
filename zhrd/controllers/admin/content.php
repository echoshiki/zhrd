<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Content extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Content_model',"content");
        $this->load->model('Category_model',"cate");
    }

    public function Index()
    {
		$this->load->model('index_model');
		$this->load->library('Pick');

		if ($_POST['do']) {
			//id为202的新闻频道采集功能
			$site = $_POST['site'];
			$url  = trim($_POST['url']);
			$page = $_POST['page'] ? trim($_POST['page']) : '';		
			$num  = $_POST['num'] ? trim($_POST['num']) : '';	
			switch ($site) {   //按采集网站制作新闻数据
					case '1':   //页数，一财网
						$dataArr = $this->pick->pick_1($url);
						for ($i=1; $i < $page; $i++) { 
							$url = $url."index-".$i.".html";
							//合并数组
							$dataArr = array_merge($dataArr,$this->pick->pick_1($url));
						}			
						break;			
					case '2':   //条数，经济参考网
						$dataArr = $this->pick->pick_2($url,$num);
						break;
				}	
				//sql CURD操作
				//print_r($dataArr);
				$counts = 0;
				foreach ($dataArr as $key => $value) {
					$contArr['TITLE'] 		= $value['title'];
					$contArr['INPUTTIME'] 	= $value['date'];
					$contArr['CATID']       = '202';   //id固定202
					$contArr['TYPEID']      = '2';   //类型：新闻列表
					$contArr['ISPAGE']      = $value['fromsite'];   //出自网站（采集）
					//检测是否存在重复新闻
					if(!check_same('CONTENT','TITLE',$contArr['TITLE'])){
						continue;
					}
					if($value['content']!=""){
						$this->db->insert('CONTENT',$contArr);   //插入content表
						$insertId = last_id('CONTENT','ID');   //获取id
						$contents = $value['content']; 
						for($i=1;$i<=5;$i++){
    						$contents = preg_replace("#<a[^>]*>(.*?)</a>#is", "$1", $contents);
						}
						//写入文件 upload/content/
						$filename = 'uploads/content/'.$insertId.".txt";
						if (!file_exists($filename)) {
							$fh = fopen($filename, 'w+');
							fputs($fh,$contents);
						}
						//写入文件完毕
						$counts++;
					}
				}
				msg('成功采集'.$counts.'条数据!',site_url('admin/content/index').'/202');
		}else{
			//添加内容
			$data['cid'] = $this->uri->segment(4,0);
			$catname = $this->db->select('CATNAME')->where('CATID',$data['cid'])->get('CATEGORY')->result_array();
			$data['list']=$this->content->getList($data['cid']);
			//每一行添加 栏目名称
			if( !empty( $data['list'] ) ){
				foreach($data['list'] as $k=>$val){
					$data['list'][$k]['catname'] = $catname[0]['CATNAME'];
				}
			}
			$data['list'] = $this->index_model->getShowUrl($data['list']);
			$this->load->view('admin/content_list.html',$data);
		}
    }

    public function add()
    {
		if($_POST && $this->input->post('dopost') == 1){
			$info			= $this->security->xss_clean( $this->input->post('info') );
			if( empty( $info['POSIDS'] ) ) {$info['POSIDS'] = 0;}

			if( !isset($info['TITLE']) || !isset($info['CONTENT']) ){
				msg('标题或内容不能为空','-1');
			}
			if( !$info['CATID'] ) msg('非法提交','-1');
			
			$queryType = $this->db->select('CATID,TYPE')->where('CATID',$info['CATID'])->get('CATEGORY')->result_array();
			$queryType = $queryType[0]['TYPE'];
			$re=$this->db->insert('CONTENT',array( 	
													'CATID'			=> $info['CATID'],
													'TYPEID'		=> $queryType,
													'TITLE'			=> $info['TITLE']? str_cut( $info['TITLE'],'100',''):'',
													'KEYWORDS'		=> $info['KEYWORDS']?str_cut( $info['KEYWORDS'],'100',''):'',
													'THUMB'			=> $info['THUMB']?str_cut( $info['THUMB'],'100',''):'',
													'POSIDS'		=> $info['POSIDS']? $info['POSIDS']:'',
													'DESCRIPTION' 	=> $info['DESCRIPTION']?str_cut( $info['DESCRIPTION'],'200',''):'',
													'INPUTTIME'		=> time(),
													'UPDATETIME'	=> time()
												  )
								);
			$insertId = last_id('CONTENT','ID');

			//写入文件 upload/content/
			$filename = 'uploads/content/'.$insertId.".txt";
			if (!file_exists($filename)) {
				$fh = fopen($filename, 'w+');
				fputs($fh,$info['CONTENT']);
			}
			//写入文件完毕

			$content  = $this->content->encode_html($info['CONTENT']); 
            $re2 	  = $this->content->insert($content,$insertId);

			if($re && $re2){msg('添加成功！',site_url('admin/content/index').'/'.$info['CATID'].'/');}else{
				if( !$re)
					$this->db->where('ID',$insertId)->delete('CONTENT');
				if( !$re2)
					$this->db->where('ID',$insertId)->delete('CONTENT_DATA');
				msg('添加失败，请重试', '-1'); 
			}
		}else{
			$data='';	
			$data['cid']=$this->uri->segment(4,0);
			$data['tree']=$this->cate->getList();
			$this->load->view('admin/content.html',$data);
		}
    }


    public function edit()
    {
		if( $_POST && $this->input->post('dopost') == 'edit'){
			$info	= $this->security->xss_clean( $this->input->post('info') );
			if(empty($info['POSIDS'])){ $info['POSIDS'] = 0; }
			if(!isset($info['TITLE'])){ msg('标题不能为空','-1'); }
			$arr = array(
						'CATID'			=> $info['CATID']       ?$info['CATID']:'',
						'TYPEID'		=> $info['TYPEID']      ?$info['TYPEID']:'',
						'TITLE'			=> $info['TITLE']       ?str_cut( $info['TITLE'],'100',''):'',
						'KEYWORDS'		=> $info['KEYWORDS']    ?str_cut( $info['KEYWORDS'],'100',''):'',
						'THUMB'			=> $info['THUMB']       ?str_cut( $info['THUMB'],'100',''):'',
						'POSIDS'		=> $info['POSIDS']      ? $info['POSIDS']:'0',
						'DESCRIPTION' 	=> $info['DESCRIPTION'] ?str_cut( $info['DESCRIPTION'],'200',''):'',
						'UPDATETIME'	=> time(),
						'POSIDS'	    => empty($info['POSIDS'])? 0 :$info['POSIDS'],
            );
			$re 		= $this->db->where('ID',$info['ID'])->update('CONTENT',$arr);

			//将新的文件内容写进原来的文件里
			$content 	= $this->content->encode_html($info['CONTENT']); 
			$filename 	= 'uploads/content/'.$info['ID'].'.txt';
			file_put_contents($filename,$content);
			//更新写入文本结束
			msg('更新成功！',site_url('admin/content/index').'/'.$info['CATID'].'/');
		}else{
			$data 	= array();	
			$id		= $this->uri->segment(4,0);
			if($this->db->where('ID',$id)->count_all_results('CONTENT') == 0){ show_404(); }
			$arr 	= $this->db->where('ID',$id)->get('CONTENT')->result_array();

			//读取文件内容，通过文件名（ID）
				// 由于服务器配置问题，只能采取curl方式读取文本（已失效 2014/11/27）
				// $filename = 'localhost/uploads/content/'.$id.".txt";
	   			// $ch = curl_init();
	   			// curl_setopt ($ch, CURLOPT_URL, $filename);
	   			// curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
				// curl_setopt ($ch, CURLOPT_ENCODING ,'utf8'); 
	   			// $content = curl_exec($ch);
	   			// curl_close($ch);
			$filename = 'uploads/content/'.$id.".txt";
			$content  = file_get_contents($filename);
			//读取文件内容结束

			$data['tree']	        = $this->cate->getList();
			$data['ID'] 			= $arr[0]['ID'];
			$data['typeid'] 			= $arr[0]['TYPEID'];
			$data['CATID'] 			= $arr[0]['CATID'];
			$data['TITLE'] 			= $arr[0]['TITLE'];
			$data['THUMB'] 			= $arr[0]['THUMB'];
			$data['KEYWORDS'] 		= $arr[0]['KEYWORDS'];
			$data['DESCRIPTION'] 	= $arr[0]['DESCRIPTION'];
			$data['POSIDS'] 		= $arr[0]['POSIDS'];
			$data['CONTENT'] 		= $content;
			$this->load->view('admin/content_edit.html',$data);
		}
	}

    public function del()
    {
		$id = $this->security->xss_clean( $this->uri->segment(4,0) );
		if($id=='') show_404();
		
		$query   	= $this->db->select('CATID')->where('ID',$id)->get('CONTENT')->result_array();
		$query   	= $query[0]['CATID'];
		$re      	= $this->db->delete('CONTENT',array('ID'=>$id));
		$filename 	= 'uploads/content/'.$id.".txt";
		unlink($filename);
		//$re_data = $this->db->delete('CONTENT_DATA',array('ID'=>$id));
		msg('删除成功！', site_url('admin/content/index/'.$query));
    }
 
  // 多选删除 
  // $_POST['id']   ',' 分割 ==> array
	public function delAll() {
		if($_POST && $this->input->is_ajax_request() )
		{
			$id = $this->security->xss_clean($this->input->post('id'));
			$id = explode(',',$id);	
			$query      = $this->db->where_in('ID',$id)->delete('CONTENT');
			foreach ($id as $key => $value) {
				$filename 	= 'uploads/content/'.$value.".txt";
				unlink($filename);
			}		
			if($query){
				echo '{"code":"1"}';
			}else{
				echo '{"code":"0"}';
			}
		}else{
			show_404();
		}
	}
	
   // 排序 action 
   // 返回 $_POST['changelistor']  == json ) ===>array
	public function listorder()
	{
		if($_POST && $this->input->is_ajax_request() )
		{
			//json => array
			$changelistor = json_decode( $this->input->post('changelistor') );
			foreach($changelistor as $value){
				$booleanUpdate = $this->db->where('ID',$value->id)->update('CONTENT', array('LISTORDER'=>$value->listorder) );
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

	public function pick(){
		$this->load->library('Pick');
		$this->load->view('admin/content_pick.html');
	}
	
}