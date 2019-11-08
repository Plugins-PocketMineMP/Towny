<?php
declare(strict_types=1);
namespace Towny\event;

use pocketmine\player\Player;
use Towny\Towny;

class TownyCreateEvent extends TownyEvent{

	/** @var Player */
	private $creator;

	public function __construct(Towny $towny, Player $player){
		parent::__construct($towny);
		$this->creator = $player;
	}

	public function getPlayer() : Player{
		return $this->creator;
	}
}