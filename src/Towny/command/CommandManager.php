<?php
declare(strict_types=1);
namespace Towny\command;

use pocketmine\Server;
use Towny\command\subcommand\CreateSubCommand;
use Towny\TownyLoader;

class CommandManager{

	public static function registerAll(TownyLoader $plugin) : void{
		$server = Server::getInstance();
		$server->getCommandMap()->register("towny", new TownyCommand(TownyLoader::getInstance()->getLanguage()->translateString("towny.command.name"), TownyLoader::getInstance()->getLanguage()->translateString("towny.command.description")));

		$command = $server->getCommandMap()->getCommand(TownyLoader::getInstance()->getLanguage()->translateString("towny.command.name"));
		if($command instanceof TownyCommand){
			$command->addSubCommand(new CreateSubCommand($plugin));
		}
	}
}