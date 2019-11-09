<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class EventListener implements Listener{

	/** @var Towny[]|null[] */
	protected $moveList = [];

	public function __construct(TownyLoader $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function checkPlayer(Player $player){
		if(($towny = TownyLoader::getInstance()->getTownyFactory()->getTownyByXZ((float)$player->getX(), (float)$player->getZ(), $player->getPosition()->getWorld()->getFolderName())) instanceof Towny){
			$player->sendPopup(TownyLoader::getInstance()->getLanguage()->translateString("towny.message.enterTown", [$towny->getName()]));
			$this->moveList[$player->getName()] = $towny;
			if(!$towny->isVillager($player)){
				if(!$towny->getOption()->canEnterTown()){
					$player->teleport($towny->getEnd()->add(1, 0, 1));
					$player->sendPopup(TownyLoader::getInstance()->getLanguage()->translateString("towny.message.accessDenied"));
				}
			}
		}else{
			if(isset($this->moveList[$player->getName()])){
				if($this->moveList[$player->getName()] instanceof Towny){
					$this->moveList[$player->getName()] = null;
				}
			}
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority HIGHEST
	 * @handleCancelled true
	 */
	public function handleBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		if(($towny = $this->moveList[$player->getName()]) instanceof Towny){
			if(!$towny->isVillager($player)){
				$event->setCancelled();
			}
		}
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @pritority HIGHEST
	 * @handleCancelled true
	 */
	public function handlePlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		if(($towny = $this->moveList[$player->getName()]) instanceof Towny){
			if(!$towny->isVillager($player)){
				$event->setCancelled();
			}
		}
	}

	/**
	 * @param PlayerInteractEvent $event
	 * @priority HIGHEST
	 * @handleCancelled true
	 */
	public function handleInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		if(($towny = $this->moveList[$player->getName()]) instanceof Towny){
			if(!$towny->isVillager($player)){
				$event->setCancelled();
			}
		}
	}
}