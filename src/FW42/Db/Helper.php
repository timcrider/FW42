<?php
/**
*
*/

/**
*
*/
class FW42_Db_Helper {
	/**
	*
	*/
	protected static $db;
	
	/**
	*
	*/
	public static function getAdapter() {
		if (!static::$db) {
			static::$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		}
		
		return static::$db;
	}

	/**
	*
	*/
	public static function tableExists($table=NULL) {
		if (!$table) {
			return false;
		}
		
		$db = static::getAdapter();

		return in_array($table, $db->listTables());
	}

	/**
	*
	*/
	public static function tableDescription($table=NULL) {
		if (!$table) {
			return false;
		}
		
		$db = static::getAdapter();
		
		return $db->describeTable($table);
	}

	/**
	*
	*/
	public static function renderTableModel($tableName, $options=NULL) {
		$table = new Zend_Db_Table($tableName);

		if (!$tableInfo = $table->info()) {
			throw new Exception("Unable to create table class. '{$tableName}' does not exist");
		}

		// options must be an instance of FW42_Base. Makes E_NOTICE much easier
		if ($options && !is_a($options, 'FW42_Base')) {
			throw new Exception("Options must be an instance of 'FW42_Base'");
		}
		
		if (!$options) {
			$options = new FW42_Base;
		}

		if (empty($tableInfo['primary'])) {
			throw new Exception("Cannot create a model for a table without a primary key");
		}

		// Generate the classname and start our model object		
		$className = ($options->className) ? $options->className : "Model_".ucfirst($tableName);

		$newClass = new Zend_CodeGenerator_Php_Class();
		$newClass->setName($className);
		$newClass->setIndentation(4);
		$newClass->setExtendedClass('FW42_Db_Table_Abstract');

		// Setup the table model properties
		$props = array(
			array(
				'name'         => '_name',
				'visibility'   => 'protected',
				'defaultValue' => $tableName
			)
		);

		// @todo: Move this to a new object?
		// Add header docblock
		$doc = new Zend_CodeGenerator_Php_Docblock(
			array(
				'shortDescription' => "{$className} Datatype (Generated by Framework 42)",
				'longDescription'  => "Class generated on: " . date("Y-m-d H:i:s"),
				'tags' => array(
					array(
						'name'        => 'todo',
						'description' => 'Auto generated class, some modfication may be required'),
					array(
						'name'        => 'version',
						'description' => '0.0.1'
					)
				)
			)
		);
		
		$newClass->setDocBlock($doc);
		
		$prim = array_values($tableInfo['primary']);
		
		if (count($prim) > 1)  {
			$props[] = array(
				'name'         => '_primary',
				'visibility'   => 'protected',
				'type'         => 'array',
				'defaultValue' => $prim
			);
		} else {
			$props[] = array(
				'name'         => '_primary',
				'visibility'   => 'protected',
				'defaultValue' => array_shift($prim)
			);
		}
		
		// Build rowset object
		if (!$options->skipRowset) {
			$rowsetClassName = ($options->rowsetClassName) ? $options->rowsetClassName : "Model_".ucfirst($tableName)."_Rowset";

			// Attach this object to the table model			
			$props[] = array(
				'name'         => '_rowsetClass',
				'visibility'   => 'protected',
				'defaultValue' => $rowsetClassName
			);
			
			// Generate the row set model
			$newRowsetClass = new Zend_Codegenerator_Php_Class();
			$newRowsetClass->setName($rowsetClassName);
			$newRowsetClass->setIndentation(4);
			$newRowsetClass->setExtendedClass('FW42_Db_Table_Rowset_Abstract');
			$newRowsetClass->setDocblock(new Zend_CodeGenerator_Php_Docblock);
		}

		// Build row object
		if (!$options->skipRow) {
			$rowClassName = ($options->rowClassName) ? $options->rowClassName : "Model_".ucfirst($tableName)."_Row";

			// Attach this object to the table model			
			$props[] = array(
				'name'         => '_rowClass',
				'visibility'   => 'protected',
				'defaultValue' => $rowClassName
			);
			
			// Generate the row model
			$newRowClass = new Zend_Codegenerator_Php_Class();
			$newRowClass->setName($rowClassName);
			$newRowClass->setIndentation(4);
			$newRowClass->setExtendedClass('FW42_Db_Table_Row_Abstract');
			$newRowClass->setDocblock(new Zend_CodeGenerator_Php_Docblock);
		}
		
		// Apply the properties to the table model
		$newClass->setProperties($props);
		$src = "<?php\n".$newClass->generate();
		
		if (isset($newRowsetClass)) {
			$src .= "\n\n".$newRowsetClass->generate();
		}

		if (isset($newRowClass)) {
			$src .= "\n\n".$newRowClass->generate();
		}
		
		return $src;
	}
	
	/**
	*
	*/
	public static function modelFileExists($tableName) {
		$tableName = preg_replace('/_/', '/', $tableName);
		return file_exists(BASEDIR."src/Model/{$tableName}.php");
	}
	
	/**
	*
	* @todo: This is a hackey mess, need to put tigther controls on this.
	* @todo: Need to add in options object
	*/
	public static function generateTableModel($table) {
		if (!$src = static::renderTableModel($table)) {
			return false;
		}
		
		$tableName = preg_replace('/ /', '/', ucwords(preg_replace('/_/', ' ', $table)));
		$modelFull = BASEDIR."src/Model/{$tableName}.php";
		$modelDir  = dirname($modelFull);
		
		// @todo: This is sloppy, need to come back and clean this up
		if (!is_dir($modelDir)) {
			mkdir($modelDir, 0777, true);
		}
		
		file_put_contents($modelFull, $src);
		
		return true;
	}

}
