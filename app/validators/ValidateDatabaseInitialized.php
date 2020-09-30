<?php

namespace shakethatbranch\validators;

use shakethatbranch\repositories\ChildRepository;
use shakethatbranch\exceptions\STBException;
use shakethatbranch\exceptions\ValidationException;

class ValidateDatabaseInitialized {
	/**
	 * @var ChildRepository
	 */
	private ChildRepository $childRepository;
	
	private function __construct(ChildRepository $childRepository) {
		$this->childRepository = $childRepository;
	}
	
	public static function create(ChildRepository $childRepository): self {
		return new self($childRepository);
	}
	
	public function validate() {
		if (!$this->childRepository->databaseFileExists()) {
			throw new class extends ValidationException {
				public function __construct() {
					parent::__construct('No stb repository initialized. Please run `init` command first.');
				}
			};
		}
	}
}