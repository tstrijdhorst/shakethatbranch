<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\system\ChildRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListChildrenCommand extends Command {
	protected static $defaultName = 'list-children';
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
		
		$this->setDescription('Lists the children of the current branch');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		
		foreach ($this->childRepository->findChildren($currentBranchName) as $childBranchName) {
			$output->writeln($childBranchName);
		}
		
		return Command::SUCCESS;
	}
}