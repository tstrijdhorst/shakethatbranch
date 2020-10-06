<?php

namespace shakethatbranch\commands;

use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\system\GitRepository;
use shakethatbranch\validators\ValidateDatabaseInitialized;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateChildCommand extends Command {
	protected static $defaultName = 'create-child';
	private GitRepository $gitRepository;
	private ChildRepository $childRepository;
	
	public function __construct(GitRepository $gitRepository, ChildRepository $childRepository) {
		parent::__construct();
		$this->gitRepository   = $gitRepository;
		$this->childRepository = $childRepository;
	}
	
	protected function configure() {
		parent::configure();
		
		$this->setDescription('Creates a new branch as a child of the current branch and check it out.');
		$this->addArgument('branchName', InputArgument::REQUIRED, 'The name of the childbranch');
		$this->addOption('allow-master', 'm', InputOption::VALUE_NONE, 'Allow to set master as a child of the current branch (dangerous).');
		$this->addOption('no-checkout', null, InputOption::VALUE_NONE, 'Do not checkout the created childbranch after creating');
		$this->setAliases(['c']);
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		ValidateDatabaseInitialized::create($this->childRepository)->validate();
		
		$currentBranchName = $this->gitRepository->getCurrentBranchName();
		$childBranchName   = $input->getArgument('branchName');
		
		if ($this->gitRepository->branchExists($childBranchName)) {
			$output->writeln('Child branch already exists, use child-add');
			return Command::FAILURE;
		}
		
		if ($childBranchName === '') {
			$output->writeln('Child branch cannot be named `-`');
			return Command::FAILURE;
		}
		
		if ($childBranchName === 'master' && !$input->getOption('allow-master')) {
			$output->writeln('Cannot add `master` as a child of this branch. Use `--allow-master` if you are really sure you want this.');
			return Command::FAILURE;
		}
		
		$checkoutBranch = $input->getOption('no-checkout') === false;
		$this->gitRepository->createBranch($childBranchName, $checkoutBranch);
		$this->childRepository->addChild($currentBranchName, $childBranchName);
		
		return Command::SUCCESS;
	}
}