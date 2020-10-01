<?php

namespace shakethatbranch\exceptions;

use Throwable;

class GitRepositoryNotInitializedException extends STBException {
	public function __construct(Throwable $previous = null) {
		parent::__construct('Git repository not initialized', 500, $previous);
	}
}