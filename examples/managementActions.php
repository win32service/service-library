<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

use Win32Service\Model\ServiceIdentifier;
use Win32Service\Service\ServiceStateManager;

include __DIR__.'/../vendor/autoload.php';

// Init the service identifier
$serviceId = ServiceIdentifier::identify(
	isset($argv[2])? $argv[2]:'my_test_service', //By default use the service name 'my_test_service'
	'localhost'
);

echo 'Service : '.$serviceId."\n";

$actions = ['start', 'stop', 'pause', 'continue'];

if (!isset($argv[1]) || !in_array($argv[1], $actions)) {
    die("Usage ".$argv[0]." ".implode('|', $actions)." [serviceId]\n");
}

$actionManager = new ServiceStateManager();

try {
	switch ($argv[1]) {
		case 'start':
			$actionManager->startService($serviceId);
			break;
		case 'stop':
			$actionManager->stopService($serviceId);
			break;
		case 'pause':
			$actionManager->pauseService($serviceId);
			break;
		case 'continue':
			$actionManager->continueService($serviceId);
			break;
		
		default:
			throw new \Exception("Unknown action ".$argv[1], 1);
			break;
	}
	printf('The action %s is success', $argv[1]);
} catch(Exception $e) {
    echo 'Error : ('.$e->getCode().') '.get_class($e).': '.$e->getMessage()."\n";
}
