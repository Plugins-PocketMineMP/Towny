<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\plugin\PluginBase;
use Towny\lang\PluginLang;

class TownyLoader extends PluginBase{

	public static $prefix;

	/** @var PluginLang */
	protected $lang;

	private static $instance = null;

	/** @var TownyFactory */
	protected $townyFactory;

	public function onLoad(){
		self::$instance = $this;
	}

	public static function getInstance() : TownyLoader{
		return self::$instance;
	}

	public function onEnable(){
		$this->lang = new PluginLang($this);

		self::$prefix = $this->lang->translateString("plugin.prefix");
		$this->getLogger()->info($this->lang->translateString("plugin.enabled"));

		$this->townyFactory = new TownyFactory($this);
	}

	public function getTownyFactory() : TownyFactory{
		return $this->townyFactory;
	}

	public function onDisable(){
		$this->getLogger()->info($this->lang->translateString("plugin.disabled"));
		foreach($this->townyFactory->getAllTownies() as $towny){
			$nbt = $towny->nbtSerialize();
			file_put_contents($this->getDataFolder() . "towns/" . $towny->getName() . ".dat", (new LittleEndianNbtSerializer())->writeCompressed(new TreeRoot($nbt)));
		}
	}

	/**
	 * Need to PluginLang
	 * @see PluginLang::__construct()
	 * @return string
	 */
	public function getFile() : string{
		return parent::getFile();
	}

	public function getLanguage() : PluginLang{
		return $this->lang;
	}
}