<?php

declare(strict_types=1);

namespace Miste\scoreboardspe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;

use Miste\scoreboardspe\API\{
	Scoreboard, ScoreboardDisplaySlot, ScoreboardSort
};

class EventHandler implements Listener{

	/** @var ScoreboardsPE */
	private $plugin;

	public function __construct(ScoreboardsPE $plugin){
		$this->plugin = $plugin;
	}

	public function onPlayerJoinEvent(PlayerJoinEvent $event){
		$scoreboard = new Scoreboard($this->plugin, "Miste");
		$scoreboard->addDisplay($event->getPlayer(), ScoreboardDisplaySlot::SIDEBAR, ScoreboardSort::DESCENDING);

		$scoreboard->setLine($event->getPlayer(), 1, "line1");
		$scoreboard->setLine($event->getPlayer(), 2, "line2");
		$scoreboard->setLine($event->getPlayer(), 4, "line4");
		$scoreboard->setLine($event->getPlayer(), 7, "line7");
		$scoreboard->setLine($event->getPlayer(), 9, "line9");
	}
}