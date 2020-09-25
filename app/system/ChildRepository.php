<?php

namespace shakethatbranch\system;

class ChildRepository {
	const DATABASE_PATH = __DIR__.'/../../db.json';
	
	public function __construct() {
		if (!$this->databaseFileExists()) {
			$this->initializeDatabase();
		}
	}
	
	public function addChild(string $parentBranchName, string $childBranchName): void {
		$database = $this->getDatabase();
		
		$database[$parentBranchName][] = $childBranchName;
		
		$this->persistDatabase($database);
	}
	
	public function removeChild(string $parentBranchName, string $childBranchName) : void {
		$database = $this->getDatabase();
		unset($database[$parentBranchName][array_search($childBranchName, $database[$parentBranchName])]);
		$this->persistDatabase($database);
	}
	
	/**
	 * @param string $parentBranchName
	 * @return string[]
	 */
	public function findChildren(string $parentBranchName) : array {
		if (!$this->hasAnyChildren($parentBranchName)) {
			return [];
		}
		
		return $this->getDatabase()[$parentBranchName];
	}
	
	public function hasAnyChildren(string $parentBranchName) : bool {
		$database = $this->getDatabase();
		
		return isset($database[$parentBranchName]);
	}
	
	public function hasChild(string $parentBranchName, string $childBranchName): bool {
		if (!$this->hasAnyChildren($parentBranchName)) {
			return false;
		}
		
		$database = $this->getDatabase();
		
		return in_array($childBranchName, $database[$parentBranchName]);
	}
	
	private function getDatabase(): array {
		$json = file_get_contents(self::DATABASE_PATH);
		return json_decode($json, true);
	}
	
	private function persistDatabase(array $database): void {
		file_put_contents(self::DATABASE_PATH, json_encode($database));
	}
	
	private function initializeDatabase(): void {
		file_put_contents(self::DATABASE_PATH, json_encode([]));
	}
	
	private function databaseFileExists(): bool {
		return is_file(self::DATABASE_PATH);
	}
}