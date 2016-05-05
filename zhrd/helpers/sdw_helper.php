<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// $str  like  '/appdata/zhrd/2013/12/asdasfasfas.jpg'
function getFileName($str){
	$str = explode( '/', $str);
	$str = end( $str);
	$str = explode( '.', $str);
	return $str[0];
}


/*生成下载链接 
param $filePath 文件实际地址
param $formId   表单id

*/
function getDownloadLink($filePath,$formId){
	return site_url('admin/download?fp='.getFileName($filePath).'&fi='.$formId );
}


if ( ! function_exists('str_cut'))
{
   
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {

	$CI =& get_instance();

	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower( $CI->config->item('charset') ) == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}


}

function rand_str($length = 4, $captcha = false){
	$str = 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890';
	if($captcha) $str = 'QWERTYUIPASDFGHJKLZXCVBNMqwertyuipasdfghjklzxcvbnm123456789';
	return substr(str_shuffle($str),0,$length);
}



function return_password($str = 'boczhrd123', $encrypt = 'sdw123'){
	$encrypt = rand_str(4);

	// return array(
	// 	'password' => md5($str.$encrypt),
	// 	'encrypt' =>  $encrypt
	// );
	
	return array(
		'password' => md5($str.$encrypt),
		'encrypt' =>  $encrypt
	);

}


function img($path){
	if($path == '')
		return base_url('/statics/imgs/default_img.jpg');
	return base_url($path);
}
	
	
function strAddslashes( $str, $force = 0, $strip = FALSE)
{
	if (!get_magic_quotes_gpc()) {
		// if( is_array($str) ){
			// foreach( $str as $k => $v)
				// $str[$k]  = addslashes($v);
		// }
		return addslashes($str);
	} else {
	return $str;
	}

	// return $str;
		// if( !MAGIC_QUOTES_GPC ||$FORCE){
			
		// }
}
