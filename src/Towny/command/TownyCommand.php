<?php
declare(strict_types=1);
namespace Towny\command;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\command\PluginIdentifiableCommand;
use Towny\command\subcommand\SubCommand;
use Towny\TownyLoader;

class TownyCommand extends PluginCommand implements PluginIdentifiableCommand{

	protected $isOp;

	protected $subCommands = [];

	public function __construct(string $name, string $description, string $usage = "", array $alias = []){
		parent::__construct($name, TownyLoader::getInstance());
		$this->setDescription($description);
		$this->setUsage($usage);
		$this->setAliases($alias);
	}

	public function addSubCommand(SubCommand $command){
		$this->subCommands[$command->getName()] = $command;
	}

	/**
	 * @return SubCommand[]
	 */
	public function getAllSubCommands() : array{
		return array_values($this->subCommands);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : bool{
		$parameters = array_shift($args);

		foreach($this->getAllSubCommands() as $subCommand){
			if($subCommand->equals($parameters ?? "")){
				if($subCommand->hasPermission($sender)){
					$subCommand->handle($sender, $args);
				}
			}
			return true;
		}
		return false;
	}
}