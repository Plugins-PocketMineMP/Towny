<?php
declare(strict_types=1);
namespace Towny\command\subcommand;

use pocketmine\command\CommandSender;
use Towny\TownyLoader;

class ManageSubCommand extends SubCommand{

	public function __construct(TownyLoader $plugin) {
		parent::__construct($plugin, $plugin->getLanguage()->translateString("towny.subcommand.manage"), false);
	}

	public function handle(CommandSender $sender, array $parameters) : void{

	}
}