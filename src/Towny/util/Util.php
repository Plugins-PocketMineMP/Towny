<?php
declare(strict_types=1);
namespace Towny\util;

use pocketmine\Server;
use pocketmine\world\Position;

class Util{

	public static function pos2hash(Position $pos) : string{
		return implode(":",[$pos->x, $pos->y, $pos->z, $pos->world->getFolderName()]);
	}

	public static function hash2pos(string $hash) : Position{
		[$x, $y, $z, $world] = explode(":", $hash);
		return new Position((float)$x, (float)$y, (float)$z, Server::getInstance()->getWorldManager()->getWorldByName($world));
	}
}