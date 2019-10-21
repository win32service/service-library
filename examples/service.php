<?php
/**
 * This file is part of Win32Service Library package
 * @copy Win32Service (c) 2018-2019
 * @author "MacintoshPlus" <macintoshplus@mactronique.fr>
 */

use Win32Service\Model\ServiceIdentifier;
use Win32Service\Model\AbstractServiceRunner;

include __DIR__.'/../vendor/autoload.php';

if (php_sapi_name() !== 'cli') {
	trigger_error('This script cannot be run in '. php_sapi_name() .' SAPI.', E_USER_ERROR);
	exit(1);
}

class MyTestService extends AbstractServiceRunner
{

    public function __construct(ServiceIdentifier $serviceIdentifier)
    {
        parent::__construct();
        $this->setServiceId($serviceIdentifier);
    }

    /**
     * This function is runnning in loop. The running duration is limited to 30 seconds
     * @param int $control contains the last control.
     * @return void
     */
    protected function run(int $control): void
    {
    	usleep(1000);
    	//trigger_error('Main run', E_USER_NOTICE);
    }

    /**
     * Implement this function setup the service before run loop
     */
    protected function setup(): void
    {
    	trigger_error('Setup', E_USER_NOTICE);
    	usleep(100);
    	//Init database connexion or other connexion
    }

    /**
     * Implement this function for run short code before service entering in pause status
     */
    protected function beforePause(): void
    {
    	//Flush database transaction and close cleanlly the connexion, save state
    	usleep(100);
    	trigger_error(__METHOD__, E_USER_NOTICE);
    }

    /**
     * Implement this function for run short code before service continue (after pause)
     */
    protected function beforeContinue(): void
    {
    	//Reopen the databases connexion and restore state
    	usleep(100);
    	trigger_error(__METHOD__, E_USER_NOTICE);
    }

    /**
     * Implement this function for run short code before service entering in stop status
     */
    protected function beforeStop(): void
    {
    	//Close all connexion and save state.
    	usleep(100);
    	trigger_error(__METHOD__, E_USER_NOTICE);
    }

    /**
     * Implement this function for tun short code if the main run code is too slow
     */
    protected function lastRunIsTooSlow(float $duration): void
    {
    	trigger_error('The last run duration time is too slow '.$this->lastRunDuration(), E_USER_ERROR);
    }
}

$serviceId = ServiceIdentifier::identify('my_test_service', 'localhost');

// init and run the service
(new MyTestService($serviceId))->doRun(100);
