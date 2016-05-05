<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Excel_model extends CI_Model{
        function __consturct(){
            parent::__construct();
    }

    public function read($filepath=''){
        // print_r($filepath);exit;
        if (!file_exists($filepath)) {
            msg("文件不存在.");
        }
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        if(!defined('EOL')){
         define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');   
        }
        

        date_default_timezone_set('Asia/Shanghai');

        $objPHPExcel = PHPExcel_IOFactory::load($filepath);

        $dataArr     = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        return $dataArr;   
    }

    public function export($data,$header=''){
        
        $objPHPExcel = new PHPExcel();
        $objWorksheet = $objPHPExcel->getActiveSheet();
        

        if(empty($data)){msg('没有数据');}
        if(!empty($header)){
            
            $objWorksheet->setTitle($header);
        }


        $objWorksheet->fromArray($data);


        $filename=date('Ymd_His',time()).'.xlsx';
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( 'Content-Disposition:inline;filename="'.$filename.'"' );
        header ( "Content-Transfer-Encoding: binary" );
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Pragma: no-cache" );
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        
    }

    /*
    *   $fieldArr   对应的存储字段
    *   $filepath   excel存放路径
    *
    */
    public function importAdmin($table,$fieldArr='',$filepath,$startRow='1',$tNum='10'){
        if (!file_exists($filepath)) {
            msg("文件不存在.");
        }
        $data   = $this->read($filepath);

        $i=$n='';
        foreach($data as $k=>$v){
            $n++;
            if($n<=$startRow-1){continue;}            
            $arr    = array_combine($fieldArr,$v);
            $arr    = $this->rule2insertType($arr,$table);
            $arr['USERNAME']    = (string)$arr['USERNAME'];
            if($table='ADMIN'){
                $result = $this->db->where('USERNAME',$arr['USERNAME'])->get('ADMIN')->result_array();
            }
            if($result){continue;}
            $re     = $this->db->insert($table,$arr);
            if($re){$i++;}
        }
        $i= $i ? $i : '0';
        msg('成功导入'.$i.'条数据！',site_url('admin/admin'));
    }



    


    public function rule2insertType($arr,$table){
        $replace_arr = array('GROUPID','AREAID');
        
        foreach($arr as $kk=>$vv){
            if(in_array($kk,$replace_arr)){
                $nv=explode('_',$vv);
                if(!empty($nv[1])){
                    $arr[$kk]=$nv[1];
                }
            }
            
        }
        if($table=='ADMIN'){
                $parr=return_password($arr['PASSWD']);
                $arr['PASSWD']=$parr['password'];
                $arr['ENCRYPT']=$parr['encrypt'];
        }

        return $arr;
    }
 

    /*
        批量导入公司融贷申请表单及附带添加默认用户
        2013-11-11备注
    */
    public function insertExcel($data,$excelname,$importuserid){
            
            $formatdata    = $this->formatData($data,$excelname);
            if(isset($formatdata['errorMsg'])){
                $str  = "<br>文件（".$excelname."）格式错误如下:"; 
                $str .= $formatdata['msgStr'];
                return $str; 
            }else{

                $formArr          = $formatdata['form']; 
                $form_mainArr     = $formatdata['form_main'];
                $str              = '';

                foreach ($form_mainArr as $k => $v) {
                    $form_mainArr[$k]['TYPE']       = '3';
                    $form_mainArr[$k]['FINISH']     = '1';
                    $form_mainArr[$k]['STATUS']     = '43';
                    $form_mainArr[$k]['CREATETIME'] = time();
                    $form_mainArr[$k]['ENDTIME']    = time();
                    $form_mainArr[$k]['IMPORTUSERID'] = $importuserid;
                }

                //去掉空值

                foreach ($form_mainArr as $key => $value) {
                    if ($value['LICENCE'] == '') {
                        unset($form_mainArr[$key]);
                        unset($formArr[$key]);
                    }else{
                        //二次验证关键字段是否为空
                        $ks    = $key + 1;
                        foreach ($form_mainArr[$key] as $kt => $vt) {
                            if ($vt == '') {
                                $str .= '<br />值'.$ks.'的'.$kt.'不能为空。';
                            }
                        }
                        $arrW = string2array($formArr[$key]['WARRANT']); 
                        $arrS = ''; 
                        foreach ($arrW as $k => $v) {
                            if ($v == '') {
                                $arrS = '1';
                            }
                        }
                        if ($arrS == '1') {
                            $str .= '<br />值'.$ks.'的担保方式请填写完整。';
                        }
                    }
                }
                if ($str !== '') {
                    return $str;
                }
            
                $ii = count($form_mainArr);
                $n  = 0;

                foreach ($form_mainArr as $key => $value) {
                    $query      = $this->db->insert('FORM',$form_mainArr[$key]);
                    $id_form    = last_id('FORM','ID'); //取得插入主表ID  
                    $formArr[$key]['ID'] = $id_form;          
                    $query2     = $this->db->insert($form_mainArr[$key]['FORM'],$formArr[$key]);
                    /*  User insert start  */  
                        $member['USERNAME']     =   $form_mainArr[$key]['LICENCE'];
                        $passwd                 =   '123123';  //初始密码
                        $encrypt                =   $this->_rand_str(4);
                        $member['PASSWD']       =   md5($passwd.$encrypt); 
                        $member['ENCRYPT']      =   $encrypt;
                        $member['COMPANY']      =   $form_mainArr[$key]['COMPANY'];
                        $member['MOBILE']       =   $form_mainArr[$key]['TEL'];
                        $member['LICENCE']      =   $form_mainArr[$key]['LICENCE'];
                        $member['REGDATE']      =   time();
                        $member['AREAID']       =   $form_mainArr[$key]['AREAID'];
                        $member['LASTDATE']     =   time();
                        $member['STATUS']       =   '1';
                        $query3                 =   $this->db->insert('MEMBER',$member);
                        $id_user                =   last_id('MEMBER','USERID');  
                        $this->db->where('ID', $id_form);
                        $query4 = $this->db->update('FORM', array('USERID'=>$id_user));                
                    /*  User insert end  */ 
                    $n++;  
                }
                return $n; 
            }            
    }

    public function formatData($data,$excelname){
        $msgStr='';

        foreach ($data as $key => $value) {

            if ($key > 2) {
                
                //计算总数，方便循环
                $numb = count($value) + 65;

                //数据过滤、检查
                $returnData = $this->excel->checkExcel($value,$excelname,$key);
                $value      = $returnData['arr'];
                if($returnData['errorMsg']==1){  // 报错
                    /* start */
                    $msgStr .= $returnData['msgStr']; 
                    /* end */
                }
               
                /* 从X轴F（ASCII码为70）位置开始 */
                for ($i = 70; $i < $numb; $i++) {

                    $charkey = chr($i);
                    //form、form_main数组的下标$j
                    $j = $i - 70;

                    if ($value['C'] == 'MAIN') {
                    //插入主表
                        if ($value['D'] == 'select') {
                            if ($value[$charkey]) {
                                //get_cons取得后缀ID
                                $form_main[$j][$value['B']] = get_cons($value[$charkey]);
                            } 
                            if ($data[$key+1][$charkey] && $data[$key+1]['D'] == 'other') {
                                //其他，自行填写的内容
                                $form_main[$j][$value['B']] = $data[$key+1][$charkey];
                            }                           
                        }
                        if ($value['D'] == 'input') {
                            $form_main[$j][$value['B']] = $value[$charkey];
                        }
                       
                        if ($value['D'] == 'number') {
                            $form_main[$j][$value['B']] = $value[$charkey];
                        }

                        if ($value['B'] == 'FORM') {
                            $form_main[$j]['TRADEID'] = getFormid($value[$charkey]);
                        }

                    }else{
                        //插入各个附表
                        if ($value['D'] == 'select') {
                            if ($value[$charkey]) {
                                $form[$j][$value['B']] = get_cons($value[$charkey]);
                            }   
                            if ($data[$key+1][$charkey] && $data[$key+1]['D'] == 'other') {
                                $arrSl = explode('$', $data[$key+1][$charkey]);

                                $qr['about']  = $arrSl[0];                               
                                if(!empty($arrSl[1])) $qr['about2'] = $arrSl[1];

                                $form[$j][$value['B']] = array2string($qr);
                            }                        
                        }
                        if ($value['D'] == 'input') {

                            $form[$j][$value['B']] = $value[$charkey];
                        }
                       
                        if ($value['D'] == 'number') {

                            $form[$j][$value['B']] = $value[$charkey];
                        }

                        if ($value['D'] == 'checkbox') {
                        //将checkbox组成数组，在转换成字符串
                            $arrCk = explode('$', $value[$charkey]);
                            if ($data[$key+1][$charkey] && $data[$key+1]['D'] == 'other') {
                                $arrCk['about'] = $data[$key+1][$charkey];
                            }
                            $form[$j][$value['B']] = array2string($arrCk);
                        }

                        /*
                         *  excel内A40至E45格式不能改变
                         */
                        if ($value['D'] == 'warrant') {
                            $id = !empty($value[$charkey])?get_cons($value[$charkey]):'';
                            if ($id == '135') {
                            //房地产抵押类型 
                                $trr    = !empty($data[$key+1][$charkey])?$data[$key+1][$charkey]:''; 
                                $arrW   = explode('$', $trr);                                    
                                $id2    = $arrW[0];
                                $arrWarrant             = array($id,$id2);
                                $arrWarrant['price']    = $arrW[1];
                                $form[$j][$value['B']]      = array2string($arrWarrant);
                            }
                            if ($id == '136') {
                            //担保公司担保类型           
                                $comw       = !empty($data[$key+1][$charkey])?$data[$key+1][$charkey]:'';
                                $comArr     = explode('$', $comw);
                                $arrWarrant = array($id);
                                $arrWarrant['com'] = $comArr;
                                $form[$j][$value['B']] = array2string($arrWarrant);
                               
                            }
                            if ($id == '137') {
                            //机器设备抵押                            
                                $price                  = !empty($data[$key+1][$charkey])?$data[$key+1][$charkey]:'';
                                $arrWarrant             = array($id);
                                $arrWarrant['price']    = $price;
                                $form[$j][$value['B']]      = array2string($arrWarrant);
                            }
                            if ($id == '138') {
                            //第三方公司担保 
                                $trr            = !empty($data[$key+1][$charkey])?$data[$key+1][$charkey]:'';
                                $arrW           = explode('$', $trr);                           
                                $price2         = $arrW[0];
                                $about          = $arrW[1];
                                $arrWarrant     = array($id);
                                $arrWarrant['price2']   = $price2;
                                $arrWarrant['about']    = $about;
                                $form[$j][$value['B']] = array2string($arrWarrant);
                            }
                            if ($id == '139') {
                            //质押物抵押
                                $trr            = !empty($data[$key+1][$charkey])?$data[$key+1][$charkey]:'';
                                $arrW           = explode('$', $trr); 
                                $good           = $arrW[0];
                                $price          = $arrW[1];
                                $arrWarrant     = array($id);
                                $arrWarrant['price']   = $price;
                                $arrWarrant['good']    = $good;
                                $form[$j][$value['B']] = array2string($arrWarrant);                   
                            }
                        }              
                    } 
                }                                       
            }
        }

        if(!empty($msgStr)){
            $returnData['msgStr']=$msgStr;
            return $returnData;
        }else{
            $formatdata['form']       = $form;
            $formatdata['form_main']  = $form_main;
            return $formatdata;   
        }

    }

    public function checkExcel($value,$excelname,$key){
        
                          
        foreach ($value as $k => $v) {   
            $arr[$k] = trim($v);
        }

        $numb = count($value) + 65;

        $type   = $arr['D'];
        $field  = $arr['B'];
        
        $msgStr = '';

        //从字母G开始检查值
        for ($i = 70; $i < $numb; $i++) {
            $charkey = chr($i);
            if ($arr[$charkey] !== '') {
            //不为空的时候检查

                if ($field == 'FORM') {
                    $arrForm = array('FORM_A','FORM_B','FORM_C','FORM_D','FORM_T');
                    if(!in_array($arr[$charkey], $arrForm)){
                        $msgStr .='<br />表单类型请勿随便更改。位置：'.$key.$charkey;
                    }
                }

                if ($field == 'LICENCE') {
                    if (!check_same('FORM','LICENCE',$arr[$charkey])) {
                        $msgStr .='<br />导入的公司申请表单已存在。位置：'.$key.$charkey;
                    }
                }                

                if ($type == 'input') {
                    $num  = strlen($arr[$charkey]);
                    if ($num > 300) {
                        $msgStr .='<br />EXCEL内'.$arr['A'].'字数超出指定长度（60），请返回检查修改。位置：'.$key.$charkey;
                    }
                }

                if ($type == 'number' && $field != 'LICENCE') {
                    $num  = strlen($arr[$charkey]);
                    if ($num > 200) {
                        $msgStr .='<br />EXCEL内'.$arr['A'].'字数超出指定长度（60），请返回检查修改。位置：'.$key.$charkey;
                    }
                    if(!(bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $arr[$charkey])){
                        $msgStr .='<br />EXCEL内'.$arr['A'].'必须填写数字(number)，请返回检查修改。位置：'.$key.$charkey;
                    }
                }                  
            }
           
        }
   
        $data['errorMsg']    = empty($msgStr) ? '' : 1; 
        $data['msgStr']      = $msgStr;
        $data['arr']         = $arr;  

        return $data;
    }

    public function getsons($value){

        $type = $value['D'];

        if ($type == 'input') {

            $arr[$value['B']] = $value['G'];
        }
        if ($type == 'number') {
            $arr[$value['B']] = $value['G'];
        }

        if ($type == 'select') {
            if ($value['G']) {
                $arr[$value['B']] = get_cons($value['G']);
            }
        }

        return $arr;
    }    
 

    //生成随机字符串
    private function _rand_str($length)
    {
        $str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
        return substr(str_shuffle($str),0,$length);
    }

    
    public function insertMemberExcel($data){
        $table='Z_MEMBER';
        $n=0;
        foreach($data as $k=>$v){
            if(trim($v['A'])==''){continue;}
            $member['USERNAME']     =   $v['A'];
            $passwd                 =   $v['B'];  //初始密码
            $encrypt                =   $this->_rand_str(4);
            $areaStrs               =   explode('_',$v['D']);
            $areaid                 =   $areaStrs[1];
            $member['PASSWD']       =   md5($passwd.$encrypt); 
            $member['ENCRYPT']      =   $encrypt;
            $member['COMPANY']      =   $v['C'];
            $member['EMAIL']        =   $v['F'];
            $member['MOBILE']       =   $v['G'];
            $member['LICENCE']      =   $v['E'];
            $member['REGDATE']      =   time();
            $member['AREAID']       =   (int)$areaid;
            $member['LASTDATE']     =   '';
            $member['STATUS']       =   '1';
            $result = $this->db->where('USERNAME',$member['USERNAME'])->or_where('LICENCE',$member['LICENCE'])->get($table)->result_array();
            
            if($result){continue;}
            else{
                $re=$this->db->insert($table,$member);
                if($re){$n++;}
            }
        }
        return $n;
    }


}