<?php
    class Excel_model extends CI_Model{
        function __consturct(){
            parent::__construct();
        }

    public function read($filepath=''){echo '';exit();
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        date_default_timezone_set('Asia/Shanghai');

        if (!file_exists($filepath)) {
            exit("文件不存在.\n");
        }


        if(!is_writeable($filepath)){
            exit("没有写权限");
        }

        $objPHPExcel = PHPExcel_IOFactory::load($filepath);

        $dataArr     = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        return $dataArr;   
    }

    public function export($table,$fieldArr,$where='',$header=''){
        $query  = $this->db->where($where);
        //$query    = $this->db->order_by('LISTORDER','ASC');
        $query  = $this->db->get($table);
        $data   = $query->result_array();
        
        if(empty($data)){msg('没有数据','-1');}
        if(!empty($header)){
            $this->excelObj->createHeader($header);
            $this->excelObj->getActivesheet->setTitle($header);
        }
        
        foreach($data as $k=>$v){

            /**/
        }
        $filename=date('Y_m_d_H_i_s',time());
        header ( "Content-Type: application/force-download" );
        header ( "Content-Type: application/octet-stream" );
        header ( "Content-Type: application/download" );
        header ( 'Content-Disposition:inline;filename="'.$filename.'"' );
        header ( "Content-Transfer-Encoding: binary" );
        header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header ( "Pragma: no-cache" );
        $objWriter = PHPExcel_IOFactory::createWriter ($this->excelObj, 'Excel5' );
        $objWriter->save ( 'php://output' );
    }

    /*
    *   $fieldArr   对应的存储字段
    *   $filepath   excel存放路径
    *
    */
    public function import($table,$fieldArr='',$filepath,$startRow='',$tNum=''){
        if (!file_exists($filepath)) {
            exit("文件不存在.");
        }
        $data   = $this->read($filepath);
        $i=$n='';
        foreach($data as $k=>$v){
            $n++;
            if($n<$startRow){continue;}
            $arr    = array_combine($fieldArr,$v);
            $re     = $this->db->insert($table,$arr);
            if($re){$i++;}
        }
        return $i;

    }
    
    
    
}