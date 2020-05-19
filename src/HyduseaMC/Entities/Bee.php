<?php

namespace HyduseaMC\HyduseaSpawners\Entities;

use HyduseaMC\HyduseaSpawners\Pocketmine\AddActorPacket;

use pocketmine\entity\Living;
use pocketmine\Player;

class Bee extends Living
{

    public const NETWORK_ID = 122;

    public $width = 0.6;
    public $height = 0.6;

    public function getName(): string
    {
        return "Bee";
    }

    public function getDrops(): array{
        $lootingL = 1;
        $cause = $this->lastDamageCause;
        if($cause instanceof EntityDamageByEntityEvent){
            $dmg = $cause->getDamager();
            if($dmg instanceof Player){
                 

                $looting = $dmg->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING);
                if($looting !== null){
                    $lootingL = $looting->getLevel();
                }else{
                    $lootingL = 1;
            }
            }
        }
        return [Item::get(Item::Sunflower, 0, mt_rand(0, 1 * $lootingL))];
    }

}
