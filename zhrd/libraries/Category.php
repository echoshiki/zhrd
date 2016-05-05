<?php
	class Category{
		public function getTree($pid=''){
			$sql='select * from CATEGORY ';
			
			if($pid==''){
					

			}else{
				$sql.= "where PID='".$pid."'";
				
			}
			return $tree;
		}

		public function getCategory($id){

			return $result;
		}

		public function getTopId(){

			return $result;
		}

		public function getChild(){

			return $result;
		}



	}
?>