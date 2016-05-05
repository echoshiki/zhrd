<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Pick {

/*
** 模块：新闻采集（一财网、经济参考网）
** 作者：shiki
** 返回: 数据数组（标题、时间、文章内容）
** 方式：CURL，正则匹配
** 时间：2014年8月
** 备注：改版失效
*/

	public function pick_1($url="http://www.yicai.com/money/fund/"){
	    date_default_timezone_set('Asia/Shanghai');
	    $ch  = curl_init(); 
	    curl_setopt ($ch, CURLOPT_URL,$url); 
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    //指定ipv4
	    if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
			curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		}
	    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5); 
	    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION ,1);    
	    curl_setopt ($ch, CURLOPT_ENCODING ,'utf8'); 
		$content 	= curl_exec($ch); 
	    preg_match_all("/<li\s*class=\"newsStyleLine01\"><h2><a\s*target=\"_blank\" href=\"(.*html)\".*>(.*)<\/a><\/h2><h3>发布时间：(.*)<\/h3>.*\n*.*<\/li>/",$content,$match); 
	    $urlArr		= $match[1];    //链接地址
	    $titleArr	= $match[2];    //标题
	    foreach ($match[3] as $key => $value) { $dateArr[] = strtotime($value); }   //时间
	    foreach ($urlArr as $key => $value) {
	     	$url = "http://www.yicai.com".$value;
			$ch  = curl_init(); 
			curl_setopt ($ch, CURLOPT_URL,$url); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);     
			curl_setopt ($ch, CURLOPT_ENCODING ,'utf8'); 
			$content 	= curl_exec($ch); 
			ini_set('pcre.backtrack_limit', 800000);
			preg_match_all("/<div\s*class=\"articleTitle\">\s*<h1>(.*)<\/h1>\s*<\/div>[\s\S]*<span class=\"iconCbn f14blue2\"[><a]*[\s\S]*?>(.*)<\/span>\s*<\/a><\/h2>[\s\S]*<div class=\"articleContent\">[\s\S]*<span\s*class=\"first-letter\">(.*)<\/span>[\s\S]*<div class=\"left-clear\"\/><\/div>\s*<!-- video -->([\s\S]*)<!--加入投票swf samson 2010-1-18 16:09-->/",$content,$matchs);

			$fromArr[]    = $matchs[2];   //来源			
	        $content      = trim($matchs[4][0]);
	        $contentArr[] = substr_replace($content,$matchs[3][0],3,0);             
	     } 
	    foreach ($urlArr as $key => $value) {
	        $articleArr[$key]['title']      = trim($titleArr[$key]);
	        //$articleArr[$key]['url']        = $value;
	        //$articleArr[$key]['from']       = trim($fromArr[$key][0]);
	        $articleArr[$key]['date']       = $dateArr[$key];
	        $articleArr[$key]['content']    = trim($contentArr[$key]);
	        $articleArr[$key]['fromsite']   = '1';
	    }
	    return $articleArr;
	} 

	public function pick_2($url="http://www.jjckb.cn/list_gn.htm",$num='15'){ 
	    date_default_timezone_set('Asia/Shanghai');
	    $ch  = curl_init(); 
	    curl_setopt ($ch, CURLOPT_URL,$url); 
	    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);     
	    curl_setopt ($ch, CURLOPT_ENCODING ,'utf');  
	    $content    = curl_exec($ch); 

	    preg_match_all("/<td width=\"72%\" height=\"25\" align=\"left\" valign=\"middle\"><a href=([_|a-z|0-9|\/|-]*.htm)/",$content,$match); 
	    $urlArr = $match[1];   //链接地址
	    foreach ($urlArr as $key => $value) {
	        if($key < $num){
	            $url = "http://www.jjckb.cn/".$value;
	            $ch  = curl_init(); 
	            curl_setopt ($ch, CURLOPT_URL,$url); 
	            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
	            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);     
	            curl_setopt ($ch, CURLOPT_ENCODING ,'gbk'); 
	            $content    = curl_exec($ch); 
	            preg_match_all('/<td align="center" valign="bottom" class="black18" height="40">\n*\s*(.*)/',$content,$match);
	            $articleArr[$key]['title'] = mb_convert_encoding(trim($match[1][0]), "UTF-8", "GBK");   //标题

	            preg_match_all('/<td align="center" valign="middle" class="black12">\n*\s*(.*)\n*\s*.*\n*\s*(.*)\n*\s*.*\n*\s*(.*)/',$content,$match);
	            $articleArr[$key]['date'] = strtotime($match[1][0]);
	            //$articleArr[$key]['articler'] = $match[2];   //作者
	            //$articleArr[$key]['from']     = $match[3];   //来源
	            preg_match_all('/<div id="newsdetail-content-text">\n*\s*<!--enpcontent-->\n*\s*<!--enpcontent-->([\s\S]*)<!--\/enpcontent-->\n*\s*<!--\/enpcontent-->/',$content,$match);
	            $articleArr[$key]['content'] = mb_convert_encoding(trim($match[1][0]), "UTF-8", "GBK");
	        	$articleArr[$key]['fromsite']   = '2';
	        }
	    }
	    return $articleArr;
	}

}