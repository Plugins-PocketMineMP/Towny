<?php
declare(strict_types=1);
namespace Towny\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use Towny\Towny;

abstract class TownyEvent extends Event implements Cancellable{
	use CancellableTrait;

	protected $towny;

	public function __construct(Towny $towny){
		$this->towny = $towny;
	}

	public function getTowny() : Towny{
		return $this->towny;
	}
}