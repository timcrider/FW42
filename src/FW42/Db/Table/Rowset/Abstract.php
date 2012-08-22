<?php
/**
*
*/

/**
*
*/
abstract class FW42_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract {
	/**
	*
	*/
	public function toArray($extended=false) {
		$out = array();
		
		foreach ($this AS $row) {
			$out[] = $row->toArray($extended);
		}
		
		return $out;
	
	}
}
