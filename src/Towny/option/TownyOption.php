<?php
declare(strict_types=1);
namespace Towny\option;

use pocketmine\nbt\tag\CompoundTag;

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

	public function nbtSerialize() : CompoundTag{
		$nbt = CompoundTag::create();
		$nbt->setByte("canDestroy", $this->canDestroy ? 1 : 0);
		$nbt->setByte("canEnterTown", $this->canEnterTown ? 1 : 0);
		return $nbt;
	}

	public static function nbtDeserialize(CompoundTag $nbt) : TownyOption{
		return new TownyOption(
				($nbt->getByte("canDestroy", 0) === 1 ? true : false),
				($nbt->getByte("canEnterTown", 0) === 1 ? true : false)
		);
	}
}