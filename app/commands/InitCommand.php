<?php

namespace shakethatbranch\commands;

use Cz\Git\IGit;
use shakethatbranch\repositories\ChildRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command {
	protected static $defaultName = 'init';
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
		
		$this->setDescription('Initializes stb in this repo');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		if ($this->childRepository->databaseFileExists()) {
			$output->writeln('Already initialized');
			return Command::FAILURE;
		}
		
		$this->childRepository->initializeDatabase();
		
		return Command::SUCCESS;
	}
}