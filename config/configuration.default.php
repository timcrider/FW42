<?php
/**
* Default Configuration
*/

/**
*
*/
$config->database = array(
	'adapter'    => 'Pdo_MySQL',
	'connection' => array(
		'host'     => '',
		'username' => '',
		'password' => '',
		'dbname'   => 'fw42',
		'profiler' => false,
		'driver_options' => array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
		)
	),
	'options'    => array(
		'quote_identifiers' => true
	)
);
