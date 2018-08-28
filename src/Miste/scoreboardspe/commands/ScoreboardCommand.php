<?php

namespace Miste\scoreboardspe\commands;

use pocketmine\command\{
	Command, CommandSender, PluginIdentifiableCommand
};
use Miste\scoreboardspe\API\{
	Scoreboard, ScoreboardDisplaySlot, ScoreboardSort
};
use Miste\scoreboardspe\ScoreboardsPE;
use pocketmine\Player;
use pocketmine\plugin\Plugin;


class ScoreboardCommand extends Command implements PluginIdentifiableCommand{


	/** @var ScoreboardsPE */
	private $plugin;

	public function __construct(ScoreboardsPE $plugin, string $name){
		parent::__construct($name, "Send a scoreboard with a command !", "/scoreboard add <player / @all> <title> <displaySlot (sidebar/list/belowname> <sortOrder (0->ascending/1->descending)>", ["sc", "score"]);
		$this->plugin = $plugin;
		$this->setPermission("scoreboard.cmd");
		$this->setDescription("Send a scoreboard with a command ! (/sc add/remove/addLine/rmLine)");
		$this->setUsage("/scoreboard add <player / @all> <title> <displaySlot (sidebar/list/belowname> <sortOrder (0->ascending/1->descending)>");
	}

	public function getPlugin() : Plugin{
		return $this->plugin;
	}

	public function execute(CommandSender $sender, $commandLabel, array $args){
		if($sender instanceof Player){
			switch($args[0]){

				case "add":
					if(!(count($args) < 5)){
						if(is_numeric($args[4])){
							$scoreboard = new Scoreboard($this->plugin, $args[2]);
							if($args[1] === "@all"){
								foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
									$scoreboard->addDisplay($p, strtolower($args[3]), (int) $args[4]);
								}
							}else{
								$p = $this->plugin->getServer()->getPlayer($args[1]);
								if($p !== null){
									$scoreboard->addDisplay($p, strtolower($args[3]), (int) $args[4]);
								}else{
									$sender->sendMessage("§cThis player isn't online.");
								}
							}
						}else{
							$sender->sendMessage("§cThe sort order need to be 0/1.");
						}
					}else{
						$sender->sendMessage("§e/scoreboard add <player / @all> <title> <displaySlot (sidebar/list/belowname)> <sortOrder (0->ascending/1->descending)>");
					}
					break;

				case "addLine":
					if(!(count($args) < 5)){
						if($this->plugin->getStore()->getId($args[2]) !== null){
							if(is_numeric($args[3])){
								if((int) $args[3] >= 1 && (int) $args[3] <= 9){
									$scoreboard = new Scoreboard($this->plugin, $args[2], false);
									if($args[1] === "@all"){
										foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
											$scoreboard->setLine($p, (int) $args[3], implode(" ", array_slice($args, 4)));
										}
									}else{
										$p = $this->plugin->getServer()->getPlayer($args[1]);
										if($p !== null){
											$scoreboard->setLine($p, (int) $args[3], implode(" ", array_slice($args, 4)));
										}else{
											$sender->sendMessage("§cThis player isn't online.");
										}
									}
								}else{
									$sender->sendMessage("§cThe line number should be a number between 1 and 9.");
								}
							}else{
								$sender->sendMessage("§cThe line number should be a number.");
							}
						}else{
							$sender->sendMessage("§cThere is no scoreboard with that name.");
						}
					}else{
						$sender->sendMessage("§e/scoreboard addLine <player / @all> <title of the scoreboard> <line> <message>");
					}
					break;

				case "rmLine":
					if(!(count($args) < 4)){
						if($this->plugin->getStore()->getId($args[2]) !== null){
							if(is_numeric($args[3])){
								if((int) $args[3] >= 1 && (int) $args[3] <= 9){
									$scoreboard = new Scoreboard($this->plugin, $args[2], false);
									if($args[1] === "@all"){
										foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
											$scoreboard->removeLine($p, (int) $args[3]);
										}
									}else{
										$p = $this->plugin->getServer()->getPlayer($args[1]);
										if($p !== null){
											$scoreboard->removeLine($p, $args[3]);
										}else{
											$sender->sendMessage("§cThis player isn't online.");
										}
									}
								}else{
									$sender->sendMessage("§cThe line number should be a number between 1 and 9.");
								}
							}else{
								$sender->sendMessage("§cThe line number should be a number.");
							}
						}else{
							$sender->sendMessage("§cThere is no scoreboard with that name.");
						}
					}else{
						$sender->sendMessage("§e/scoreboard rmLine <player / @all> <title of the scoreboard> <line>");
					}
					break;

				case "remove":
					if(!(count($args) < 3)){
						if($this->plugin->getStore()->getId($args[2]) !== null){
							$scoreboard = new Scoreboard($this->plugin, $args[2], false);
							if($args[1] === "@all"){
								foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
									$scoreboard->removeDisplay($p);
								}
							}else{
								$p = $this->plugin->getServer()->getPlayer($args[1]);
								if($p !== null){
									$scoreboard->removeDisplay($p);
								}else{
									$sender->sendMessage("§cThis player isn't online.");
								}
							}
						}else{
							$sender->sendMessage("§cThere is no scoreboard with that name.");
						}
					}else{
						$sender->sendMessage("§e/scoreboard remove <player / @all> <title of the scoreboard>");
					}
					break;
			}
		}
	}
}