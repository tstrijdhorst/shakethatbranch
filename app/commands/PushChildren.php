<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushChildren extends Command {
	protected static $defaultName = 'push-children';
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
		
		$this->setDescription('Pushes all the children to origin');
		$this->addOption('recursive',['r'],InputOption::VALUE_NONE, 'Push children of children');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$this->pushChildren($currentBranchName, $input->hasOption('recursive'));
		
		return Command::SUCCESS;
	}
	
	/**
	 * @param string $currentBranchName
	 * @param bool   $recursive
	 * @throws \Cz\Git\GitException
	 */
	protected function pushChildren(string $currentBranchName, bool $recursive = false): void {
		try {
			foreach ($this->childRepository->findChildren($currentBranchName) as $childBranchName) {
				$this->gitRepository->checkout($childBranchName);
				$this->gitRepository->push();
				
				if ($recursive && $this->childRepository->hasAnyChildren($childBranchName)) {
					$this->pushChildren($childBranchName, true);
				}
			}
		}
		finally {
			$this->gitRepository->checkout($currentBranchName);
		}
	}
}