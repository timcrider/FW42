<?php
/**
* Register common system level events to the 
*
*/

/**
* Register Event: Logging
* @todo Setup logging levels in the configuration and test them here
*/
FW42_Event::register(
	'log',
	function ($data) {
		if (is_array($data)) {
			print_r($data);
			$level = (!empty($data['Level'])) ? $data['Level'] : 'INFO';
			$message = (!empty($data['Message'])) ? $data['Message'] : 'No Message';
		} else {
			$level = 'INFO';
			$message = (string)$data;
		}

		$line = sprintf("%s [%s]: {$message}\n", date('Ymd:His'), $level, $message);
		@file_put_contents(BASEDIR.'logs/app.log', $line, FILE_APPEND);
	}
);

FW42_Event::register(
	'log.error',
	function ($data) {
		FW42_Event::trigger('log', array('Level' => 'ERROR', 'Message' => $data));
	}
);

FW42_Event::register(
	'log.debug',
	function ($data) {
		FW42_Event::trigger('log', array('Level' => 'DEBUG', 'Message' => $data));
	}
);

/**
* Register Event: 
*/
