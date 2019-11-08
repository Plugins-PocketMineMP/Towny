<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\NbtDataException;
use pocketmine\player\Player;
use Towny\event\TownyCreateEvent;
use Towny\event\TownyDeleteEvent;

class TownyFactory{

	/** @var Towny[] */
	protected $towns = [];

	/** @var Towny[] */
	protected $playerToTowny = [];

	protected $plugin;

	public function __construct(TownyLoader $plugin){
		$this->plugin = $plugin;

		if(!is_dir($plugin->getDataFolder() . "towns/")){
			mkdir($plugin->getDataFolder() . "towns/");
		}

		$c = 0;

		foreach(array_diff(scandir($plugin->getDataFolder() . "towns/"), [".", ".."]) as $file){
			$ext = pathinfo($plugin->getDataFolder() . "towns/" . $file)["extension"];

			if($ext !== "dat"){
				continue;
			}

			try{
				$data = (new LittleEndianNbtSerializer())->readCompressed(file_get_contents($plugin->getDataFolder() . "towns/" . $file));
			}catch(NbtDataException $e){
				$plugin->getLogger()->error("Towny data corrupted: " . $file);
				rename($plugin->getDataFolder() . "towns/" . $file, $plugin->getDataFolder() . "towns/" . $file . ".bak");
				continue;
			}
			$data = $data->getTag();
			try{
				$towny = Towny::nbtDeserialize($data);
			}catch(\Exception $e){
				$plugin->getLogger()->error("Towny data corrupted: " . $file);
				rename($plugin->getDataFolder() . "towns/" . $file, $plugin->getDataFolder() . "towns/" . $file . ".bak");
				continue;
			}

			$this->towns[$towny->getName()] = $towny;
			$c++;

			foreach($towny->getVillagers() as $villager){
				if(isset($this->playerToTowny[$villager])){ // if villager of another town
					unset($this->playerToTowny[$villager]);
				}elseif(isset($this->playerToTowny[$villager]) and (!$this->playerToTowny[$villager] instanceof Towny or !$this->playerToTowny[$villager]->isVillager($villager))){ // if villager of another town and is not villager of town
					unset($this->playerToTowny[$villager]);
				}
				$this->playerToTowny[$villager] = $towny;
			}
		}
		$plugin->getLogger()->notice($plugin->getLanguage()->translateString("plugin.dataLoaded", [0 => $c]));
	}

	public function addTowny(Towny $towny) : bool{
		if(isset($this->towns[$towny->getName()])){
			return false;
		}

		$ev = new TownyCreateEvent($towny, $this->plugin->getServer()->getPlayerExact($towny->getLeader()));
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}

		$this->towns[$towny->getName()] = $towny;
		return true;
	}

	public function removeTowny(Towny $towny) : bool{
		if(!isset($this->towns[$towny->getName()])){
			return false;
		}

		$ev = new TownyDeleteEvent($towny);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}

		unset($this->towns[$towny->getName()]);
		@unlink($this->plugin->getDataFolder() . "towns/" . $towny->getName() . ".dat");
		return true;
	}

	public function addVillager(Towny $towny, $player) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		if(isset($this->playerToTowny[$player])){
			return false;
		}

		if($towny->isVillager($player)){
			return false;
		}

		if(!$towny->canJoin()){
			return false;
		}

		$towny->addVillager($player);
		$this->playerToTowny[$player] = $towny;
		return true;
	}

	public function removeVillager(Towny $towny, $player, bool $force = false) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		if(!isset($this->playerToTowny[$player])){
			return false;
		}

		if(!$towny->isVillager($player)){
			return false;
		}

		$towny->removeVillager($player, $force);
		unset($this->playerToTowny[$player]);
		return true;
	}

	public function getTowny(string $name) : ?Towny{
		return $this->towns[$name] ?? null;
	}

	public function getTownyByPlayer(Player $player) : ?Towny{
		return $this->playerToTowny[strtolower($player->getName())] ?? null;
	}

	public function getTownyByXZ(float $x, float $z, string $world) : ?Towny{
		foreach(array_values($this->towns) as $towny){
			if(($towny->getStart()->getX() <= $x and $towny->getEnd()->getX() >= $x) and ($towny->getStart()->getZ() <= $z and $towny->getEnd()->getZ() >= $z) and $towny->getSpawn()->getWorld()->getFolderName() === $world){
				return $towny;
			}
		}
		return null;
	}

	/**
	 * @return Towny[]
	 */
	public function getAllTownies() : array{
		return array_values($this->towns);
	}
}