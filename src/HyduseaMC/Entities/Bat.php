<?php

namespace HyduseaMC\HyduseaSpawners\Entities;

use pocketmine\entity\Animal;

class Bat extends Animal
{
    const NETWORK_ID = self::BAT;

    public $width = 0.5;
    public $height = 0.9;

    public function getName(): string
    {
        return "Bat";
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
        return [Item::get(Item::String, 0, mt_rand(0, 1 * $lootingL))];
    }

}
