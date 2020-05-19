<?php

declare(strict_types=1);

namespace HyduseaMC\HyduseaSpawners;

use HyduseaMC\HyduseaSpawners\Commands\SpawnerCommand;
use HyduseaMC\HyduseaSpawners\Entities\EntityManager;
use HyduseaMC\HyduseaSpawners\Items\SpawnEgg;
use HyduseaMC\HyduseaSpawners\Items\SpawnerBlock;
use HyduseaMC\HyduseaSpawners\Tiles\MobSpawnerTile;
use HyduseaMC\HyduseaSpawners\Utilities\ConfigManager;
use HyduseaMC\HyduseaSpawners\Utilities\Utils;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat as C;
use ReflectionException;
use ReflectionProperty;

class Main extends PluginBase
{

    /** @var string */
    public const PREFIX = C::BOLD . C::AQUA . "Burger" . C::LIGHT_PURPLE . "Spawners" . "> " . C::RESET;

    /**
     * @var Main
     */
    public static $instance;

    /**
     * @var array
     */
    public $exemptedEntities = [];

    public function onEnable(): void
    {
        self::$instance = $this;
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register("ULTRAMOBS", new SpawnerCommand($this));

        /** @noinspection PhpUnhandledExceptionInspection */
        Tile::registerTile(MobSpawnerTile::class, [Tile::MOB_SPAWNER, "minecraft:mob_spawner"]);
        BlockFactory::registerBlock(new SpawnerBlock(), true);
        ItemFactory::registerItem(new SpawnEgg(), true);
        Item::initCreativeItems();

        if (ConfigManager::getToggle("register-mobs")) {
            EntityManager::init();
        }

        if(is_array(ConfigManager::getArray("exempted-entities"))) {
            foreach (ConfigManager::getArray("exempted-entities") as $entityName) {
                $this->exemptEntityFromStackingByName((string)$entityName);
            }
        }

        UpdateNotifier::checkUpdate($this, $this->getDescription()->getName(), $this->getDescription()->getVersion());
    }

    /**
     * @return array|null
     * @throws ReflectionException
     */
    public function getRegisteredEntities(): ?array
    {
        $reflectionProperty = new ReflectionProperty(Entity::class, 'knownEntities');
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue();
    }

    public static function getInstance(): Main
    {
        return self::$instance;
    }

    public function getSpawner(string $name, int $amount): Item
    {
        $name = strtolower($name);
        $name = str_replace(" ", "", $name);
        $entityID = Utils::getEntityIDFromName($name);

        $nbt = new CompoundTag("", [
            new IntTag("EntityID", (int)$entityID)
        ]);

        $spawner = Item::get(Item::MOB_SPAWNER, 0, $amount, $nbt);
        $spawnerName = Utils::getEntityNameFromID((int)$entityID) . " Spawner";
        $spawner->setCustomName(C::RESET . $spawnerName);

        return $spawner;
    }

    /**
     * @param Entity $entity
     */
    public function exemptEntityFromStacking(Entity $entity): void
    {
        $this->exemptedEntities[] = $entity->getId();
    }

    /**
     * @param string $entityName
     */
    public function exemptEntityFromStackingByName(string $entityName): void
    {
        $this->exemptedEntities[] = $entityName;
    }

}
