<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\SetHudPacket;
use pocketmine\network\mcpe\protocol\types\hud\HudElement;
use pocketmine\network\mcpe\protocol\types\hud\HudVisibility;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use ReflectionException;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;

class HideHearts extends BasePartnerItem implements OnAttackPartnerItem {

    public function __construct() {
        parent::__construct(
            'hidehearts',
            TextFormat::colorize('&r&4Hide Hearts'),
            VanillaItems::HEART_OF_THE_SEA()
        );
    }

    /**
     * @throws ReflectionException
     */
    public function onAttack(Player $damager, Player $player): void {
        $packet = SetHudPacket::create([HudElement::HEALTH], HudVisibility::HIDE);
        $player->getNetworkSession()->sendDataPacket($packet);

        $player->sendMessage(TextFormat::colorize('&r&7Your health is now hidden for &f5 &7seconds.'));
        $damager->sendMessage(TextFormat::colorize('&r&7You have hidden the health of &f' . $player->getName() . '&7.'));

        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
            $resetPacket = SetHudPacket::create([HudElement::HEALTH], HudVisibility::RESET);
            $player->getNetworkSession()->sendDataPacket($resetPacket);
        }), 20*5);
    }
}