<?php
/**
*
*/

/**
*
*/
abstract class FW42_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract {

	/**
	*
	*/
	public function toArray($extended=false) {
		$out = parent::toArray();
		
		if (!$extended) {
			return $out;
		}

		// Custom array extension code goes here
		
		return $out;
	}

	/**
	*
	*/
	public function backupRecord($type=NULL, $commitGroup=0) {
		if (!$this->_table->backupTableExists()) {
			return false;
		}
	
		$commitGroup = (int)$commitGroup;
		$data        = $this->_cleanData;
		
		// @todo: Make Modified more cross db friendly
		$data['Modified']  = new Zend_Db_Expr('NOW()');

		$data['ModSource'] = (empty($_SERVER['REMOTE_ADDR'])) ? "shell" : $_SERVER['REMOTE_ADDR'];
		$data['ModType']   = (!empty($type)) ? strtolower($type) : 'unknown';
		
		// @todo: Make the user an actual CMS User
		$data['ModUser'] = 'unknown';
		
		$this->_table->getAdapter()->insert($this->_table->generateBackupTableName(), $data);
		return $this->_table->getAdapter()->lastInsertId();
	}
	
	/**
	*
	*/
	public function save($type=NULL, $commitGroup=0) {
		if (!$type) {
			$type = 'save';
		}
		
		$this->backupRecord($type, $commitGroup);
		return parent::save();
	}
	
	/**
	*
	*/
	public function delete($type=NULL, $commitGroup=0) {
		if (!$type) {
			$type = 'delete';
		}
		
		$backupId = $this->backupRecord($type, $commitGroup);
		parent::delete();
		return $backupId;
	}
	
	
}
