<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

use Win32Service\Model\ServiceInformations;
use Win32Service\Model\ServiceIdentifier;
use Win32Service\Service\ServiceAdminManager;

include __DIR__.'/../vendor/autoload.php';

//Init the service informations
$serviceInfos = new ServiceInformations(
    ServiceIdentifier::identify('my_test_service', 'localhost'),
    'PHP service for tests',
    'This service does not compute anything, just prove the concept.',
    __DIR__.DIRECTORY_SEPARATOR.'service.php',
    ''
);

if (!isset($argv[1]) || ($argv[1] !== 'register' && $argv[1] !== 'unregister')) {
    die("Usage ".$argv[0]." register|unregister\n");
}

$adminService = new ServiceAdminManager();

//Register the service
if ($argv[1] === 'register') {
    try {
        $adminService->registerService($serviceInfos);
        echo 'Registration success';
    } catch(Exception $e) {
        echo 'Error : ('.$e->getCode().') '.$e->getMessage()."\n";
    }
}

//Unregister the service
if ($argv[1] === 'unregister') {
    try {
        $adminService->unregisterService($serviceInfos);
        echo 'Unregistration success';
    } catch(Exception $e) {
        echo 'Error : ('.$e->getCode().') '.$e->getMessage()."\n";
    }
}
