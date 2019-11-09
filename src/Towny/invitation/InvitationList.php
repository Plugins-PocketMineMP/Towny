<?php
declare(strict_types=1);
namespace Towny\invitation;

use pocketmine\nbt\tag\CompoundTag;

class InvitationList{

	/** @var Invitation[] */
	private $invitations = [];

	public function __construct(array $invitations){
		foreach($invitations as $invitation){
			$this->add($invitation);
		}
	}

	public function add(Invitation $invitation){
		$this->invitations[$invitation->getHash()] = $invitation;
	}

	public function remove(Invitation $invitation){
		unset($this->invitations[$invitation->getHash()]);
	}

	/**
	 * @return Invitation[]
	 */
	public function all() : array{
		return array_values($this->invitations);
	}

	public function nbtSerialize() : CompoundTag{
		$tag = CompoundTag::create();
		foreach($this->invitations as $invitation){
			$tag->setTag($invitation->getPlayer(), $invitation->nbtSerialize());
		}

		return CompoundTag::create()
				->setTag("Invitations", $tag);
	}

	public static function nbtDeserialize(CompoundTag $nbt) : InvitationList{
		$arr = [];
		foreach($nbt->getValue() as $name => $tag){
			if($tag instanceof CompoundTag){
				$arr[] = Invitation::nbtDeserialize($tag);
			}
		}
		return new InvitationList($arr);
	}
}