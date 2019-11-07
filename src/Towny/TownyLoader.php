<?php
declare(strict_types=1);
namespace Towny;

use pocketmine\plugin\PluginBase;
use Towny\lang\PluginLang;

class TownyLoader extends PluginBase{

	public static $prefix;

	/** @var PluginLang */
	protected $lang;

	public function onEnable(){
		$this->lang = new PluginLang($this);

		self::$prefix = $this->lang;
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