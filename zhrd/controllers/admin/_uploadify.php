<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by JetBrains PhpStorm.
 * User: zyc
 * Date: 13-7-6
 * Time: 下午6:31
 * To change this template use File | Settings | File Templates.
 */

// class uploadify extends adminBase {
class uploadify extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
		$this->load->model('upload_model');
    }

    public function excelUp()
    {
        if($_POST)
        {
            $message = $this->upload_model->excelUp();

            if( $message)
            {
                echo $message;
            }else{ show_404(); }
        }else{
            show_404();
        }

    }
	
    public function imgUp()
    {
        if($_POST)
        {
            $message = $this->upload_model->imageUp();

            if( $message)
            {
                echo $message;
            }else{
                show_404();
            }
			
			
        }else{
            show_404();
        }

    }

	
}