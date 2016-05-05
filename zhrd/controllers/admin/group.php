<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Group extends adminBase{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('group_model',"group");
        $this->load->model('admin_model',"admin");
    }

    public function index(){
    	$data='';
        $data['list']       = $this->group->getList();
      
        $this->load->view('admin/group_list.html',$data);
    }

    public function add(){
    	$data='';
        if(isset($_POST['dopost']) && $this->input->post('dopost')=='add') {

            $groupname = $this->input->post('GROUPNAME');
            $listorder = $this->input->post('LISTORDER');

            if (!check_same('ADMIN_GROUP','GROUPNAME',$groupname)) {
                msg('用户组名已经被使用，请输入其他组名。','-1');
            }

            $datas  =   array(
                            'GROUPNAME'  => $groupname,
                        );   
            if (!empty($listorder)) {
                $datas['LISTORDER'] = $listorder;
            }

            $re = $this->db->insert('ADMIN_GROUP',$datas);
            if (!$re) {
                msg('添加用户组失败，请检查数据库配置。','-1');
            }

            msg('添加用户组成功！',site_url('admin/group/index/'));

        }else{

           $this->load->view('admin/group_add.html',$data); 
        }

    	



    }

    public function edit(){

    }

    public function del(){

        $gid    =   $this->input->get('gid');
        $gArr   =   explode(',', $gid);

        foreach ($gArr as $v) {
            if ($v == '1') {
                msg('超级管理员用户组无法删除！','-1');
            }

            $this->db->select('USERID');
            $this->db->where('GROUPID',$v);
            $arr = $this->admin->getList();
            if (count($arr) > 0){
                msg('选择的组别中仍存在用户，请清空后再删除！','-1');
            } 
        }

        $re = $this->db->where_in('GROUPID',$gArr)->delete('ADMIN_GROUP');

         if(!$re){
             msg("删除用户组失败，请检查数据库配置！","-1");
         }
        //header("Location:".site_url('admin/member/'));

         msg("删除用户组成功！",site_url('admin/group/index/'));
    }



}