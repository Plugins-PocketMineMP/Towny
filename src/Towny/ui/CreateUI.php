<?php
declare(strict_types=1);
namespace Towny\ui;

use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\player\Player;
use Towny\Towny;
use Towny\TownyLoader;
use Towny\TownyQueue;

class CreateUI extends UI{

	public static function getFormId() : int{
		return 0xe3;
	}

	public static function sendToPlayer(Player $player) : void{
		$encode = [
			"type" => "custom_form",
			"title" => self::getPlugin()->getLanguage()->translateString(self::$uiMessage . "create.title"),
			"content" => [
				[
					"type" => "input",
					"text" => self::getPlugin()->getLanguage()->translateString(self::$uiMessage . "create.name.string")
				]
			]
		];

		$player->getNetworkSession()->sendDataPacket(ModalFormRequestPacket::create(self::getFormId(), json_encode($encode)));
	}

	public function handleResponse(Player $player, $data) : void{
		if(trim($data[0] ?? "") === ""){
			$player->sendMessage(TownyLoader::$prefix . self::getPlugin()->getLanguage()->translateString(self::$message . "create.inputMessage"));
			return;
		}

		if(TownyLoader::getInstance()->getTownyFactory()->getTowny($data[0]) instanceof Towny){
			$player->sendMessage(TownyLoader::$prefix . self::getPlugin()->getLanguage()->translateString(self::$message . "create.alreadyExists"));
			return;
		}

		$player->sendMessage(TownyLoader::$prefix . self::getPlugin()->getLanguage()->translateString(self::$message . "create.start"));
		TownyQueue::$createQueue[$player->getName()] = $data[0];
	}
}