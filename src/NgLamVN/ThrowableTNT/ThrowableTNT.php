<?php

declare(strict_types=1);

namespace NgLamVN\ThrowableTNT;

use pocketmine\entity\Entity;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ThrowableTNT extends PluginBase implements Listener
{
    /** @var $multiply */
    public $multiply;

    public function onEnable()
    {
        $this->saveResource("setting.yml");
        $config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
        $this->multiply = $config->get("multiply");
        $config->save();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    /**
     * @return float
     */
    public function getMultiply(): float
    {
        return $this->multiply;
    }

    /**
     * @param PlayerInteractEvent $event
     * @priotity LOW
     */
    public function onTap (PlayerInteractEvent $event)
    {
        if ($event->isCancelled())
        {
            return;
        }
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_AIR)
        {
            return;
        }

        $player = $event->getPlayer();
        $item = $event->getItem();
        $pos = $player->asVector3();
        $pos->y = $pos->y + $player->getEyeHeight();
        $direction = $player->getDirectionVector()->multiply($this->getMultiply());
        $fuse = 80;
        if ($item->getId() == Item::TNT)
        {
            $nbt = Entity::createBaseNBT($pos);
            $nbt->setShort("Fuse", $fuse);
            $entity = Entity::createEntity("PrimedTNT", $player->getLevel(), $nbt);
            $entity->setRotation($player->getYaw(), $player->getPitch());
            $entity->spawnToAll();
            $entity->setMotion($direction);
            $player->getInventory()->removeItem(Item::get(Item::TNT, 0, 1));
        }
    }
}