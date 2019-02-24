<?php

declare(strict_types=1);

namespace Miste\scoreboardspe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

/**
 * Class EventHandler
 * @package Miste\scoreboardspe
 */
class EventHandler implements Listener{

	/** @var ScoreboardsPE */
	private $plugin;

	/**
	 * EventHandler constructor.
	 * @param ScoreboardsPE $plugin
	 */
	public function __construct(ScoreboardsPE $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @param PlayerQuitEvent $event
	 */
	public function onQuitEvent(PlayerQuitEvent $event){
		$this->plugin->getStore()->removePotentialViewer($event->getPlayer()->getName());
	}
}