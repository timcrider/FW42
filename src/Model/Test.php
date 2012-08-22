<?php
/**
*
*/

/**
*
*/
class Model_Test extends FW42_Db_Table_Abstract {
	protected $_name        = 'Test';
	protected $_primary     = 'TestKey';
	protected $_rowsetClass = 'Model_Test_Rowset';
	protected $_rowClass    = 'Model_Test_Row';
}

/**
*
*/
class Model_Test_Rowset extends FW42_Db_Table_Rowset_Abstract {
}

/**
*
*/
class Model_Test_Row extends FW42_Db_Table_Row_Abstract {
}
