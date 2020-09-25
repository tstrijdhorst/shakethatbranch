<?php

use Cz\Git\GitRepository;
use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\commands\RemoveChildCommand;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$gitRepository   = new GitRepository(__DIR__);
$childRepository = new ChildRepository();

$addChildCommand    = new AddChildCommand(null, $gitRepository, $childRepository);
$removeChildCommand = new RemoveChildCommand(null, $gitRepository, $childRepository);

$application = new Application();
$application->addCommands([$addChildCommand, $removeChildCommand]);
$application->run();