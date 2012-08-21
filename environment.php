<?php
/**
* Framework 42 Environment
*
* This file sets up the working environment for both web and shell scripts.
*
* - Setup PHP Options
* - Setup Framework constants
*   - BASEDIR = the base directory of the framework
* - Setup our autoloader pathing
* - Fetch the ZF Autoloader and set it as the default
* - Register system events
*
* - Exit on failure to successfully setup the environment
*/

// Force php options
@ini_set('magic_quotes_gpc', 'Off');

// Framework Constants
define('START_TIME', microtime(1));
define('BASEDIR', dirname(__FILE__).'/');

// Pathing
$pathing = array(
	BASEDIR.'src/',
	BASEDIR.'vendor/ZendFramework-1.11.11/library/',
	BASEDIR.'vendor/PEAR/'
);

$sep = (!defined('PHP_OS') || !preg_match('/^WIN/', PHP_OS)) ? ':' : ';';
ini_set('include_path', implode($sep, $pathing));

// Configuration File
$tryConfigFile = BASEDIR.'config/configuration.php';

if (!file_exists($tryConfigFile) || !is_readable($tryConfigFile)) {
	print "Unable to load configuration file '{$tryConfigFile}'.\n";
	exit(1);
}

try {
	if (!file_exists(BASEDIR.'vendor/ZendFramework-1.11.11/library/Zend/Loader/Autoloader.php')) {
		throw new Exception("Unable to find Zend_Autoloader");
	}

	require_once 'Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	// Setup global registry
	$reg = Zend_Registry::getInstance();

	// Load configuration
	$config = new FW42_Base;
	require_once $tryConfigFile;
	$reg->config = $config;

	// Database connection
	try {
		$reg->db = Zend_Db::factory(
			$config->database['adapter'],
			$config->database['connection'],
			$config->database['options']
		);
		
		Zend_Db_Table_Abstract::setDefaultAdapter($reg->db);

	} catch (Exception $e) {
		print "Unable to connect to database: ".$e->getMessage()."\n";
		exit(1);
	}

	// Register events
	require_once BASEDIR.'config/events.php';
} catch (Exception $e) {
	print "Error loading environment: ".$e->getMessage()."\n";
	exit(1);
}
