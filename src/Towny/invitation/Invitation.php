<?php
declare(strict_types=1);
namespace Towny\invitation;

use pocketmine\nbt\tag\CompoundTag;

class Invitation{

	protected $message;

	protected $player;

	protected $requestDate;

	protected $hash;

	public function __construct(string $message, string $player, string $requestDate, string $hash){
		$this->message = $message;
		$this->player = $player;
		$this->requestDate = $requestDate;
		$this->hash = $hash;
	}

	public function getMessage() : string{
		return $this->message;
	}

	public function getPlayer() : string{
		return $this->player;
	}

	public function getRequestDate() : string{
		return $this->requestDate;
	}

	public function getHash() : string{
		return $this->hash;
	}

	public function nbtSerialize() : CompoundTag{
		return CompoundTag::create()
				->setString("message", $this->message)
				->setString("player", $this->player)
				->setString("requestData", $this->requestDate)
				->setString("hash", $this->hash);
	}

	public static function nbtDeserialize(CompoundTag $nbt) : Invitation{
		return new Invitation(
				$nbt->getString("message",""),
				$nbt->getString("player"),
				$nbt->getString("requestData"),
				$nbt->getString("hash")
		);
	}
}