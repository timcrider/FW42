<?php
/**
*
*/

/**
*
*/
abstract class FW42_Db_Table_Abstract extends Zend_Db_Table_Abstract {
	/**
	*
	*/
	protected $_rowsetClass = 'FW42_Db_Rowset_Abstract';

	/**
	*
	*/
	protected $_rowClass = 'FW42_Db_Row_Abstract';

	/**
	*
	*/
	protected $_backupTablePrefix = '_Changelog_';
	
	/**
	*
	*/
	protected $_backupTableExists = false;
	
	/**
	*
	*/
	public function __construct($config=array()) {
		parent::__construct($config);
		
		$this->backupTableExists();
	}
	
	/**
	*
	*/
	public function backupTableExists() {
		if (FW42_Db_Helper::tableExists($this->generateBackupTableName())) {
			$this->_backupTableExists = true;
		} else {
			$this->_backupTableExists = false;
		}
		
		return $this->_backupTableExists;
	}
	
	/**
	*
	*/
	public function generateBackupTableName() {
		return $this->_backupTablePrefix.$this->_name;
	}
	

	/**
	*
	*/
	public function generateBackupTableSQL($backupTable=NULL) {
		if (!$backupTable) {
			$backupTable = $this->generateBackupTableName();
		}

		// Setup our starting SQL
		$db          = $this->getAdapter();
		$tableSQL    = $db->quoteIdentifier($this->_name);
		$tableInfo   = $db->fetchRow("SHOW CREATE TABLE {$tableSQL}");
		$createSQL   = $tableInfo['Create Table'];

		// Prepare regex for removing auto increments/primary and unique keys that would fail the backup table
		$regs        = array(
			'/ auto_increment/',
			'/ AUTO_INCREMENT=([0-9]*)/',
			'/ AUTO_INCREMENT/',
			'/  PRIMARY KEY(.*)\)[^\r]/',
			'/  UNIQUE KEY(.*),[^\r]/'
		);

		$reps = array(
			'',
			'',
			'',
			'',
			''
		);
		
		// Break the SQL up into fields
		$createSQLNew = preg_replace($regs, $reps, $createSQL);
		$createStack  = preg_split("/(\r|\n|\r\n)/", trim($createSQLNew));
		$stackCount   = count($createStack);
		$backupStack  = array();
		
		for ($i = 0; $i < $stackCount; $i++) {
			if (preg_match('/^(CONSTRAINT|KEY)/', trim($createStack[$i]))) {
				continue;
			}
			
			if ($i == 0) {
				$backupStack[] = preg_replace("/".preg_quote($tableSQL)."/", $backupTable, $createStack[$i]);
				
				// Add new PK
				$idKey         = $db->quoteIdentifier('ID');
				$backupStack[] = "	{$idKey} MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,";

				// Commit Group
				$cGroupKey     = $db->quoteIdentifier('CommitGroupKey');
				$backupStack[] = "	{$cGroupKey} MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,";
				
				// Modification Time
				$modKey        = $db->quoteIdentifier('Modified');
				$backupStack[] = "	{$modKey} DATETIME NOT NULL,";
				
				// Modification Source IP
				$modSrcKey     = $db->quoteIdentifier('ModSource');
				$backupStack[] = "	{$modSrcKey} CHAR( 15 ) NOT NULL DEFAULT 'unknown',";
				
				// Modification Type
				$modTypeKey    = $db->quoteIdentifier('ModType');
				$backupStack[] = "	{$modTypeKey} CHAR( 25 ) NOT NULL DEFAULT 'unknown',";
				
				// Modification User
				$modUserKey    = $db->quoteIdentifier('ModUser');
				$backupStack[] = "	{$modUserKey} CHAR( 100 ) NOT NULL DEFAULT 'unknown',";
			} elseif ($i == ($stackCount-2)) {
				$backupStack[] = "\t".preg_replace('/(,$|UNIQUE)/', '', trim($createStack[$i]));
			} else {
				$line = trim($createStack[$i]);
				
				if ($line == '') {
					continue;
				} elseif (preg_match('/^\)/', $line)) {
					$tmp = array_pop($backupStack);
					$tmp = preg_replace('/,$/', '', trim($tmp));
					$backupStack[] = $tmp;
					$backupStack[] = $line;
				} else {
					$backupStack[] = "\t".$line;
				}
			}
		}
		
		return implode("\n", $backupStack).';';
	}
	
	/**
	*
	*/
	public function createBackupTable($backupTable=NULL) {
		if (!$backupTable) {
			$backupTable = $this->generateBackupTableName();
		}

		if (FW42_Db_Helper::tableExists($backupTable)) {
			throw new Exception("Unable to create backup table, because a table named '{$backupTable}' already exists.");
		}
	
		$sql = $this->generateBackupTableSQL($backupTable);
		$db  = $this->getAdapter();

		$db->query($sql);
		
		return true;
	}
	
	/**
	*
	*/
	public function dropBackupTable($backupTable=NULL) {
		if (!$backupTable) {
			$backupTable = $this->generateBackupTableName();
		}

		if (!FW42_Db_Helper::tableExists($backupTable)) {
			throw new Exception("Unable to drop backup table, because a table named '{$backupTable}' does not exist.");
		}
		
		$db          = $this->getAdapter();
		$tableSQL    = $db->quoteIdentifier($backupTable);
		$db->query("DROP TABLE {$tableSQL}");
	}

	/**
	*
	*/
	public function insert($data, $type=NULL, $commitGroup=0) {
		$id = parent::insert($data);
		
		// @todo: add in quick check for table exists
		if (is_array($id)) {
			$params = array();

			foreach ($this->_primary AS $v) {
				$params[] = array($id[$v]);
			}
		
			$current = call_user_func_array(array($this, 'find'), $params);
		} else {
			$current = $this->find($id);
		}
		
		if (!$current->valid()) {
			// @todo: Throw an exception here?
			return $id;
		}
		
		$current = $current->current();
		
		if (!method_exists($current, 'backupRecord')) {
			return $id;
		}
		
		if (!$type) {
			$type = 'insert';
		}

		$current->backupRecord($type, $commitGroup);
		
		return $id;
	}

}
