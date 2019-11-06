<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\world\Position;
use Towny\option\TownyOption;

class Towny{

	protected $plugin;

	/** @var string Town's name */
	protected $name;

	/** @var Position Town's Position */
	protected $start;

	/** @var Position Town's Position */
	protected $end;

	/** @var string Town's Leader */
	protected $leader;

	/**
	 * @var string[]
	 * PlayerName => Role
	 */
	protected $villagers = [];

	/** @var Position Town's main spawn */
	protected $spawn;

	/** @var TownyOption */
	protected $option; // TODO: Starting Option

	public function __construct(TownyLoader $plugin, string $name, Position $start, Position $end, Position $spawn, array $villagers, TownyOption $option, string $leader){
		$this->plugin = $plugin;
		$this->name = $name;
		$this->start = $start;
		$this->end = $end;
		$this->spawn = $spawn;
		$this->leader = $leader;
		$this->villagers = $villagers;
		$this->option = $option;
	}
}