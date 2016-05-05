<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zyc
 * Date: 13-7-6
 * Time: 下午6:44
 * To change this template use File | Settings | File Templates.
 */

class ZHRD_Controller extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }
}

/*
 * admin后台class 需要继承的父类
 */
class adminBase extends ZHRD_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
		$this->load->model('Admin_model',"admin");
		$this->load->model('Role_model',"role");
		$this->admin->checkLogin();
		$this->role->checkRole();
    }
}

/*
 * user用户class 需要继承的父类
 */
class userBase extends ZHRD_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->library('session');
		//验证是否登录
		if($this->uri->uri_string  != 'member/login'&& ($this->session->userdata('member_logged_in') !== TRUE ))
		{
			redirect(site_url('/member/login'),200);
		}
		$this->session->set_userdata('member_status','1');
		//验证是否通过sms验证
		if( $this->uri->uri_string  != 'member/logout' && $this->uri->uri_string  != 'member/sendsms'){
			if( $this->uri->uri_string  != 'member'&& $this->session->userdata('member_logged_in') === TRUE  && $this->session->userdata('member_status') !== '1'){
				redirect( site_url('/member/'));
			}
		}

	}

}