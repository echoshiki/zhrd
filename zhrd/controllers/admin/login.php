<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
		$this->load->library('session');
    }

    public function index(){
	   $this->login();
    }

    public function login(){
		$uname=$this->session->userdata('uname');
		if(!empty($uname)){
			msg('已经登录',site_url('admin/index'));
		}

        if($_POST && $this->input->post('dopost') == 1) //POST
		{
			$info = $this->security->xss_clean( $this->input->post('info') );
			
			$info['USERNAME'] = strAddslashes($info['USERNAME']);
			$info['PASSWD']   = strAddslashes($info['PASSWD']);

			if( strtolower($info['checkCode']) != $this->session->userdata('checkCode') 
			 and (strtolower($info['checkCode']) != strtolower( $this->session->userdata('checkCode')  ))
			){
				$this->session->unset_userdata('checkCode');
				msg('验证码不正确', site_url('admin/login') );				
			}
			
            $re = $this->db->get_where('ADMIN',array('USERNAME' => $info['USERNAME']))->result_array();
			try
			{
				if( empty($re)){
					throw new Exception();
				}
				
				if( $re[0]['PASSWD'] === md5( $info['PASSWD'] . $re[0]['ENCRYPT']) ){


					$this->load->model('Group_model',"group");
					$groupname=$this->group->getGroupByUser($info['USERNAME']);
                    $this->session->set_userdata(
                    							array(
												'adminLogged'   => TRUE,
												'USERID'   => $re[0]['USERID'],
												'uname'    => $re[0]['USERNAME'],
												'ROLEID'   => $re[0]['ROLEID'],
												'AREAID'   => $re[0]['AREAID'],
												'GROUPID'  => $re[0]['GROUPID'],
												'GROUPNAME'=> $groupname,
														)
												);
					redirect( site_url('admin/index'),200);
				}
				throw new Exception();
			}catch(Exception $e){
				$this->session->unset_userdata('checkCode');
				msg('用户名密码不正确', site_url('admin/login') );
			}
			
        }else{		//load view
        	$data = array();
			$this->load->helper('captcha');
			$vals = array(
						'word' => rand_str(4,TRUE),
						'img_path' => './captcha/',
						'img_url' => base_url().'captcha/',
						'font_path' => APPPATH . 'fonts' . DIRECTORY_SEPARATOR . 'HandVetica.ttf',
						'img_width' => '100',
						'img_height' => 30,
						'expiration' => 1200
			);

			$cap = create_captcha($vals);
			$this->session->set_userdata('checkCode',  $vals['word']  ); 
			$data['checkCode']=$cap['image'];           
            
			
            $this->load->view('admin/login.html',$data);
        }
    }

    public function logout(){
		$this->session->sess_destroy();
		redirect( site_url('admin/login') );
    }

}