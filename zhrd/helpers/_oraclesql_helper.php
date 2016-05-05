<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('oraclePage'))
{
   /**
     * 返回 limit 用法的 oracle SQL语句
     * 
     * @param  string $field     字段
     * @param  string $orderBy   排序字段
     * @param  string $table     表名，不加表前缀
     * @param  string $page      分页开始值
     * @param  string $pageSize  分页量
     * @return array             sql语句
	 */
	function oraclePage($field = '*',$orderBy,$table,$page,$pageSize)
	{
		return 
		'SELECT '.$field.' FROM 
				( SELECT ROWNUM AS rn, '.$field.'  FROM  
						( SELECT '.$field.' FROM '.databaseTable($table).' ORDER BY '.$orderBy.') 
				)
		WHERE rn >= '.$page.' AND rn < '.($pageSize+$page);
	}
}


	
	
	
//      SELECT "COUNT"(*) FROM Z_LINK





if(!function_exists('databaseTable'))
{
   /**
     * 返回数据库表全称  
     * 
     * @param  string $tb     数据库表 无表前缀
     * @return string		  数据库表 加表前缀
	 */

	function databaseTable($tb)
	{
		$CI = & get_instance();
		return $CI->db->dbprefix($tb);
	}

}

