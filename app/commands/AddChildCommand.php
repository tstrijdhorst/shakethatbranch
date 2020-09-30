<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\validators\ValidateDatabaseInitialized;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddChildCommand extends Command {
	protected static $defaultName = 'add-child';
	/** @var IGit */
	private IGit $gitRepository;
	/** @var ChildRepository */
	private ChildRepository $childRepository;
	
	public function __construct(IGit $gitRepository, ChildRepository $childRepository) {
		parent::__construct();
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Adds the given branch-name as a child of the current branch');
		$this->addArgument('branchName', InputArgument::REQUIRED, 'The name of the childbranch');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		ValidateDatabaseInitialized::create($this->childRepository)->validate();
		
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$childBranchName   = $input->getArgument('branchName');
		
		if ($currentBranchName === $childBranchName) {
			$output->writeln('A branch cannot be a child of itself.');
			return Command::FAILURE;
		}
		
		if ($this->childRepository->hasChild($currentBranchName, $childBranchName)) {
			$output->writeln('That branch is already a child of the current branch.');
			return Command::FAILURE;
		}
		
		if (!in_array($childBranchName, $this->gitRepository->getLocalBranches())) {
			$output->writeln('That is not a valid branch name.');
			return Command::FAILURE;
		}
		
		$this->childRepository->addChild($currentBranchName, $childBranchName);
		
		return Command::SUCCESS;
	}
}