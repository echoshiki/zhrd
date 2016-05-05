<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class Content_model extends CI_Model{
		function __consturct(){
			parent::__construct();
			
		}
		public function getList($cid=''){
			$result='';
			 $query	= $this->db->order_by('ID','DESC');
			 $query	= $this->db->get_where('CONTENT',array('CATID'=>$cid));	
			 $result = $query->result_array();
			return $result;	
		}

		public function encode_html($data){
			$ndata  ='';
			$ndata	= htmlspecialchars($data, ENT_QUOTES);
			return $ndata;
		}



		public function decode_html($data){
			$ndata  ='';
			$ndata	= htmlspecialchars_decode($data, ENT_QUOTES);
			return $ndata;
		}


		public function insert($str,$id){
			$inArr=$this->getCutArr($str);
			foreach($inArr as $k=>$v){
				if($k==0){
					$sql="insert into  Z_CONTENT_DATA (ID,CONTENT) values ('".$id."','".$v."') ";		
				}else{
					$sql="UPDATE Z_CONTENT_DATA SET CONTENT = CONTENT || '".$v."' WHERE ID ='".$id."' ";				
				}
				$this->db->query($sql);
				if(!$re){ continue; }
			}
			return true;
		}

		// public function insert($str,$id){
		// 	$inArr = $this->getCutArr($str);
		// 	print_r($inArr);
		// 	echo "++++++++++++++++++++++++<br/>";
		// 	// foreach($inArr as $k=>$v){
		// 	// 	if($k==0){
		// 	// 		echo $sql="INSERT INTO Z_CONTENT_DATA (ID,CONTENT) VALUES ('".$id."','$v') ";		
		// 	// 	}else{
		// 	// 		$sql="UPDATE Z_CONTENT_DATA SET CONTENT = CONTENT || '".$v."' WHERE ID ='".$id."' ";
		// 	// 	}
		// 	// 	$this->db->query($sql);
		// 	// 	if(!$re){ return false; }
		// 	// }
		// 	return true;
		// }

		public function insertPage($str,$id){
			$inArr=$this->getCutArr($str);
			$n=0;
			//print_r($inArr);exit();
			foreach($inArr as $k=>$v){
				$n++;
				if($n==1){
					$sql="insert into  Z_PAGE (CATID,CONTENT) values ('".$id."','".$v."') ";
					
				}else{
					$sql="UPDATE Z_PAGE SET CONTENT = CONTENT || '".$v."' WHERE CATID ='".$id."' ";
					
				}
				$re=$this->db->query($sql);
				if(!$re){return false;}
			}
			return true;
		}

		public function update($str,$id){
			$inArr=$this->getCutArr($str);
			$n=0;
			foreach($inArr as $k=>$v){

				$n++;
				if($n==1){
					$sql="UPDATE Z_CONTENT_DATA SET CONTENT = '".$v."' WHERE ID ='".$id."' ";
					
				}else{
					$sql="UPDATE Z_CONTENT_DATA SET CONTENT = CONTENT || '".$v."' WHERE ID ='".$id."' ";
					
				}
				$re=$this->db->query($sql);
				if(!$re){return false;}
			}
			return true;
		}


		public function updatePage($str,$id){
			$inArr=$this->getCutArr($str);
			$n=0;
			foreach($inArr as $k=>$v){

				$n++;
				if($n==1){
					$sql="UPDATE Z_PAGE SET CONTENT = '".$v."' WHERE CATID ='".$id."' ";
					
				}else{
					$sql="UPDATE Z_PAGE SET CONTENT = CONTENT || '".$v."' WHERE CATID ='".$id."' ";
					
				}
				$re=$this->db->query($sql);
				if(!$re){return false;}
			}
			return true;
		}


		public function getCutArr($str){
			$len=strlen($str);
			$cutlen='3900';
			if($len>$cutlen){
				// $narr[]=substr($str,0,$cutlen);
				// $nowStr=substr($str,$cutlen);
				// $narr2=$this->getCutArr($nowStr);
				// $narr=array_merge($narr,$narr2);

				$narr[]=substr($str,0,$cutlen);
				$nowStr=substr($str,$cutlen);
				$narr2=$this->getCutArr($nowStr);
				$narr=array_merge($narr,$narr2);

			}else{
				$narr[]=$str;
			}
			
			return $narr;
		}

		// function getCutArr($str,$charset="utf8") {
		//   $strlen=mb_strlen($str);
		//   while($strlen){
		//     $array[]=mb_substr($str,0,2000,$charset);
		//     $str=mb_substr($str,2000,$strlen,$charset);
		//     $strlen=mb_strlen($str);
		//   }
		//   return $array;
		// }

		function cutstr($string, $length) {
	        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);  		$wordscut=$j='';
	        for($i=0; $i<count($info[0]); $i++) {
	                $wordscut .= $info[0][$i];
	                $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
	                //if ($j > $length - 3) {
	                //        return $wordscut." ...";
	                //}
	        }
	        return join('', $info[0]);
		}





/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
		public function str_cut($string, $length, $dot = '') {
			$strlen = strlen($string);
			if($strlen <= $length) return $string;
			$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
			$strcut = '';
			if(strtolower(CHARSET) == 'utf-8') {
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
?>