<?php
declare(strict_types=1);
namespace Towny\lang;

use Towny\TownyLoader;

class PluginLang{

	protected $lang;

	protected $plugin;

	private $langData = [];

	public function __construct(TownyLoader $plugin){
		$this->plugin = $plugin;
		$this->plugin->saveResource("config.yml");

		$lang = $this->plugin->getConfig()->getNested("lang", "eng");

		$this->lang = $lang;

		$this->langData = parse_ini_file($this->plugin->getFile() . "lang" . DIRECTORY_SEPARATOR . $this->lang . ".ini");
		if(!is_array($this->langData)){
			throw new \InvalidStateException("Invalid language $this->lang");
		}
	}

	/**
	 * @param string $str
	 * @param array $input
	 * @return string
	 */
	public function translateString(string $str, array $input = []) : string{
		$str = $this->langData[$str] ?? "";
		foreach($input as $i => $string){
			$str = str_replace("{%" . $i . "}", $string, $str);
		}

		return $str;
	}
}