<?php

use Cz\Git\GitRepository;
use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$gitRepository   = new GitRepository(__DIR__);
$childRepository = new ChildRepository();

$addChildCommand = new AddChildCommand(null, $gitRepository, $childRepository);

$application = new Application();
$application->addCommands([$addChildCommand]);
$application->run();