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
}
