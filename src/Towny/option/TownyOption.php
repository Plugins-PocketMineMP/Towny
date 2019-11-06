<?php
declare(strict_types=1);
namespace Towny\option;

class TownyOption{

	/** @var bool */
	protected $canDestroy;

	/** @var bool */
	protected $canEnterTown;

	public function __construct(bool $canDestroy, bool $canEnterTown){
		$this->canDestroy = $canDestroy;
		$this->canEnterTown = $canEnterTown;
	}

	public function canDestroy() : bool{
		return $this->canDestroy;
	}

	public function canEnterTown() : bool{
		return $this->canEnterTown;
	}

	public function setCanDestroy(bool $value) : self{
		$this->canDestroy = $value;
		return $this;
	}

	public function setCanEnterTown(bool $value) : self{
		$this->canEnterTown = $value;
		return $this;
	}
}