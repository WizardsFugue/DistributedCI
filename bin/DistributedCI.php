#!/usr/bin/env php
<?php

require __DIR__ . '/../bootstrap.php';

$basepath = __DIR__ .'/../';

use Symfony\Component\Console\Application;

$application = new Application();
$application->add( new \WizardsFugue\DistributedCI\Command\BackupCreate() );
$application->add( new \WizardsFugue\DistributedCI\Command\Execute() );
$application->add( new \WizardsFugue\DistributedCI\Command\Generate() );
$application->run();
