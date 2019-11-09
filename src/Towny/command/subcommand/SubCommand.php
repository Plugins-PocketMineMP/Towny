<?php
declare(strict_types=1);
namespace Towny\command\subcommand;

use pocketmine\command\CommandSender;
use Towny\TownyLoader;

abstract class SubCommand{

	protected $name;

	protected $isOp;

	protected $plugin;

	public function __construct(string $name, bool $isOp = false){
		$this->name = $name;
		$this->isOp = $isOp;
		$this->plugin = TownyLoader::getInstance();
	}

	public function getPlugin() : TownyLoader{
		return $this->plugin;
	}

	public function hasPermission(CommandSender $sender) : bool{
		if($this->isOp){
			return $sender->isOp();
		}
		return true;
	}

	public function getName() : string{
		return $this->name;
	}

	public function equals(string $name) : bool{
		return $this->getName() === $name;
	}

	abstract public function handle(CommandSender $sender, array $parameters) : void;
}