<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/


$route['member/register']		= "member/register/index";	// 会员
$route['member']				= "member/member/index/";	
$route['member/login']			= "member/member/login";
$route['member/logout']			= "member/member/logout";
$route['member/changepwd']		= "member/member/changePwd";
$route['member/reform']			= "member/member/reForm";
$route['member/showformdetail/(:num)']	= "member/member/showFormDetail/$1";
$route['member/newform']		= "member/member/addNewForm";
$route['member/formlist']		= "member/member/formList";

$route['processstatus/(:num)']		= "member/processstatus/index/$1";	//流程状态信息
$route['capt']					= 'member/captcha/index';	//验证码

$route['member/message']		= "member/member/message";   //站内信 列表页
$route['member/message/show']	= "member/member/showMessage";
$route['member/message/delete']	= "member/member/deletelMessage";
$route['member/message/read']   = "member/member/readMessage";
$route['member/setok']			= "member/member/setok";
$route['member/setno']			= "member/member/setno";
$route['member/sendsms']		= "member/member/sendsms";                  //发送短信controller




$route['apply']					= "apply/index/index/";		// 申请
$route['apply/step1']			= "apply/index/step1/";
$route['apply/step2']			= "apply/index/step2/";
$route['apply/step3']			= "apply/index/step3/";
$route['apply/stepex']			= "apply/index/stepex/";

$route['apply/attachmentup']	= "apply/upload/index/";
$route['apply/rmattachment']	= "apply/upload/rmattachment/";



$route['getu']					= "member/getuname/index";  //header.html 获取用户名

$route['contactus']				= "content/msg/index";			 //留言板

$route['news/(:num)']			= "content/lists/index/$1";		//新闻
$route['news/(:num)/(:num)']	= "content/lists/index/$1/$2";
$route['news/show/(:num)']		= "content/lists/show/$1";

$route['product/47']			= "content/lists/category_product/47";		// 产品
$route['product/(:num)']		= "content/lists/index/$1";
$route['product/(:num)/(:num)']	= "content/lists/index/$1/$2";
$route['product/show/(:num)']	= "content/lists/show/$1";


$route['activities/(:num)']		= "content/lists/index/$1";   // 活动
$route['activities/(:num)/(:num)']	= "content/lists/index/$1/$2";
$route['activities/show/(:num)']	= "content/lists/show/$1";

$route['page/(:num)']			= "content/page/index/$1";		// page
$route['search']				= "content/search/index";		// 搜索



$route['admin']					= "admin/index";				//后台

$route['default_controller']	= "content/index";
$route['fast']					= "content/index/fast";			//快捷通道

$route['404_override']      	= '';							
// $route['404_override']        = 'show404';


/* End of file routes.php */
/* Location: ./application/config/routes.php */