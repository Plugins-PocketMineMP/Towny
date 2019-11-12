<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\player\Player;
use pocketmine\world\Position;
use Towny\invitation\InvitationList;
use Towny\option\TownyOption;
use Towny\ui\UI;

class EventListener implements Listener{

	/** @var Towny[]|null[] */
	protected $moveList = [];

	public function __construct(TownyLoader $plugin){
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}

	public function checkPlayer(Player $player){
		if(($towny = TownyLoader::getInstance()->getTownyFactory()->getTownyByXZ((float)$player->getX(), (float)$player->getZ(), $player->getPosition()->getWorld()->getFolderName())) instanceof Towny){
			if(!isset($this->moveList[$player->getName()])){
				$player->sendPopup(TownyLoader::getInstance()->getLanguage()->translateString("towny.message.enterTown", [$towny->getName()]));
			}
			$this->moveList[$player->getName()] = $towny;
			if(!$towny->isVillager($player)){
				if(!$towny->getOption()->canEnterTown()){
					$player->teleport($towny->getEnd()->add(1, 0, 1));
					$player->sendPopup(TownyLoader::getInstance()->getLanguage()->translateString("towny.message.accessDenied"));
				}
			}
		}else{
			if(isset($this->moveList[$player->getName()])){
				$this->moveList[$player->getName()] = null;
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
		if(($towny = $this->moveList[$player->getName()] ?? null) instanceof Towny){
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
		if(($towny = $this->moveList[$player->getName()] ?? null) instanceof Towny){
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

		$block = $event->getBlock();

		if(isset(TownyQueue::$createQueue[$player->getName()])){
			try{
				$name = TownyQueue::$createQueue[$player->getName()];

				$defaultSize = TownyLoader::getInstance()->getConfig()->getNested("default-towny-area-size", 100);
				for($x = $block->getPos()->getX() - (int)$defaultSize; $x <= $block->getPos()->getX() + (int)$defaultSize; $x++){
					for($z = $block->getPos()->getZ() - (int)$defaultSize; $z <= $block->getPos()->getZ() + (int)$defaultSize; $z++){
						if(TownyLoader::getInstance()->getTownyFactory()->getTownyByXZ((float)$x, (float)$z, $block->getPos()->getWorld()->getFolderName()) instanceof Towny){
							$player->sendMessage(TownyLoader::$prefix . TownyLoader::getInstance()->getLanguage()->translateString("towny.message.create.overlapPos", [0 => $defaultSize]));
							break;
						}
					}
				}

				$startX = $block->getPos()->getX() - $defaultSize;
				$endX = $block->getPos()->getX() + $defaultSize;
				$startZ = $block->getPos()->getZ() - $defaultSize;
				$endZ = $block->getPos()->getZ() + $defaultSize;

				$start = new Position((float)$startX, $block->getPos()->getY(), (float)$endX, $block->getPos()->getWorld());
				$end = new Position((float)$startZ, $block->getPos()->getY(), (float)$endZ, $block->getPos()->getWorld());

				$towny = new Towny(TownyLoader::getInstance(), $name, $start, $end, $block->getPos(), [], new TownyOption(false, false), strtolower($player->getName()), 10, new InvitationList([]), 0);
				if(TownyLoader::getInstance()->getTownyFactory()->addTowny($towny)){
					$player->sendMessage(TownyLoader::$prefix . TownyLoader::getInstance()->getLanguage()->translateString("towny.message.create.success", [$towny->getName()]));
					return;
				}
				$player->sendMessage(TownyLoader::$prefix . TownyLoader::getInstance()->getLanguage()->translateString("towny.message.create.failed"));
			}finally{
				unset(TownyQueue::$createQueue[$player->getName()]);
			}
			return;
		}

		if(($towny = $this->moveList[$player->getName()] ?? null) instanceof Towny){
			if(!$towny->isVillager($player)){
				$event->setCancelled();
			}
		}
	}

	public function handlePacketReceive(DataPacketReceiveEvent $event){
		$player = $event->getOrigin()->getPlayer();
		$packet = $event->getPacket();
		if($packet instanceof ModalFormResponsePacket){
			if(($ui = UI::getFormById($packet->formId)) instanceof UI){
				$ui->handleResponse($player, json_decode($packet->formData, true));
			}
		}
	}
}