<?php

namespace shakethatbranch\commands;

use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\system\GitRepository;
use shakethatbranch\validators\ValidateDatabaseInitialized;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushCommand extends Command {
	protected static $defaultName = 'push';
	private GitRepository $gitRepository;
	private ChildRepository $childRepository;
	
	public function __construct(GitRepository $gitRepository, ChildRepository $childRepository) {
		parent::__construct();
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Push the current branch to origin');
		$this->addOption('recursive',['r'],InputOption::VALUE_NONE, 'Push children and children of children');
		$this->setAliases(['p']);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		ValidateDatabaseInitialized::create($this->childRepository)->validate();
		
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$this->gitRepository->checkout($currentBranchName);
		$this->gitRepository->push();
		
		if ($input->getOption('recursive')) {
			$this->pushChildren($currentBranchName);
		}
		
		return Command::SUCCESS;
	}
	
	/**
	 * @param string $branch
	 * @throws \Cz\Git\GitException
	 */
	protected function pushChildren(string $branch): void {
		try {
			foreach ($this->childRepository->findChildren($branch) as $childBranchName) {
				$this->gitRepository->checkout($childBranchName);
				$this->gitRepository->push();
				
				if ($this->childRepository->hasAnyChildren($childBranchName)) {
					$this->pushChildren($childBranchName);
				}
			}
		}
		finally {
			$this->gitRepository->checkout($branch);
		}
	}
}