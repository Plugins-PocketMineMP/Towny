<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use Towny\option\TownyOption;
use Towny\util\Util;

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

	/** @var int */
	protected $maxVillagers = 10;

	/** @var string */
	protected $prefix;

	/** @var string */
	protected $message = "towny.message.";

	public const TAG_LEADER = "Leader";

	public const TAG_CO_LEADER = "Co-Leader";

	public const TAG_VILLAGER = "Villager";

	public function __construct(TownyLoader $plugin, string $name, Position $start, Position $end, Position $spawn, array $villagers, TownyOption $option, string $leader, int $maxVillagers){
		$this->plugin = $plugin;
		$this->name = $name;
		$this->start = $start;
		$this->end = $end;
		$this->spawn = $spawn;
		$this->leader = $leader;
		$this->villagers = $villagers;
		$this->option = $option;
		$this->maxVillagers = $maxVillagers;

		$this->prefix = "§b§l[ " . $this->getName() . "§b§l] §r§7";
	}

	/**
	 * @return TownyLoader
	 */
	public function getPlugin() : TownyLoader{
		return $this->plugin;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return Position
	 */
	public function getStart() : Position{
		return $this->start;
	}

	/**
	 * @return Position
	 */
	public function getEnd() : Position{
		return $this->end;
	}

	/**
	 * @return Position
	 */
	public function getSpawn() : Position{
		return $this->spawn;
	}

	/**
	 * @return string[]
	 */
	public function getVillagers() : array{
		return array_keys($this->villagers);
	}

	/**
	 * @return TownyOption
	 */
	public function getOption() : TownyOption{
		return $this->option;
	}

	/**
	 * @return string
	 */
	public function getLeader() : string{
		return $this->leader;
	}

	/**
	 * @return Player[]
	 */
	public function getOnlineVillagers() : array{
		return array_filter($this->getVillagers(), function(string $name){
			return ($player = Server::getInstance()->getPlayerExact($name)) instanceof Player ? $player : null;
		});
	}

	/**
	 * @param string $role
	 * @throws \InvalidArgumentException
	 */
	public function validate(string $role) : void{
		$roles = [
			self::TAG_LEADER,
			self::TAG_CO_LEADER,
			self::TAG_VILLAGER
		];

		if(!in_array($role, $roles)){
			throw new \InvalidArgumentException("$role is invalid role.");
		}
	}

	/**
	 * @param Player|string $player
	 * @param string $role
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function addVillager($player, string $role = self::TAG_VILLAGER) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		$this->validate($role);
		if($this->isVillager($player)){
			return false;
		}

		if($this->maxVillagers >= count($this->villagers)){
			return false;
		}

		$this->villagers[$player] = $role;
		return true;
	}

	/**
	 * @param Player|string $player
	 * @param bool $force
	 * @return bool
	 */
	public function removeVillager($player, bool $force = false) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		if(!$this->isVillager($player)){
			return false;
		}

		unset($this->villagers[$player]);

		if($force){
			$this->broadcastMessage($this->getPlugin()->getLanguage()->translateString($this->message . "forceQuit", [$player]));
		}
		return true;
	}

	/**
	 * @param Player|string $player
	 * @return bool
	 */
	public function isVillager($player) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		return isset($this->villagers[$player]) or $this->leader === $player;
	}

	/**
	 * @param string $message
	 * @param null $player
	 */
	public function broadcastMessage(string $message, $player = null){
		$this->getPlugin()->getServer()->broadcastMessage($this->prefix . ($player !== null ? $player . " > " : "") . $message, $this->getOnlineVillagers());
	}

	public function getRole($player) : string{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		return $this->villagers[$player];
	}

	public function translateRole($player) : string{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		return $this->getPlugin()->getLanguage()->translateString("towny.role." . $this->getRole($player));
	}

	public function canJoin() : bool{
		return $this->maxVillagers > count($this->villagers);
	}

	/**
	 * @param Player|string $player
	 * @param string $role
	 * @throws \InvalidArgumentException
	 */
	public function setRole($player, string $role) : void{
		$this->validate($role);
		$this->villagers[$player] = $role;
	}

	public function nbtSerialize() : CompoundTag{
		$nbt = CompoundTag::create();
		$nbt->setString("name", $this->name);
		$nbt->setString("villagers", json_encode($this->villagers));
		$nbt->setString("leader", $this->leader);
		$nbt->setInt("maxVillagers", $this->maxVillagers);
		$nbt->setTag("option", $this->option->nbtSerialize());
		$nbt->setString("start", Util::pos2hash($this->start));
		$nbt->setString("end", Util::pos2hash($this->end));
		$nbt->setString("spawn", Util::pos2hash($this->start));
		return $nbt;
	}

	public static function nbtDeserialize(CompoundTag $nbt) : Towny{
		return new Towny(
				TownyLoader::getInstance(),
				$nbt->getString("name"),
				Util::hash2pos($nbt->getString("start")),
				Util::hash2pos($nbt->getString("end")),
				Util::hash2pos($nbt->getString("spawn")),
				json_decode($nbt->getString("villagers"), true),
				TownyOption::nbtDeserialize($nbt->getCompoundTag("option")),
				$nbt->getString("leader"),
				$nbt->getInt("maxVillagers")
		);
	}
}