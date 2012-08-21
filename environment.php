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
	BASEDIR.'vendor/ZendFramework-1.11.11/library/'
);

$sep = (!defined('PHP_OS') || !preg_match('/^WIN/', PHP_OS)) ? ':' : ';';
ini_set('include_path', implode($sep, $pathing));

try {
	if (!file_exists(BASEDIR.'vendor/ZendFramework-1.11.11/library/Zend/Loader/Autoloader.php')) {
		throw new Exception("Unable to find Zend_Autoloader");
	}

	require_once 'Zend/Loader/Autoloader.php';
	$autoloader = Zend_Loader_Autoloader::getInstance();
	$autoloader->setFallbackAutoloader(true);

	// Register events
	require_once BASEDIR.'config/events.php';

} catch (Exception $e) {
	print "Error loading environment: ".$e->getMessage()."\n";
	exit(1);
}
