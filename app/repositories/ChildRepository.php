<?php

namespace shakethatbranch\repositories;

class ChildRepository {
	const DATABASE_FILENAME = 'stb.json';
	private string $gitDirectory;
	
	public function __construct(string $gitDirectory) {
		$this->gitDirectory = $gitDirectory;
	}
	
	public function addChild(string $parentBranchName, string $childBranchName): void {
		$database = $this->getDatabase();
		
		$database[$parentBranchName][] = $childBranchName;
		
		$this->persistDatabase($database);
	}
	
	public function removeChild(string $parentBranchName, string $childBranchName): void {
		$database = $this->getDatabase();
		unset($database[$parentBranchName][array_search($childBranchName, $database[$parentBranchName])]);
		$this->persistDatabase($database);
	}
	
	/**
	 * @param string $parentBranchName
	 * @return string[]
	 */
	public function findChildren(string $parentBranchName): array {
		if (!$this->hasAnyChildren($parentBranchName)) {
			return [];
		}
		
		return $this->getDatabase()[$parentBranchName];
	}
	
	public function hasAnyChildren(string $parentBranchName): bool {
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
	
	public function initializeDatabase(): void {
		file_put_contents($this->getDatabasePath(), json_encode([]));
	}
	
	public function databaseFileExists(): bool {
		return is_file($this->getDatabasePath());
	}
	
	public function getDatabasePath(): string {
		return $this->gitDirectory.'/'.self::DATABASE_FILENAME;
	}
	
	private function getDatabase(): array {
		$json = file_get_contents($this->getDatabasePath());
		return json_decode($json, true);
	}
	
	private function persistDatabase(array $database): void {
		file_put_contents($this->getDatabasePath(), json_encode($database));
	}
}