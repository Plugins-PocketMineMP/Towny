<?php
declare(strict_types=1);
namespace Towny\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Towny\TownyLoader;
use Towny\ui\CreateUI;

class CreateSubCommand extends SubCommand{

	public function __construct(TownyLoader $plugin){
		parent::__construct($plugin, $this->getPlugin()->getLanguage()->translateString("towny.subcommand.create"), false);
	}

	public function handle(CommandSender $sender, array $parameters) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TownyLoader::$prefix . $this->getPlugin()->getLanguage()->translateString("towny.message.onlyInGame"));
			return;
		}

		CreateUI::sendToPlayer($sender);
	}
}