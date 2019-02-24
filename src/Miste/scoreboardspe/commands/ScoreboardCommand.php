<?php

declare(strict_types=1);

namespace Miste\scoreboardspe\commands;

use Miste\scoreboardspe\API\{Scoreboard, ScoreboardAction};
use Miste\scoreboardspe\ScoreboardsPE;
use pocketmine\command\{Command, CommandSender, PluginIdentifiableCommand};
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class ScoreboardCommand
 * @package Miste\scoreboardspe\commands
 */
class ScoreboardCommand extends Command implements PluginIdentifiableCommand{

	/** @var ScoreboardsPE */
	private $plugin;

	/**
	 * ScoreboardCommand constructor.
	 * @param ScoreboardsPE $plugin
	 * @param string        $name
	 */
	public function __construct(ScoreboardsPE $plugin, string $name){
		parent::__construct($name, "Send a scoreboard with a command !", "/scoreboard add <player / @all> <title> <displaySlot (sidebar/list/belowname> <sortOrder (0->ascending/1->descending)>", ["sc", "score"]);
		$this->plugin = $plugin;
		$this->setPermission("scoreboard.cmd");
		$this->setDescription("Send a scoreboard with a command ! (/sc help/create/delete/add/remove/setLine/rmLine)");
		$this->setUsage("/scoreboard help/create/delete/add/remove/setLine/rmLine");
	}

