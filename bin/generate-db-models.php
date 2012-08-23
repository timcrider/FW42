<?php
/**
*
*/
require_once dirname(__FILE__).'/bin-config.php';


try {
	print "Creating Models For Tables\n".str_repeat('-', 30)."\n";
	$tables = $reg->db->listTables();
	
	foreach ($tables AS $table) {
		if (preg_match('/^_Changelog_/', $table)) {
			print " Skipping: {$table} (Changelog table detected)\n";
			continue;
		}

		// Process table
		print " -> {$table}\n";
		$objName = "Model_".ucfirst($table);
		
		// If model file doesn't exist, generate one
		if (!FW42_Db_Helper::modelFileExists($table)) {
			try {
				FW42_Db_Helper::generateTableModel($table);
				print "     -> Created Table Object '{$objName}'\n";
			} catch (Exception $e) {
				print "[FAIL] ".$e->getMessage()."\n";
				continue;
			}
		}
		
		// Try to create the object and test for backup table
		try {
			$obj = new $objName;
			if (!$obj->backupTableExists()) {
				$obj->createBackupTable();
				print "     -> Creating Backup Table\n";
			}
		} catch (Exception $e) {
			print "    -> Error Creating Backup Table: ".$e->getMessage()."\n";
		}
	}

	print "\n!!!!!!!!!! REMEMBER TO COMMIT YOUR NEW MODELS TO VERSION CONTROL !!!!!!!!\n\n";


	print str_repeat('-', 30)."\nProcess Complete\n";
	
} catch (Exception $e) {
	print "Error: ".$e->getMessage()."\n";
	exit(1);
}
