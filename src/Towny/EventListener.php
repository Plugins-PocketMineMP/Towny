<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\event\Listener;
use pocketmine\player\Player;

class EventListener implements Listener{

	protected $moveList = [];

	public function __construct(TownyLoader $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function checkPlayer(Player $player){
		if(($towny = TownyLoader::getInstance()->getTownyFactory()->getTownyByXZ((float)$player->getX(), (float)$player->getZ(), $player->getPosition()->getWorld()->getFolderName())) instanceof Towny){
		}
	}
}