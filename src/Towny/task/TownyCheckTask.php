<?php
declare(strict_types=1);
namespace Towny\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Towny\TownyLoader;

class TownyCheckTask extends Task{

	public function onRun(int $unused) : void{
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			TownyLoader::getInstance()->getEventListener()->checkPlayer($player);
		}
	}
}