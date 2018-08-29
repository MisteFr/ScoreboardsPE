<?php

namespace Miste\scoreboardspe\API;

use Miste\scoreboardspe\ScoreboardsPE;
use pocketmine\Player;

use pocketmine\network\mcpe\protocol\{
	SetScorePacket, RemoveObjectivePacket, SetDisplayObjectivePacket
};
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard{

	public function __construct(ScoreboardsPE $plugin, string $title, int $action){
		$this->plugin = $plugin;
		$this->displayName = $title;
		if($action === ScoreboardAction::CREATE){
			if($this->plugin->getStore()->getId($title) === null){
				$this->objectiveName = uniqid();
			}else{
				$this->objectiveName = $this->plugin->getStore()->getId($title);
				$this->displaySlot = $this->plugin->getStore()->getDisplaySlot($this->objectiveName);
				$this->sortOrder = $this->plugin->getStore()->getSortOrder($this->objectiveName);
				$this->scoreboardId = $this->plugin->getStore()->getScoreboardId($this->objectiveName);
			}
		}else{
			$this->objectiveName = $this->plugin->getStore()->getId($title);
			$this->displaySlot = $this->plugin->getStore()->getDisplaySlot($this->objectiveName);
			$this->sortOrder = $this->plugin->getStore()->getSortOrder($this->objectiveName);
			$this->scoreboardId = $this->plugin->getStore()->getScoreboardId($this->objectiveName);
		}
	}

	const MAX_LINES = 15;

	/** @var ScoreboardsPE */
	private $plugin;

	/** @var string */
	private $objectiveName;

	/** @var string */
	private $displayName;

	/** @var string */
	private $displaySlot;

	/** @var int */
	private $sortOrder;

	/** @var int */
	private $scoreboardId;

	/**
	 * @param        $player
	 */

	public function addDisplay(Player $player){
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = $this->displaySlot;
		$pk->objectiveName = $this->objectiveName;
		$pk->displayName = $this->displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = $this->sortOrder;
		$player->sendDataPacket($pk);

		/*
		 	I am not sure of what is exactly the belowname displaySlot
		 */

		if($this->displaySlot === "belowname"){
			$player->setScoreTag($this->displayName);
		}
	}

	/**
	 * @param        $player
	 */

	public function removeDisplay(Player $player){
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $this->objectiveName;
		$player->sendDataPacket($pk);
	}

	/**
	 * @param        $player
	 * @param int    $line
	 * @param string $message
	 */

	public function setLine(Player $player, int $line, string $message){
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_REMOVE;

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $this->objectiveName;
		$entry->score = self::MAX_LINES - $line;
		$entry->scoreboardId = ($this->scoreboardId + $line);
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);


		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_CHANGE;

		if(!$this->plugin->getStore()->entryExist($this->objectiveName, ($line - 2)) && $line !== 1){
			for($i = 1; $i <= ($line - 1); $i++){
				if(!$this->plugin->getStore()->entryExist($this->objectiveName, ($i - 1))){
					$entry = new ScorePacketEntry();
					$entry->objectiveName = $this->objectiveName;
					$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
					$entry->customName = str_repeat(" ", $i); //You can't send two lines with the same message
					$entry->score = self::MAX_LINES - $i;
					$entry->scoreboardId = ($this->scoreboardId + $i - 1);
					$pk->entries[] = $entry;
					$this->plugin->getStore()->addEntry($this->objectiveName, ($i - 1), $entry);
				}
			}
		}

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $this->objectiveName;
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = self::MAX_LINES - $line;
		$entry->scoreboardId = ($this->scoreboardId + $line);
		$pk->entries[] = $entry;
		$this->plugin->getStore()->addEntry($this->objectiveName, ($line - 1), $entry);
		$player->sendDataPacket($pk);
	}

	/**
	 * @param        $player
	 * @param int    $line
	 */

	public function removeLine(Player $player, int $line){
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_REMOVE;

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $this->objectiveName;
		$entry->score = self::MAX_LINES - $line;
		$entry->scoreboardId = ($this->scoreboardId + $line);
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);

		$this->plugin->getStore()->removeEntry($this->objectiveName, $line);
	}

	/**
	 * @param string 	$displaySlot
	 * @param int    	$sortOrder
	 */

	public function create(string $displaySlot, int $sortOrder){
		$this->displaySlot = $displaySlot;
		$this->sortOrder = $sortOrder;
		$this->scoreboardId = mt_rand(1, 100000);
		$this->plugin->getStore()->registerScoreboard($this->objectiveName, $this->displayName, $this->displaySlot, $this->sortOrder, $this->scoreboardId);
	}

	public function delete(){
		$this->plugin->getStore()->unregisterScoreboard($this->objectiveName, $this->displayName);
	}
}