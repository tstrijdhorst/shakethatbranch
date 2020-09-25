<?php

use Cz\Git\GitRepository;
use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\commands\MergeIntoChildrenCommand;
use shakethatbranch\commands\RemoveChildCommand;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$gitRepository   = new GitRepository(__DIR__);
$childRepository = new ChildRepository();

$application = new Application();
$application->addCommands(
	[
		new AddChildCommand($gitRepository, $childRepository),
		new RemoveChildCommand($gitRepository, $childRepository),
		new MergeIntoChildrenCommand($gitRepository, $childRepository),
	]
);
$application->run();