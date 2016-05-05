<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


 /**
   * ueditor 编辑器图片上传动作
   * 
   *
   */
class editorUp extends adminBase{

    public function __construct()
    {
		header("Content-Type: text/html; charset=utf-8");
        parent::__construct();		
    }


	//附件上传
    public function fileUp()
    {
        if($_POST )
        {
            $this->load->library('Uploader','','editorUp');

    		//上传配置
    	    $config = array(
    	        "savePath" => "uploads/editor/" , //保存路径
    	        "allowFiles" => array( ".rar" , ".doc" , ".docx" , ".zip" , ".pdf" , ".txt" ) , //文件允许格式
    	        "maxSize" => 100000 //文件大小限制，单位KB
    	    );
    	    //生成上传实例对象并完成上传
            $this->editorUp->initialize("upfile", $config);
    	    /**
    	     * 得到上传文件所对应的各个参数,数组结构
    	     * array(
    	     *     "originalName" => "",   //原始文件名
    	     *     "name" => "",           //新文件名
    	     *     "url" => "",            //返回的地址
    	     *     "size" => "",           //文件大小
    	     *     "type" => "" ,          //文件类型
    	     *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
    	     * )
    	     */
    	    $info = $this->editorUp->getFileInfo();

    	    /**
    	     * 向浏览器返回数据json数据
    	     * {
    	     *   'url'      :'a.rar',        //保存后的文件路径
    	     *   'fileType' :'.rar',         //文件描述，对图片来说在前端会添加到title属性上
    	     *   'original' :'编辑器.jpg',   //原始文件名
    	     *   'state'    :'SUCCESS'       //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
    	     * }
    	     */
    	    echo '{"url":"' .$info[ "url" ] . '","fileType":"' . $info[ "type" ] . '","original":"' . $info[ "originalName" ] . '","state":"' . $info["state"] . '"}';
            
        }else{
            show_404();
        }
    }


//图片上传
   public function imgUp()
    {
        if( $_POST )
        {
            $this->load->library('Uploader','','editorUp');

            //上传图片框中的描述表单名称，
            $title = htmlspecialchars($_POST['pictitle'], ENT_QUOTES);
            $path = htmlspecialchars($_POST['dir'], ENT_QUOTES);

            //上传配置
            $config = array(
                // "savePath" => ($path == "1" ? "upload/" : "upload1/"),
                 // "savePath" => '/uploads/editor/',
                 "savePath" => 'uploads/editor/',

                "maxSize" => 500, //单位KB
                "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp", ".gif")
            );

            //生成上传实例对象并完成上传
            // $up = new Uploader("upfile", $config);
            $this->editorUp->initialize("upfile", $config);
            /**
             * 得到上传文件所对应的各个参数,数组结构
             * array(
             *     "originalName" => "",   //原始文件名
             *     "name" => "",           //新文件名
             *     "url" => "",            //返回的地址
             *     "size" => "",           //文件大小
             *     "type" => "" ,          //文件类型
             *     "state" => ""           //上传状态，上传成功时必须返回"SUCCESS"
             * )
             */
            $info = $this->editorUp->getFileInfo();

            /**
             * 向浏览器返回数据json数据
             * {
             *   'url'      :'a.jpg',   //保存后的文件路径
             *   'title'    :'hello',   //文件描述，对图片来说在前端会添加到title属性上
             *   'original' :'b.jpg',   //原始文件名
             *   'state'    :'SUCCESS'  //上传状态，成功时返回SUCCESS,其他任何值将原样返回至图片上传框中
             * }
             */
            echo "{'url':'" . $info["url"] . "','title':'" . $title . "','original':'" . $info["originalName"] . "','state':'" . $info["state"] . "'}";

        }else{
            show_404();
        }
    }

//图片列表
    public function imageManager()
    {
        if($_POST )
        {
            //需要遍历的目录列表，最好使用缩略图地址，否则当网速慢时可能会造成严重的延时
            $paths = array('uploads/editor');

            $action = htmlspecialchars( $_POST[ "action" ] );
            if ( $action == "get" ) {
                $files = array();
                foreach ( $paths as $path){
                    $tmp = $this->_getfiles( $path );
                    if($tmp){
                        $files = array_merge($files,$tmp);
                    }
                }
                if ( !count($files) ) return;
                rsort($files,SORT_STRING);
                $str = "";
                foreach ( $files as $file ) {
                    $str .= $file . "ue_separate_ue";
                }
                echo $str;
            } 

        }else{
            show_404();
        }

    }


    /**
     * ueditor  $this->imageManage 需求
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function _getfiles( $path , &$files = array() )
    {
        if ( !is_dir( $path ) ) return null;
        $handle = opendir( $path );
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                $path2 = $path . '/' . $file;
                if ( is_dir( $path2 ) ) {
                    $this->_getfiles( $path2 , $files );
                } else {
                    if ( preg_match( "/\.(gif|jpeg|jpg|png|bmp)$/i" , $file ) ) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }

  

}