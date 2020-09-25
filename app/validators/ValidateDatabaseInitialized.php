<?php

namespace shakethatbranch\validators;

use shakethatbranch\system\ChildRepository;
use shakethatbranch\system\STBException;
use shakethatbranch\system\ValidationException;

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
					parent::__construct('Not initialized');
				}
			};
		}
	}
}