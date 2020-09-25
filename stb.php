<?php

use Cz\Git\GitRepository;
use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\commands\InitCommand;
use shakethatbranch\commands\ListChildrenCommand;
use shakethatbranch\commands\MergeIntoChildrenCommand;
use shakethatbranch\commands\PushChildren;
use shakethatbranch\commands\RemoveChildCommand;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

//@todo throw exception if the current directory is not a git initialized directory
$gitRepository   = new GitRepository(getcwd());
$childRepository = new ChildRepository($gitRepository->getRepositoryPath());

$application = new Application();
$application->addCommands(
	[
		new InitCommand($gitRepository, $childRepository),
		new AddChildCommand($gitRepository, $childRepository),
		new RemoveChildCommand($gitRepository, $childRepository),
		new ListChildrenCommand($gitRepository, $childRepository),
		new MergeIntoChildrenCommand($gitRepository, $childRepository),
		new PushChildren($gitRepository, $childRepository),
	]
);
$application->run();