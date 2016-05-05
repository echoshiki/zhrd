<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('email');
        $this->load->model('linkage_model',"linkage"); 
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
                        art.dialog({title:"提示信息：",content:"', '",cancel:function(){location.href="'.site_url('admin/member/').'";},time:1});
        
                });
                    </script>
                </body>
                </html>');

    }

    public function Index()
    {     

        //搜索功能，筛选联动菜单
        $data['area_linkage']      = $this->linkage->getSelect('35');
        
        $per_page='12';

        $dopost=$this->input->post('dopost');

        $where_str='';

        $p_str  = $this->uri->segment(4,0);                             //获取URL第四段字符串参数0_0_0
        $pstr_arr=explode('_',$p_str);                                  //分割get来的字符串为数组


        if(($dopost==1)||(count($pstr_arr)>1)){                         //如果提交或者获取来的数组大于1


            if(count($pstr_arr)>1){                                     //如果是get来的情况
                            
                $areaid = $pstr_arr[1];                                 //获取区域id
                $keyword = $pstr_arr[2];                                
                $keyword = urldecode($keyword);
                $pArr=array($pstr_arr[0],$areaid,$keyword);         //将三个参数组合数组，备赋予分页类
            }else{
                $area    = $this->input->post('lxcd');              
                $areaid  = $area[0];
                $keyword = $this->input->post('c_name');
                $pArr=array(0,$areaid,$keyword);
            }


            


            if (!empty($areaid) || !empty($keyword)) {

                if (!empty($areaid)) {
                    $where[]= "AREAID LIKE '%".$areaid."%'";
                }
                if (!empty($keyword)) {
                    $where[]= "COMPANY LIKE '%".$keyword."%'";
                }

                if(count($where)>1){
                    $where_str=implode(' and ',$where);
                }else{
                    $where_str=isset($where[0]) ? $where[0] : '';
                }
                
            }

            $datas = $this->gotopage->page_data('MEMBER',$per_page,$where_str,'USERID DESC');
            $data['result'] = $datas['data'];  
            
            $data['link'] = $this->gotopage->page_p('MEMBER',site_url('/admin/member/index/'),$per_page,$where_str,$pArr); 


        }else{
            $datas = $this->gotopage->page_data('MEMBER',$per_page,$where_str,'USERID DESC');
            $data['result'] = $datas['data']; 
            
            $data['link'] = $this->gotopage->page_p('MEMBER',site_url('/admin/member/index/'),$per_page,$where_str); 
        }
  
        $data['total'] = $datas['total'];  
        $data['per_page'] = $per_page;    
        
        $this->load->view('admin/member_list.html',$data);
    }


    public function edit()
    {

        $this->form_validation->set_rules('USERNAME', '用户名', 'alpha_dash|trim|required|min_length[3]|max_length[15]|xss_clean');
        $this->form_validation->set_rules('PASSWD', '密码', 'trim|matches[PASSWD2]|min_length[5]|max_length[30]|xss_clean');

        if(isset($_POST['dopost']) && $this->input->post('dopost')=='edit'){

            if ($this->form_validation->run() == FALSE){

                    $str = validation_errors();
                    echo $str;
               
            }else{

                    $username   = trim($this->input->post('USERNAME'));
                    $company    = trim($this->input->post('COMPANY'));
                    $email      = trim($this->input->post('EMAIL'));
                    $areaid     = $this->input->post('lxcd');
                    $areaid     = $areaid[0];

                    $passwd     =   $this->input->post('PASSWD');

                    $username_old   = trim($this->input->post('USERNAME_OLD'));
                    $email_old      = trim($this->input->post('EMAIL_OLD'));

                    if ($username !== $username_old) {
                        if (!check_same('MEMBER','USERNAME',$username)) { 
                            msg('该用户名已经存在！','-1'); 
                        }
                    }

                    if ($email) {

                        if ($email !== $email_old) {
                            if (!check_same('MEMBER','EMAIL',$email)) { 
                                msg('该Email已经存在！','-1'); 
                            }
                        }

                        if (valid_email($email) == false) { 
                            msg('输入的email格式不合法！','-1');
                        }                
                    }


                    $data = array('USERNAME' => $username, 'AREAID' => $areaid, 'COMPANY' => $company, 'EMAIL' => $email);

                    //密码不为空时更新用户密码
                    if ($passwd !== '') {
                        $passwd             =   $this->input->post('PASSWD');
                        $encrypt            =   $this->_rand_str(4);
                        $passwd             =   md5($passwd.$encrypt);  
                        $data['ENCRYPT']   =   $encrypt;
                        $data['PASSWD']    =   $passwd;        
                    }


                    
                    $id = $this->input->post('USERID');
                    $this->db->where('USERID',$id);
                    $re = $this->db->update('MEMBER',$data);      
                    //header("Location:".site_url('admin/member/'));
                    if($re){
                        msg('更新用户成功！',site_url('admin/member/'));
                    }else{
                        msg('更新用户失败，请检查数据库配置！','-1');
                    }

            }
        } else {

            $data['userid'] = $this->input->get('id');
            $data['username'] = $this->input->get('name');
            $data['email'] = $this->input->get('email');
            $data['company'] = $this->input->get('company');
            $data['areaid'] = $this->input->get('areaid');
            $this->load->view('admin/member_edit.html',$data);
        }

    }


    public function add()
    {
        $this->form_validation->set_rules('USERNAME', '用户名', 'alpha_dash|trim|required|min_length[3]|max_length[15]|xss_clean');
        //$this->form_validation->set_rules('PASSWD', '密码', 'trim|matches[PASSWD2]|min_length[5]|max_length[30]|xss_clean');
       // $this->form_validation->set_rules('USERNAME', '用户名', '');


        if(isset($_POST['dopost']) && $this->input->post('dopost')=='add'){
       
            if ($this->form_validation->run() == FALSE){

                    $str = validation_errors();
                    echo $str;
               
            }else{
                    
                    $username = trim($this->input->post('USERNAME'));
                    $passwd = trim($this->input->post('PASSWD'));
                    $passwd = do_hash($passwd,'md5');
                    $company = trim($this->input->post('COMPANY'));
                    $areaid = $this->input->post('lxcd');
                    $areaid = $areaid[0];
                    $email = trim($this->input->post('EMAIL'));
                    $mobile = trim($this->input->post('MOBILE'));

                    echo validation_errors(); 

                    if (!check_same('MEMBER','USERNAME',$username)) { 
                        msg('该用户名已经存在！','-1'); 
                    }

                    if ($email) {

                        if (!check_same('MEMBER','EMAIL',$email)) { 
                            msg('该Email已经存在！','-1'); 
                        }

                        if (valid_email($email) == false) { 
                            msg('输入的email格式不合法！','-1');
                        }                
                    }

                    $regdate = time();
                    $data = array(
                                'USERNAME'  => $username,
                                'PASSWD'    => $passwd,
                                'COMPANY'   => $company,
                                'AREAID'    => $areaid,
                                'EMAIL'     => $email,
                                'MOBILE'     => $mobile,
                                'STATUS'     => '1',
                                'REGDATE'   => $regdate
                            );
                    $query = $this->db->insert('MEMBER',$data);
                    if (!$query) {
                        msg('添加用户失败，请检查数据库配置！','-1');            
                    }

                    msg('添加用户成功！',site_url('admin/member/'));

             }

        } else {   
    
            $this->load->view('admin/member_add.html');   
        }

    }



    public function test(){

        $cm=$this->uri->rsegment_array();  
        print_r($cm);
    }


    public function del()
    {
        $id     = $this->input->get('id');
        $id     = $this->security->xss_clean($id);
        $id     = explode(',',$id);
        $re     = $this->db->where_in('USERID',$id)->delete('MEMBER');
        $re2    = $this->db->where_in('USERID',$id)->delete('MSG');
        if(!$re){
            msg("删除用户失败，请检查数据库配置！","-1");
        }
        if(!$re2){
            msg("删除用户相关留言失败，请检查数据库配置！","-1");
        }
        //header("Location:".site_url('admin/member/'));
        msg("删除用户成功！",site_url('admin/member/'));
    }


    //生成随机字符串
    private function _rand_str($length)
    {
        $str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
        return substr(str_shuffle($str),0,$length);
    }


}