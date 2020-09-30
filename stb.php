<?php

use Cz\Git\GitRepository;
use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\commands\InitCommand;
use shakethatbranch\commands\ListChildrenCommand;
use shakethatbranch\commands\MergeIntoChildrenCommand;
use shakethatbranch\commands\Push;
use shakethatbranch\commands\RemoveChildCommand;
use shakethatbranch\repositories\ChildRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$gitRepository = new GitRepository(getcwd());
try {
	$gitRepository->getBranches();
}
catch (\Exception $e) {
	//An error here will trigger git to output `fatal: not a git repository`, this should be obvious enough
	exit(1);
}

$childRepository = new ChildRepository($gitRepository->getRepositoryPath());

$application = new Application();
$application->addCommands(
	[
		new InitCommand($gitRepository, $childRepository),
		new AddChildCommand($gitRepository, $childRepository),
		new RemoveChildCommand($gitRepository, $childRepository),
		new ListChildrenCommand($gitRepository, $childRepository),
		new MergeIntoChildrenCommand($gitRepository, $childRepository),
		new Push($gitRepository, $childRepository),
	]
);
$application->run();