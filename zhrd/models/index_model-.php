<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class index_model extends CI_Model {
	
	public $setting        = array();
	
	//构造的URL前缀 根据config/route.php 路由修改
	public $routeUri = array(
						'pageUrl'        => '/page/',
						'newsListUrl'    => '/news/',
						'newsShowUrl'    => '/news/show/',
						'productListUrl' => '/product/',
						'productShowUrl' => '/product/show/',
						'searchUrl'      => '/search',
						'sitemapUrl'     => '/setmap/'
	);

	public $changjianwenda = '81';				//常见问答ID	列表页 单页 内容页
	public $shouyepics     = '102';				//首页图片切换	ID
	public $kuaiSuTongdao  = array();			//融资快速通道
	public $contactUs      = '49'; 				//联系我们 ID

	//面包屑 array
	private $breadcrumbsArr = array();
	
    public function __construct()
    {
        parent::__construct();
		$this->setting = $this->getSetting();

		$this->kuaiSuTongdao['jibenjieshao']  = $this->routeUri['pageUrl'].'61';	//智慧网贷基本介绍
		$this->kuaiSuTongdao['kehugaozhishu'] = $this->routeUri['pageUrl'] . '62';	//智慧网贷客户告知书
		$this->kuaiSuTongdao['rongzhiUrl']    = 'apply';							//融资 URL
		
	}

	/**
	 * 加载view header.html 如果是文章 or 列表页page 传递 meta array
	 * @param array $meta array('contentTitle'=>,'categoryTitle'=>,'keywords'=>,'description'=>,)
	 *
	 * @return load view 
	 */
    public function header($meta = array(), $jscss = array())
    {		
		$data = array();
        $this->load->model('linkage_model',"linkage");
		//banner
		$query = $this->db->select('CATID,CATNAME,TYPE,PARENTID,LISTORDER')->where('ISBANNER',1)->order_by('LISTORDER DESC')->get('CATEGORY')->result_array();
		$query = $this->getCategoryUrl($query);
		$data['banner'] = $this->array_child($query);
		//融资申请
		$data['rongzishengqing'] = $this->kuaiSuTongdao['rongzhiUrl'];
		
		$query = $this->db->select('ID,CATID,TYPEID,TITLE')->where('POSIDS','1')->order_by('UPDATETIME DESC')->get('CONTENT')->result_array();
		$data['tuijian'] = $this->getShowUrl($query);

		
		//setting meta serach etc.
		$data['setting'] = $this->setting;
		$data['searchURL']  = $this->routeUri['searchUrl'];

		//setting extra js css
		if( !empty($jscss['jquery'])){
			$data['jquery'] = $jscss['jquery'];
		}
		if( !empty($jscss['js'])){
			$data['js'] = $jscss['js'];
		}
		if( !empty($jscss['css'])){
			$data['css'] = $jscss['css'];
		}
		
		//设置下面变量 前台将会显示 不设置显示默认setting里面的值
		if(!empty( $meta) ){
			$data['meta'] = $meta;		
		}

		
		$data['isNewNews'] = false;
		//智慧资讯 添加 New 三天内更新过信息  259200 (s)三天
		$wtf_num = $this->db->where_in('CATID',array(48,83,84) )->where('UPDATETIME >=', ( time() - 259200 )) ->count_all_results('CONTENT') ;
		if( $wtf_num >= 1){
			$data['isNewNews'] = true;
		}

		
		
		$this->load->view('content/header.html', $data);
	}

    public function footer()
    {
		$data = array();
		
		$data['footUrl'] = array(
						'sitemap'          => $this->routeUri['sitemapUrl'],
						'banquanshengming' => $this->routeUri['pageUrl'] . '74',				//版权声明
						'shiyong '         => $this->routeUri['pageUrl'] . '75',				//使用条款
						'contactUs'        => $this->routeUri['pageUrl'] . $this->contactUs		//联系我们
		);
		
		$data['setting'] = $this->setting;
		$this->load->view('content/footer.html',$data);
	}
	
	/**
	 * 列表页 左侧页面视图
	 *
	 * @return string load view 后的字符串
	 */	
	public function categoryLeft()
	{
		$data = array();
		
		$query = $this->db->select('CATID,CATNAME,TYPE')->where('PARENTID' ,'48')->get('CATEGORY')->result_array();
		$data['categoryList'] = $this->getCategoryUrl($query);

		$data['changjianwenda'] = $this->routeUri['newsListUrl'].$this->changjianwenda; //常见问答URL

		$data['kuaiSuTongdao'] = $this->kuaiSuTongdao;
		return $this->load->view('content/categoryLeft.html',$data,true);
	}
	
	/**
	 * show页面 右侧视图
	 *
	 * @param string $id adid
	 * @return string load view 后的字符串
	 */
	public function showRight()
	{
		$data = array();
		
		//热点新闻
		$query = $this->db->select('ID,CATID,TITLE,TYPEID')->where('POSIDS' ,'1')->where('CATID','84')->limit('6')->get('CONTENT')->result_array();
		// print_r($query);
		// print_r($query);exit;
		$data['hotNews'] = $this->getShowUrl($query,2);
		//常见问答URL
		$data['changjianwenda'] = $this->routeUri['newsListUrl'].$this->changjianwenda; 
		
		$data['kuaiSuTongdao'] = $this->kuaiSuTongdao;
		return $this->load->view('content/showRight.html',$data,true);
	}
	
	/**
	 * 面包屑导航
	 * @param array  栏目列表
	 * @param string 当前栏目ID
	 *
	 * @return array 当前栏目ID
	 */
	public function breadcrumbs($queryCategory,$catid)
	{
		$categoryOfCatid = array();
		foreach($queryCategory as $key => $value){
			if($value['CATID'] == $catid){
				$categoryOfCatid = $value;
				break;
			}
		}
		
		$this->_breadcrumbs($queryCategory,$categoryOfCatid);
		return $this->getCategoryUrl($this->breadcrumbsArr);
	}
	//面包屑
	private function _breadcrumbs($query,$catArray)
	{
		foreach($query as $k => $v){
			if( $v['CATID'] == $catArray['PARENTID'] ){
				$this->_breadcrumbs($query, $v);
				return array_push($this->breadcrumbsArr,$catArray);
			}
			if($catArray['PARENTID'] == 0 ){
				return array_push($this->breadcrumbsArr,$catArray);
			}
		}
	}
	
	/**
	 * 根据ad adid 获取图片列表
	 *
	 * @param string $id adid
	 * @return array
	 */
	public function getAD( $id = '' )
	{
		if( $id == '') return $data;
		
		$query = $this->db->select('ITEMID,URL,ADID,URLTO')->where('ADID',$id)->get('AD_ITEM') ->result_array();
		// print_r($query);exit;
		return $query;
	}
	
	/**
	 * 禁止直接访问 $directory 目录下的controller
	 *
	 * @param string $directory controller目录下的目录名
	 * @return show_404() or not
	 */
	public function accessBarred($directory = 'content')
	{
		if( $this->uri->segment(1) == $directory )
			show_404();
		// if( $this->uri->segment(1) == 'apply' )
			// show_404();
		if( $this->uri->segment(2) == 'member' || $this->uri->segment(2) == 'register')
			show_404();
	}
	
	
	/*
	 *获取setting表中 type==1 值
	 * @return string
	 */
	private function getSetting()
	{
		$data = array();
		$query = $this->db->select('KEY,VALUE')->where('TYPE','1')->get('SETTING')->result_array();
		foreach( $query as $key => $value)
		{
			$data[ $value['KEY'] ] = $value['VALUE']; 
		}
		return $data;
	}
	
	/**
     * 子栏目放在父栏目的 child 中           这个函数需要排序
	 * @param $query array   带有id  parentid的二位数组
	 * @param $query string  id字段名
	 * @param $query string  parentid 字段名
	 * @param $query string  child 添加的array child 名称
	 *
	 * @return $query array  带有child的array 多维数组
	 *
	 * $query示例
	 * Array
	 * (
	 *     [0] => Array
	 *         (
	 *             [CATID] => 47
	 *             [PARENTID] => 0
	 *         )
	 * 
	 *     [1] => Array
	 *         (
	 *             [CATID] => 48
	 *             [PARENTID] => 47
	 *         )
	 * 
	 *     [2] => Array
	 *         (
	 *             [CATID] => 49
	 *             [PARENTID] => 0
	 *         )
	 * )
	 */
	public function array_child($query = array(),$id = 'CATID',$parent = 'PARENTID',$child = 'CHILD')
	{
		$copyQuery = $query;
        foreach($copyQuery as $key => $value)
        {
            //查找子栏目
            if($value[$parent] != '0')
            {
                //循环数组
                $i = -1;
                while( isset( $query[++$i]) )
                {
                    if($query[$i][ $id ] == (int)$value[$parent] )
                    {
						// echo $query[$i][ $id ];
						// echo '                        ';
						// echo $value[$parent];
                        //添加child key
                        if( !array_key_exists( $child, $query[$i]) )
                        {
                            $query[$i][ $child ] = array();
                        }
                        //子栏目 array_puhs 父栏目child key 中
                        // array_push($query[$i][$child], $value);
						$query[$i][$child][] = $value;
                        // unset($query[$key]);
						// $query[$key] = array();
                    }
                }
            }
        }
		foreach($query as $key => $value){
			if($value[$parent] != 0){
			unset($query[$key]);
			}
		}
		return $query;
	}
	
	
	
	/**
     *  array 添加child KEY 值
	 * @param $query array   带有id  parentid的二位数组
	 * @param $query string  id字段名
	 * @param $query string  parentid 字段名
	 * @param $query string  child 添加的array child 名称
	 *
	 */
	public function addChindKey($query = array(),$id = 'CATID',$parent = 'PARENTID',$child = 'CHILD')
	{
		foreach($query as $key => $value){
			foreach($query as $k => $v)
			{
				if( !array_key_exists( $child, $query[$key]) ){
					$query[$key][$child ] = '';
				}
				if( $v[$parent] == $value[$id]){

					$query[$key][$child] .= $v[$id] .' ';

					continue;
				}
			}
		}

		return $query;
	}

   /**
	 *根据 $query 里面type构造URL 栏目页
	 *
	 * @param $query array
	 * @return $query array  带有URL的array
	 */
	public function getCategoryUrl($query)
	{
		foreach( $query as $k => $v ){
			// 1是列表     0 是单页
			switch ( $v['TYPE'] ) {
				case 0: $query[$k]['URL'] = $this->routeUri['pageUrl'].$v['CATID']; break;
				case 1: $query[$k]['URL'] = $this->routeUri['productListUrl'].$v['CATID']; break;
				case 2: $query[$k]['URL'] = $this->routeUri['newsListUrl'].$v['CATID']; break;
			}
		}
		return $query;
	}
	
   /**
	 *根据$type构造URL show页面 列表页
	 *
	 * @param $query array
	 * @param $type int  1 ==> product   2==> news
	 * @return $query array  带有URL的array
	 */
	public function getShowUrl($query,$type = 1)
	{
		foreach( $query as $k => $v ){
			switch ( $v['TYPEID'] ) {
				case 1: $query[$k]['URL'] = $this->routeUri['productShowUrl'].$v['ID']; break;
				case 2: $query[$k]['URL'] = $this->routeUri['newsShowUrl'].$v['ID']; break;
			}
		}
		return $query;
	}
	
	
}