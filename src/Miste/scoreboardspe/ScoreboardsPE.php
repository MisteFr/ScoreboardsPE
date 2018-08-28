<?php

declare(strict_types=1);

namespace Miste\scoreboardspe;

use Miste\scoreboardspe\API\ScoreboardStore;
use Miste\scoreboardspe\commands\ScoreboardCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class ScoreboardsPE extends PluginBase{

	private $scoreboardStore;

	public function onEnable() : void{
		$this->getLogger()->info("I have been enabled !");
		$this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);
		$this->getServer()->getCommandMap()->register("scoreboard", new ScoreboardCommand($this, "scoreboard"));

		$this->scoreboardStore = new ScoreboardStore();
	}

	public function getStore() : ScoreboardStore{
		return $this->scoreboardStore;
	}

	public function getPlugin(){
		return $this;
	}
}