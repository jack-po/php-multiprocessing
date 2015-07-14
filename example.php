#!/usr/bin/env php
<?php

use JackPo\MultiProcessing\Manager;
use JackPo\MultiProcessingExamples\Workers;
use JackPo\MultiProcessingExamples\Listeners;

error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

$Listener = new Listeners\MonteCarlo();
$Manager = new Manager(include __DIR__ . '/config/app.php');

$Manager->addWorker(new Workers\MonteCarlo(100000), $pcocesses_count = 10);
$Manager->addListener($Listener);
$Manager->run();

printf("Pi is about: %f", $Listener->getAnswer());

/*
 * Usage with standalone worker script
 */
//$Manager = new Manager();
//$Manager->addWorker(new Workers\Script('/path/to/script.php'), $pcocesses_count = 5);
//$Manager->run();