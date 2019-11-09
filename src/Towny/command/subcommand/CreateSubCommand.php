<?php
declare(strict_types=1);
namespace Towny\command\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Towny\TownyLoader;

class CreateSubCommand extends SubCommand{

	public function __construct(){
		parent::__construct($this->getPlugin()->getLanguage()->translateString("towny.subcommand.create"), false);
	}

	public function handle(CommandSender $sender, array $parameters) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TownyLoader::$prefix . $this->getPlugin()->getLanguage()->translateString("towny.message.onlyInGame"));
			return;
		}

		$name = array_shift($parameters);

		if(!isset($name)){
			$sender->sendMessage(TownyLoader::$prefix . $this->getPlugin()->getLanguage()->translateString("towny.message.enterString", [$this->getPlugin()->getLanguage()->translateString("string.name")]));
			return;
		}


	}
}