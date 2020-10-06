<?php

namespace shakethatbranch\system;

use shakethatbranch\exceptions\GitRepositoryNotInitializedException;

class GitRepository extends \Cz\Git\GitRepository {
	public function getPreviousBranchName(): string {
		exec('git checkout -'.' 2>&1', $output, $ret);
		preg_match('/Switched to branch \'(?<branchName>.+)\'/', $output[0], $matches);
		exec('git checkout -'.' 2>&1', $output, $ret);
		
		return $matches['branchName'];
	}
	
	public function branchExists($branchName): bool {
		return in_array($branchName, $this->getBranches());
	}
	
	/**
	 * @throws GitRepositoryNotInitializedException
	 */
	public function getRepositoryRoot(): string {
		$searchPath = $this->getRepositoryPath();
		
		while (glob($searchPath.'/.git', GLOB_ONLYDIR) === []) {
			$searchPathParent = realpath($searchPath.'/..');
			
			if ($searchPath === $searchPathParent) {
				throw new GitRepositoryNotInitializedException();
			}
			
			$searchPath = $searchPathParent;
		}
		
		return $searchPath.'/.git';
	}
}