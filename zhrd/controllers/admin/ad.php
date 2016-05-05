<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ad extends adminBase{

    public function __construct()
    {
        parent::__construct();
    }

    public function Index()
    {	
    	$this->load->library('Gotopage');
 		$per_page = '3';
 		$datas = $this->gotopage->page_data('AD',$per_page,'','LISTORDER DESC');

    	$data['result'] = $datas['data'];    
      	$data['total'] = $datas['total'];	

      	$data['link'] = $this->gotopage->page_p('AD',site_url('/admin/ad/index/'),$per_page); 	
      	$data['per_page'] = $per_page;

		// $query = $this->db->get('AD');
		// $data['result'] = $query->result_array();

		$this->load->view('admin/ad_list.html',$data);
    }


    public function edit()
    {
		
		if(isset($_POST['dopost']) && $_POST['dopost']=='edit'){
			//执行操作，然后提示信息并跳转
			$adname = $this->input->post('ADNAME');
			$listorder = $this->input->post('LISTORDER');
			$adtype = $this->input->post('ADTYPE');

			$data = array('ADNAME' => $adname, 'ADTYPE' => $adtype, 'LISTORDER' => $listorder );
			$id = $this->input->post('ADID');
			$this->db->where('ADID',$id);
			$re = $this->db->update('AD',$data);
			if(!$re){
				msg("更新广告位失败，请检查数据库配置","-1");
			}		
			// header("Location:".site_url('admin/ad/'));
			msg("广告位更新成功！",site_url('admin/ad/'));

		}else{
			$this->load->view('admin/ad_edit.html');
		}

    }
	
	
	
	
	public function editadurl()
    {
		
		if(isset($_POST['dopost']) && $_POST['dopost']=='edit'){
			//执行操作，然后提示信息并跳转
			$urlto = $this->input->post('URLTO');
			$ISCHECK = $this->input->post('ISCHECK');
			$listorder = $this->input->post('LISTORDER');
			$adtype = $this->input->post('ADTYPE');

			$data = array('URLTO' => $urlto,'ISCHECK' => $ISCHECK);
			$id = $this->input->post('ADID');
			$ITEMID = $this->input->post('ITEMID');
			$this->db->where('ITEMID',$ITEMID);
			$re = $this->db->update('AD_ITEM',$data);
			if(!$re){
				msg("更新广告位失败，请检查数据库配置","-1");
			}		
			// header("Location:".site_url('admin/ad/'));
			msg("链接地址更新成功！",site_url('admin/ad/set?adid='.$id));

		}else{
			$this->load->view('admin/adurl_edit.html');
		}

    }
	
	

    public function set()
    {
		$this->load->helper('number');
    	$adid = $this->input->get('adid');
		if($adid == '' ) show_404();
		$query = $this->db->where('ADID',$adid)->order_by('ITEMID','DESC')->get('AD_ITEM');
    	$data['result'] = $query->result_array();

		$this->load->view('admin/ad_set.html',$data);
    }


    public function set_upload(){

    	$ad_url = $this->input->post('ad_url')?$this->input->post('ad_url'):'';
    	$ad_name = $this->input->post('ad_name')?$this->input->post('ad_name'):'';
    	$ad_type = $this->input->post('ad_type')?$this->input->post('ad_type'):'';
    	$ad_size = $this->input->post('ad_size')?$this->input->post('ad_size'):'';
    	$adid = $this->input->post('adid');
		if( $adid == '') show_404();
    	
    	if (isset($_POST['dopost']) && $_POST['dopost'] == 'upload') {
    		$re = $this->db->insert('AD_ITEM',array('URL'=>$ad_url,'ADID'=>$adid,'FILENAME'=>$ad_name,'FILETYPE'=>$ad_type,'FILESIZE'=>$ad_size));
    		if (!$re) {
    			msg("广告图片上传失败，请检查数据库配置！","-1");
    		}
    		msg("上传广告图片成功！",site_url('admin/ad/set?adid='.$adid));

    	}else{

    		//错误提示 未完善
    		echo $this->load->view('admin/ad_list.html');
    	}



    }


    public function add()
    {
			if(isset($_POST['dopost']) && $_POST['dopost'] == 'add'){
				//执行操作，然后提示信息并跳转到ad_list
				$re = $this->db->insert('AD',array('ADNAME'=>$_POST['ADNAME'],'ADTYPE'=>$_POST['ADTYPE']));
				if(!$re){
					msg("广告位添加失败，请检查数据库配置！","-1");
				}
				// header("Location:".site_url('admin/ad/'));
				msg("广告位添加成功！",site_url('admin/ad/'));
			}else{
				echo $this->load->view('admin/ad_add.html');
			}    		
    }

    public function del()
    {
		$id = $this->input->get('id');
		$cat = $this->input->get('cat');
		$adid = $this->input->get('adid');
		$arr = explode(",", $id);
		if ($cat == 'set') {
			//广告位设定页面删除
			// foreach ($arr as $row) {
			// 		$this->db->delete('AD_ITEM',array('ITEMID' => $row));
			// }
			$re = $where = $this->db->where_in('ITEMID',$arr)->delete('AD_ITEM');
			if (!$re) {
				msg("删除广告图片失败，请检查数据库配置！","-1");
			}

			// header("Location:".site_url('admin/ad/set?adid='.$adid));
			msg("广告图片删除成功！",site_url('admin/ad/set?adid='.$adid));

		} else {
			//广告位列表页面删除
			foreach ($arr as $row) {
					$re = $this->db->delete('AD_ITEM',array('ADID' => $row));
					$re2 = $this->db->delete('AD',array('ADID' => $row));
				}

			if (!$re || !$re2) {
				msg("广告位删除失败，请检查数据库配置！","-1");
			}

			msg("广告位删除成功！",site_url('admin/ad/'));		
			// $this->db->delete('AD', array('ADID' => $id)); 
			// header("Location:".site_url('admin/ad/'));


		}


    }

    public function ts(){
    	$str = "纺织服装、鞋、帽制造业_82
皮革、毛皮、羽毛（绒）及其制品业_83
非金属矿物制品业_84
工艺品及其他制造业_85
煤炭开采和洗选业_86
黑色金属冶炼矿采选业_87
有色金属矿采选业_88
非金属矿采选业_89
交通运输、仓储和邮政业_90
信息传输、计算机服务和软件业_91
批发业_92
零售业_93
住宿业_94
餐饮业_95
租赁业和商务服务业_96
水利、环境和公共设施管理业_97
居民服务和其他服务业_98
食品制造业_99
造纸及纸制品业_100
通用设备制造业_101
木材加工及木、竹、藤、棕、草制品业_102
橡胶制品业_103
交通运输设备制造业_104
家具制造业_105
纺织业_106
饮料制造业_107
文教体育用品制造业_108
仪器仪表及文化、办公用机械制造业_109
印刷业和记录媒介的复制_110
电气机械及器材制造业_111
塑料制品业_112
金属制品业_113
专用设备制造业_114
农副食品加工业_115
通信设备、计算机及其他电子设备制造业_116
建筑业_117
融资性担保机构_118
金融业_130
房地产业_131
其他_132
";
    	
    	$str = str_replace('
', ',', $str);
    	echo $str;
    }
}