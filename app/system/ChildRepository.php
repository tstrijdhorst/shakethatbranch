<?php

namespace shakethatbranch\system;

class ChildRepository {
	const DATABASE_PATH = __DIR__.'/../../db.json';
	
	public function __construct() {
		if (!$this->databaseFileExists()) {
			$this->initializeDatabase();
		}
	}
	
	public function addChild($branchName) : void {
		$database = $this->getDatabase();
		
		$database[$branchName]['children'][] = $branchName;
		
		$this->persistDatabase($database);
	}
	
	private function getDatabase() : array {
		$json = file_get_contents(self::DATABASE_PATH);
		return json_decode($json, true);
	}
	
	/**
	 * @param $database
	 */
	private function persistDatabase($database) : void {
		file_put_contents(self::DATABASE_PATH, json_encode($database));
	}
	
	private function initializeDatabase(): void {
		file_put_contents(self::DATABASE_PATH, json_encode([]));
	}
	
	/**
	 * @return bool
	 */
	private function databaseFileExists(): bool {
		return is_file(self::DATABASE_PATH);
	}
}