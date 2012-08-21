<?php
/**
*
*/
require_once dirname(__FILE__).'/bin-config.php';

print "\n\nEnvironment loaded\n\n";

// Custom test event register
FW42_Event::register('test', function ($data) { print "\n----- Testing data -----\n"; print_r($data); print "\n-----\n\n"; });
FW42_Event::trigger('test', "Triggering the test");

// Testing event logging
FW42_Event::trigger('log', "Logging Test");
FW42_Event::trigger('log.error', "Logging Error Test");
FW42_Event::trigger('log.debug', "Logging Debug Test");


print "\nEvents run successful\n\n";
