<?php

use shakethatbranch\commands\AddChildCommand;
use shakethatbranch\commands\CreateChildCommand;
use shakethatbranch\commands\InitCommand;
use shakethatbranch\commands\ListChildrenCommand;
use shakethatbranch\commands\MergeIntoChildrenCommand;
use shakethatbranch\commands\PushCommand;
use shakethatbranch\commands\RemoveChildCommand;
use shakethatbranch\exceptions\GitRepositoryNotInitializedException;
use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\system\GitRepository;
use Symfony\Component\Console\Application;

require_once __DIR__.'/vendor/autoload.php';

$gitRepository = new GitRepository(getcwd());
try {
	$gitRepository->getRepositoryRoot();
}
catch (GitRepositoryNotInitializedException $e) {
	echo 'Git repository not initialized, please run `git init`'.PHP_EOL;
	exit(1);
}

$childRepository = new ChildRepository($gitRepository->getRepositoryRoot());

$application = new Application();
$application->addCommands(
	[
		new InitCommand($gitRepository, $childRepository),
		new AddChildCommand($gitRepository, $childRepository),
		new CreateChildCommand($gitRepository, $childRepository),
		new RemoveChildCommand($gitRepository, $childRepository),
		new ListChildrenCommand($gitRepository, $childRepository),
		new MergeIntoChildrenCommand($gitRepository, $childRepository),
		new PushCommand($gitRepository, $childRepository),
	]
);
$application->run();