	/**
	 * @return Plugin
	 */
	public function getPlugin() : Plugin{
		return $this->plugin;
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 * @return mixed|void
	 */
	public function execute(CommandSender $sender, $commandLabel, array $args){
		if($sender instanceof Player){
			if(count($args) > 0){
				switch($args[0]){

					case "create":
						if(!(count($args) < 4)){
							if($this->plugin->getStore()->getId($args[1]) === null){
								if(is_numeric($args[3])){
									$scoreboard = new Scoreboard($this->plugin, $args[1], ScoreboardAction::CREATE);
									$scoreboard->create($args[2], $args[3]);
									$sender->sendMessage("§aSuccessfully created scoreboard " . $args[1] . " ! You can now add it to by using /sc add.");
								}else{
									$sender->sendMessage("§cThe sort order need to be 0/1.");
								}
							}else{
								$sender->sendMessage("§cThis scoreboard already exist, you can add it to a player using /scoreboard add");
							}
						}else{
							$sender->sendMessage("§e/scoreboard create <title> <displaySlot (sidebar/list/belowname)> <sortOrder (0->ascending/1->descending)>");
						}
						break;

					case "delete":

						if(!(count($args) < 2)){
							if($this->plugin->getStore()->getId($args[1]) !== null){
								$scoreboard = new Scoreboard($this->plugin, $args[1], ScoreboardAction::MODIFY);
								$scoreboard->delete();
								$sender->sendMessage("§aSuccessfully deleted scoreboard " . $args[1] . ".");
							}else{
								$sender->sendMessage("§cThere is no scoreboard with that name.");
							}
						}else{
							$sender->sendMessage("§e/scoreboard delete <title>");
						}


						break;

					case "add":
						if(!(count($args) < 3)){
							if($this->plugin->getStore()->getId($args[2]) !== null){
								$scoreboard = new Scoreboard($this->plugin, $args[2], ScoreboardAction::MODIFY);
								if($args[1] === "@all"){
									foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
										$scoreboard->addDisplay($p);
									}
									$sender->sendMessage("§aSent " . $args[2] . " to all the online players.");
								}else{
									$p = $this->plugin->getServer()->getPlayer($args[1]);
									if($p !== null){
										$scoreboard->addDisplay($p);
										$sender->sendMessage("§aSent " . $args[2] . " scoreboard to  " . $p->getName());
									}else{
										$sender->sendMessage("§cThis player isn't online.");
									}
								}
							}else{
								$sender->sendMessage("§cThere is no scoreboard with that name.");
							}
						}else{
							$sender->sendMessage("§e/scoreboard add <player / @all> <title>");
						}
						break;

					case "setLine":
						if(!(count($args) < 4)){
							if($this->plugin->getStore()->getId($args[1]) !== null){
								if(is_numeric($args[2])){
									if((int) $args[2] >= 1 && (int) $args[2] <= 15){
										$scoreboard = new Scoreboard($this->plugin, $args[2], ScoreboardAction::MODIFY);
										$scoreboard->setLine((int) $args[2], implode(" ", array_slice($args, 3)));
										$sender->sendMessage("§aSent line number " . $args[2] . " of scoreboard " . $args[1] . " to all the online players.");
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
							$sender->sendMessage("§e/scoreboard setLine <title of the scoreboard> <line> <message>");
						}
						break;

					case "rmLine":
						if(!(count($args) < 3)){
							if($this->plugin->getStore()->getId($args[1]) !== null){
								if(is_numeric($args[2])){
									if((int) $args[2] >= 1 && (int) $args[2] <= 15){
										if($this->plugin->getStore()->entryExist($this->plugin->getStore()->getId($args[1]), $args[2])){
											$scoreboard = new Scoreboard($this->plugin, $args[1], ScoreboardAction::MODIFY);
											$scoreboard->removeLine($args[2]);
										}else{
											$sender->sendMessage("§cThis scoreboard doesn't have line number " . $args[2] . ".");
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
							$sender->sendMessage("§e/scoreboard rmLine <title of the scoreboard> <line>");
						}
						break;

					case "remove":
						if(!(count($args) < 3)){
							if($this->plugin->getStore()->getId($args[2]) !== null){
								$scoreboard = new Scoreboard($this->plugin, $args[2], ScoreboardAction::MODIFY);
								if($args[1] === "@all"){
									foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
										$scoreboard->removeDisplay($p);
									}
									$sender->sendMessage("§aRemoved the display of the scoreboard " . $args[2] . " for all the online players");
								}else{
									$p = $this->plugin->getServer()->getPlayer($args[1]);
									if($p !== null){
										$scoreboard->removeDisplay($p);
										$sender->sendMessage("§aRemoved the display of the scoreboard " . $args[2] . " for " . $p->getName());
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

					case "rename":
						if(!(count($args) < 3)){
							if($this->plugin->getStore()->getId($args[1]) !== null){
								$scoreboard = new Scoreboard($this->plugin, $args[1], ScoreboardAction::MODIFY);
								$scoreboard->rename($args[1], $args[2]);
								$sender->sendMessage("§aRenamed the scoreboard with title " . $args[1] . " to " . $args[2] . " and re sent it to all the viewers.");
							}else{
								$sender->sendMessage("§cThere is no scoreboard with that name.");
							}
						}else{
							$sender->sendMessage("§e/scoreboard rename <old title> <new title>");
						}
						break;

					case "clearLines":
						if(!(count($args) < 2)){
							if($this->plugin->getStore()->getId($args[1]) !== null){
								$scoreboard = new Scoreboard($this->plugin, $args[1], ScoreboardAction::MODIFY);
								$scoreboard->removeLines();
								$sender->sendMessage("§aCleared the lines of the scoreboard with title " . $args[1] . ".");
							}else{
								$sender->sendMessage("§cThere is no scoreboard with that name.");
							}
						}else{
							$sender->sendMessage("§e/scoreboard clearLines <name of the scoreboard>");
						}
						break;

					case "help":
						$sender->sendMessage("§6/scoreboard create <Name of the scoreboard> <displaySlot (sidebar/list/belowname)> <sortOrder (0->ascending/1->descending)>\n§eIt creates the scoreboard and save it.\n\n§6/scoreboard add <player / @all> <title>\n§eIt sends the scoreboard display.\n\n§6/scoreboard setLine <Name of the scoreboard> <line> <message>\n§eIt adds the line you want with the text you want to the scoreboard.§eThe player to which you are sending this setLine have to have received the scoreboard first.\n\n§6/scoreboard rmLine <player / @all> <Name of the scoreboard> <line>\n§eIt removes the line you want of the scoreboard.\n\n§6/scoreboard remove <player / @all> <Name of the scoreboard>\n§eIt removes the scoreboard display from the player.\n\n§6/scoreboard delete <Name of the scoreboard>\n§eIt removes the scoreboard from the database, that means that you wouldn't be able to use it in the future.§eNB: Please note that this command doesn't remove the scoreboard from it's viewers\n\n§6/scoreboard rename <old name> <new name>\n§eIt change the title of the scoreboard and re send it to all the original viewers.");
						break;

					default:
						$sender->sendMessage("§e/scoreboard help/create/delete/add/remove/setLine/rmLine");
						break;
				}
			}else{
				$sender->sendMessage("§e/scoreboard help/create/delete/add/remove/setLine/rmLine");
			}
		}
	}
}