<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('admin_model',"admin");
        $this->load->model('group_model',"group");
        $this->load->model('linkage_model',"linkage"); 
		$this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('security');
		$this->load->library('Gotopage');


        //定义错误信息界定符
        $this->form_validation->set_error_delimiters('<!DOCTYPE html>
                <html lang="zh-CN">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>提示信息： </title>
                <link  href="'.base_url("statics/artDialog/skins/blue.css").'" rel="stylesheet" />
                <script src="'.base_url("statics/artDialog/artDialog.source.js").'" type="text/javascript"></script>
                </head>
                <body style="background-color:#E9EAED">
                    <script type="text/javascript">
                      $(function(){
                        art.dialog({title:"提示信息：",content:"', '",cancel:function(){location.href="'.site_url('admin/admin/index').'";},time:1});
        
                });
                    </script>
                </body>
                </html>');

    }


    public function index(){
    	$data   =	'';
    	$where 	= 	'';
    	$pArr 	= 	'';
    	$str 	= 	$this->uri->segment(4,0);
    	$arr 	= 	explode('_', $str);
		$data['area_linkage']      = $this->linkage->getSelect('35');
		//$data['group_linkage']      = $this->linkage->getSelect('214');

    	//带groupid参数的情况
    	if (count($arr) > 1) {
    		$gid 	= 	$arr[1];
    		$where 	= 	"GROUPID ='".$gid."'";			//查询指定用户组用户
    		$pArr 	= 	$arr;							//页码、参数为元素的数组赋给分页类link输出
    	}

   		$data['list'] = $this->admin->getList();
		$data['keywords']='';
    	//分页
 		$per_page 			= 	'20';
 
 		$datas 				= 	$this->gotopage->page_data('ADMIN',$per_page,$where,'USERID asc');
 		//输出页码链接
      	$data['link'] 		= 	$this->gotopage->page_p('ADMIN',site_url('/admin/admin/index/'),$per_page,$where,$pArr); 	

      	$data['per_page'] 	= 	$per_page;
 		$data['list'] 		= 	$datas['data'];					//输出分页数据
 		$data['total'] 		= 	$datas['total'];				//输出数据总和
		$data['groupid'] 	= 	'';

		foreach ($data['list'] as $key => $value) {
			$gid_tmp 			= $value['GROUPID'];
			$gQuery  			= $this->db->get_where('ADMIN_GROUP',array('GROUPID'=>$gid_tmp));
			$gArr	 			= $gQuery->result_array();
			$data['list'][$key]['GROUPNAME'] = $gArr[0]['GROUPNAME'];
		}

    	$this->load->view('admin/admin_list.html',$data);
    }
    
	 public function search(){
	
        
        $per_page='20';

        $dopost=$this->input->post('dopost');

        $where_str='';

        $p_str  = $this->uri->segment(4,0);                             //获取URL第四段字符串参数0_0_0
        $pstr_arr=explode('_',$p_str);                                  //分割get来的字符串为数组
		if($dopost==1){
				$areaArr	= $this->input->post('lxcd');                                 //获取区域id
                $keywords   = $this->input->post('c_name');     				
				$areaid   = $pstr_arr[1]=$areaArr[0];                                 //获取区域id
				$groupid  = $pstr_arr[2]=$this->input->post('select');
                $keywords = $pstr_arr[3]=urldecode($keywords);
		}
		
        $data['area_linkage']      = $this->linkage->getSelect('35',isset($pstr_arr[1])?$pstr_arr[1]:0);

        if(($dopost==1)||(count($pstr_arr)>1)){                         //如果提交或者获取来的数组大于1
            if(count($pstr_arr)>1){                                     //如果是get来的情况
                            
                $areaid   = $pstr_arr[1];                                 //获取区域id
				$groupid  = $pstr_arr[2];
                $keywords = $pstr_arr[3];     				
                $keywords = urldecode($keywords);
                $pArr=array($pstr_arr[0],$areaid,$groupid,$keywords);         //将三个参数组合数组，备赋予分页类
            }else{
			    $keywords = $this->input->post('c_name');
                $temp    = $this->input->post('lxcd');
                $areaid  = $temp[0];
                $groupid = $this->input->post('select');
                $pArr=array(0,$areaid,$groupid,$keywords);
            }
  //print_r ($groupid);
            if (!empty($areaid) ||!empty($groupid)|| !empty($keywords)) {

                if (!empty($areaid)) {
                    $where[]= "AREAID = '".$areaid."'";
                }
				if (!empty($groupid)) {
                    $where[]= "GROUPID = '".$groupid."'";
                }
                if (!empty($keywords)) {
                    $where[]= "USERNAME LIKE '%".$keywords."%'";
                }

                if(count($where)>1){
                    $where_str=implode(' and ',$where);
                }else{
                    $where_str=isset($where[0]) ? $where[0] : '';
                }
                
            }
		
            $datas = $this->gotopage->page_data('ADMIN',$per_page,$where_str,'USERID DESC');
			//print_r($datas);exit;
            $data['list'] = $datas['data'];  
            // print_r($data['list']);exit;
            $data['link'] = $this->gotopage->page_p('ADMIN',site_url('/admin/admin/search/'),$per_page,$where_str,$pArr); 


        }
	
		foreach ($data['list'] as $key => $value) {
			$gid_tmp 			= $value['GROUPID'];
			$gQuery  			= $this->db->get_where('ADMIN_GROUP',array('GROUPID'=>$gid_tmp));
			$gArr	 			= $gQuery->result_array();
			$data['list'][$key]['GROUPNAME'] = $gArr[0]['GROUPNAME'];
		}

		
			$data['keywords'] = $keywords = empty($keywords) ? '' :$keywords ;
			$data['groupid'] = !empty($groupid) ? $groupid : '0' ;
			$data['total'] = $datas['total'];  
			$data['per_page'] = $per_page;    
			$this->load->view('admin/admin_list.html',$data);
	}

	
    public function add(){
    	$data = '';

    	if(isset($_POST['dopost']) && $this->input->post('dopost')=='add') {

	        $this->form_validation->set_rules('USERNAME', '用户名', 'alpha_sub|trim|required|min_length[3]|max_length[20]|xss_clean');
	        $this->form_validation->set_rules('PASSWD', '密码', 'trim|required|matches[PASSWD2]|xss_clean');
	        $this->form_validation->set_rules('TEL', '手机号码', 'trim|xss_clean');


    		if ($this->form_validation->run() == FALSE){
    				//返回验证错误的信息
                    $str = validation_errors();
                    echo $str;
               
            }else{

            		//print_r($this->input->post());
            		$username           =	$this->input->post('USERNAME');
            		$gid                =   $this->input->post('GROUPID');
		    		$passwd    			=   $this->input->post('PASSWD');
		    		$tel    			=   $this->input->post('TEL');
					$encrypt  			= 	$this->_rand_str(4);
					$passwd 			= 	md5($passwd.$encrypt);	
					//获取最后一级areaid
					$areaArr			=   $this->input->post('lxcd');
					$areaid 			=	end_id($areaArr);
		    		if (end_id($areaArr) !== '') {
		    			$areaid 	=	end_id($areaArr);
		    		}else{
		    			$areaid     =   $areaArr[0];
		    		}

					$datas 				= 	array(
							               			'USERNAME' 	=> $username,
							               			'GROUPID' 	=> $gid,
							               			'PASSWD' 	=> $passwd,
							               			'ENCRYPT' 	=> $encrypt,
							               			'AREAID' 	=> $areaid,
							               			'TEL' 	=> $tel,
						            		);
					if(!check_same('ADMIN','USERNAME',$username)){	
						msg('该用户名已存在！','-1');
					}

					if($gid == '1'){	
						msg('你无法添加超级管理员！','-1');
					}

					$re = $this->db->insert('ADMIN',$datas);
					if (!$re) {
						msg('添加用户失败，请检查数据库配置！','-1');
					}

					msg('添加用户成功！',site_url('admin/admin/index/'));

            }

    	}else{

	    	$this->db->select('GROUPID,GROUPNAME');
	    	$data['gArr'] = $this->group->getList(); 		


    		$this->load->view('admin/admin_add.html',$data);	

    	}

    }

    public function edit(){
    	$data	= '';

    	if(isset($_POST['dopost']) && $this->input->post('dopost')=='edit') {

	    	//定义验证规则
	        $this->form_validation->set_rules('USERNAME', '用户名', 'alpha_sub|required|min_length[3]|max_length[100]|xss_clean|trim');
	        $this->form_validation->set_rules('PASSWD', '密码', 'trim|matches[PASSWD2]|min_length[3]|max_length[30]|xss_clean');


    		if ($this->form_validation->run() == FALSE){
    				//返回验证错误的信息
                    $str = validation_errors();
                    echo $str;
               
            }else{

		    		$username 	= 	$this->input->post('USERNAME');
		    		$gid 		= 	$this->input->post('GROUPID');
		    		$passwd     =   $this->input->post('PASSWD');
		    		$userid     =	$this->input->post('USERID');
		    		$areaArr	=   $this->input->post('lxcd');
		    		$tel 		= 	$this->input->post('TEL');

		    		if (end_id($areaArr) !== '') {
		    			$areaid 	=	end_id($areaArr);
		    		}else{
		    			$areaid     =   $areaArr[0];
		    		}
										
					if ($areaid == '0') {
						$areaid = $areaArr['0'];
						if ($areaid == '0') {
							msg('请选择该用户所属的地区！','-1');

						}
					}

					if(check_same('ADMIN','USERID',$userid)){	
						msg('该用户不存在！','-1');
					}

					if($gid == '1'){	
						msg('你无法修改超级管理员！','-1');
					}

					$datas 		= 	array(
							               'USERNAME' 	=> $username,
							               'GROUPID' 	=> $gid,
							               'AREAID' 	=> $areaid,
							               'TEL' 		=> $tel,
						            	);

		    		if ($passwd !== '') {
		    			$passwd    			=   $this->input->post('PASSWD');
						$encrypt  			= 	$this->_rand_str(4);
						$passwd 			= 	md5($passwd.$encrypt);	
						$datas['ENCRYPT']   =   $encrypt;
						$datas['PASSWD']    =   $passwd;     	
		    		}

					$this->db->where('USERID',$userid);
					$re = $this->db->update('ADMIN',$datas);
					if (!$re) {
						msg('更新失败，请检查数据库配置！','-1');
					}
					
					msg('更新用户成功！',$_SERVER['HTTP_REFERER']);
    		
    		}

    		
    	} else {


    		$uid 	= $this->uri->segment(4,0);
	    	$this->db->select('GROUPID,GROUPNAME');
	    	$data['gArr'] = $this->group->getList();

	    	$this->db->where('USERID',$uid);
	    	$this->db->select('USERID,USERNAME,GROUPID,AREAID,TEL');

	    	$uArr = $this->admin->getList();
	    	$data['userid'] 	= 	$uArr[0]['USERID'];
	    	$data['username'] 	= 	$uArr[0]['USERNAME'];
	    	$data['gid'] 		= 	$uArr[0]['GROUPID'];
	    	$data['areaid'] 	= 	$uArr[0]['AREAID'];
	    	$data['tel'] 		= 	$uArr[0]['TEL'];

	    	$this->load->view('admin/admin_edit.html',$data);
    	
    	}

    }

    public function del(){

    	$uid = $this->input->get('uid');

    	$uArr = explode(',', $uid);
    	foreach ($uArr as $v) {
    		if ($v == '1') {
    			msg('超级管理员用户无法删除！','-1');
    		}
    	}

        $re = $this->db->where_in('USERID',$uArr)->delete('ADMIN');

         if(!$re){
             msg("删除用户失败，请检查数据库配置！","-1");
         }
        //header("Location:".site_url('admin/member/'));

         msg("删除用户成功！",site_url('admin/admin/index/'));


    }



	
	/*
	 *修改密码
	 */
	public function changePwd()
	{
		if($_POST){
			//数据验证
			$oldPwd  = $this->security->xss_clean($this->input->post('oldPwd'));
			$newPwd  = $this->security->xss_clean($this->input->post('newPwd'));
			$newPwd2 = $this->security->xss_clean($this->input->post('newPwd2'));
			if( $oldPwd == '' || $newPwd == '' || $newPwd2 == '' || $newPwd != $newPwd2 || !preg_match('~^[a-zA-z0-9]{4,16}$~',$oldPwd) || !preg_match('~^[a-zA-z0-9]{4,16}$~',$newPwd) || !preg_match('~^[a-zA-z0-9]{4,16}$~',$newPwd2) ){
				msg('非法提交',-1);return;
			}
			//获取原 PASSWD ENCRYPT
			$username = $this->security->xss_clean( $this->session->userdata('uname') );
			$query    = $this->db->select('PASSWD,ENCRYPT')->where('USERNAME',$username)->get('ADMIN')->result_array();
			$oldPassword = $query[0]['PASSWD'];
			$oldEncrypt  = $query[0]['ENCRYPT'];
			unset($query);
			
			//验证原密码
			if( md5( $oldPwd.$oldEncrypt ) != $oldPassword ){
				msg('原密码不正确', -1);return;
			}
			
			//重置encryp
			$encrypt  = $this->_rand_str(4);
			$password = md5($newPwd.$encrypt);	

			//md5( substr($newPwd,0,3).substr($encrypt,2).substr($newPwd,3).substr($encrypt,0,2) );o(╯□╰)o
			
			$this->db->where('USERNAME',$username)->update('ADMIN',array('PASSWD'=>$password,'ENCRYPT'=>$encrypt) );
			$this->session->sess_destroy();

			msg('更新成功，请重新登录','','1',true);
		}else{
			$this->load->view('admin/admin_changePwd.html');
		}
	}

	//生成随机字符串
	private function _rand_str($length)
	{
		$str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
		return substr(str_shuffle($str),0,$length);
	}


}