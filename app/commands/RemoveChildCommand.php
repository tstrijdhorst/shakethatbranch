<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveChildCommand extends Command {
	protected static $defaultName = 'remove-child';
	/**
	 * @var IGit
	 */
	private $gitRepository;
	/**
	 * @var ChildRepository
	 */
	private ChildRepository $childRepository;
	
	public function __construct(string $name = null, IGit $gitRepository, ChildRepository $childRepository) {
		parent::__construct($name);
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Removes the given branch-name as a child of the current branch');
		$this->addArgument('branchName', InputArgument::REQUIRED, 'The name of the childbranch');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$childBranchName   = $input->getArgument('branchName');
		
		if (!$this->childRepository->hasChild($currentBranchName, $childBranchName)) {
			$output->writeln('That branch is not a child of the current branch.');
			return Command::FAILURE;
		}
		
		if (!in_array($childBranchName, $this->gitRepository->getLocalBranches())) {
			$output->writeln('That is not a valid branch name.');
			return Command::FAILURE;
		}
		
		$this->childRepository->removeChild($currentBranchName, $childBranchName);
		
		return Command::SUCCESS;
	}
}