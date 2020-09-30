<?php

namespace shakethatbranch\system;

class GitRepository extends \Cz\Git\GitRepository {
	public function getPreviousBranchName() {
		exec('git checkout -'.' 2>&1', $output, $ret);
		preg_match('/Switched to branch \'(?<branchName>.+)\'/', $output[0], $matches);
		exec('git checkout -'.' 2>&1', $output, $ret);
		
		return $matches['branchName'];
	}
}