<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\validators\ValidateDatabaseInitialized;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
		$this->addOption('recursive',['r'],InputOption::VALUE_NONE, 'Merge children of children');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$this->mergeChildren($currentBranchName, $input->hasOption('recursive'));
		
		return Command::SUCCESS;
	}
	
	/**
	 * @param string $currentBranchName
	 * @param bool   $recursive
	 * @throws \Cz\Git\GitException
	 */
	protected function mergeChildren(string $currentBranchName, bool $recursive = false): void {
		ValidateDatabaseInitialized::create($this->childRepository)->validate();
		
		try {
			foreach ($this->childRepository->findChildren($currentBranchName) as $childBranchName) {
				$this->gitRepository->checkout($childBranchName);
				$this->gitRepository->merge($currentBranchName);
				
				if ($recursive && $this->childRepository->hasAnyChildren($childBranchName)) {
					$this->mergeChildren($childBranchName, true);
				}
			}
		}
		finally {
			$this->gitRepository->checkout($currentBranchName);
		}
	}
}