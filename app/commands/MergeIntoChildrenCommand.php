<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeIntoChildrenCommand extends Command {
	protected static $defaultName = 'merge-into-children';
	/**
	 * @var IGit
	 */
	private $gitRepository;
	/**
	 * @var ChildRepository
	 */
	private ChildRepository $childRepository;
	
	public function __construct(IGit $gitRepository, ChildRepository $childRepository) {
		parent::__construct();
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Merges the current branch into it\'s children');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		
		try {
			foreach($this->childRepository->findChildren($currentBranchName) as $childBranchName) {
				$this->gitRepository->checkout($childBranchName);
				$this->gitRepository->merge($currentBranchName);
			}
		}
		finally {
			$this->gitRepository->checkout($currentBranchName);
		}
		
		return Command::SUCCESS;
	}
}