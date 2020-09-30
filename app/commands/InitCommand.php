<?php

namespace shakethatbranch\commands;

use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\system\GitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {
	protected static $defaultName = 'init';
	private GitRepository $gitRepository;
	private ChildRepository $childRepository;
	
	public function __construct(GitRepository $gitRepository, ChildRepository $childRepository) {
		parent::__construct();
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Initializes stb in this repo');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		if ($this->childRepository->databaseFileExists()) {
			$output->writeln('Already initialized');
			return Command::FAILURE;
		}
		
		$this->childRepository->initializeDatabase();
		$output->writeln('Initialized stb repository in '.$this->childRepository->getDatabasePath());
		
		return Command::SUCCESS;
	}
}