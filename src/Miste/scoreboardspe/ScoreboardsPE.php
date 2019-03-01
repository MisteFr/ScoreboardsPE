<?php

declare(strict_types=1);

namespace Miste\scoreboardspe;

use Miste\scoreboardspe\API\ScoreboardStore;
use Miste\scoreboardspe\commands\ScoreboardCommand;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ScoreboardsPE extends PluginBase{

	/** @var ScoreboardStore */
	private $scoreboardStore;

	public function onEnable() : void{
		if (!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
		$this->saveResource("config.yml");
		$config = new Config($this->getDataFolder() . "config.yml");
		
		$this->getServer()->getPluginManager()->registerEvents(new EventHandler($this), $this);

		if($config->get("register-commands") === true){
			$this->getLogger()->info("Registering commands !");
			$this->getServer()->getCommandMap()->register("scoreboardspe", new ScoreboardCommand($this, "scoreboard"));
		}

		$this->scoreboardStore = new ScoreboardStore();
		$this->getLogger()->info("I have been enabled !");
	}

	/**
	 * @return ScoreboardStore
	 */
	public function getStore() : ScoreboardStore{
		return $this->scoreboardStore;
	}

	/**
	 * @return $this
	 */
	public function getPlugin(){
		return $this;
	}
}