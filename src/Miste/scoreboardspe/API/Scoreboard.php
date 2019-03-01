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
				$this->plugin->getLogger()->info("The scoreboard $title already exists ! Therefore we are going to use its already existing data.");
				$this->objectiveName = $this->plugin->getStore()->getId($title);
				$this->displaySlot = $this->plugin->getStore()->getDisplaySlot($this->objectiveName);
				$this->sortOrder = $this->plugin->getStore()->getSortOrder($this->objectiveName);
				$this->scoreboardId = $this->plugin->getStore()->getScoreboardId($this->objectiveName);
			}
		}else{
			if($this->plugin->getStore()->getId($title) !== null){
				$this->objectiveName = $this->plugin->getStore()->getId($title);
				$this->displaySlot = $this->plugin->getStore()->getDisplaySlot($this->objectiveName);
				$this->sortOrder = $this->plugin->getStore()->getSortOrder($this->objectiveName);
				$this->scoreboardId = $this->plugin->getStore()->getScoreboardId($this->objectiveName);
			}else{
				$this->plugin->getLogger()->info("The scoreboard $title doesn't exist.");
			}
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

	/** @var bool */
	private $padding;

	/**
	 * @param        $player
	 */

	public function addDisplay(Player $player) : void{
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = $this->displaySlot;
		$pk->objectiveName = $this->objectiveName;
		$pk->displayName = $this->displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = $this->sortOrder;
		$player->sendDataPacket($pk);

		$this->plugin->getStore()->addViewer($this->objectiveName, $player->getName());

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

	public function removeDisplay(Player $player) : void{
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $this->objectiveName;
		$player->sendDataPacket($pk);

		$this->plugin->getStore()->removeViewer($this->objectiveName, $player->getName());
	}

	/**
	 * @param int    $line
	 * @param string $message
	 */

	public function setLine(int $line, string $message, bool $padding = true) : void{
		if(!$this->plugin->getStore()->entryExist($this->objectiveName, ($line - 1)) && $line !== 1){
			for($i = 1; $i <= ($line - 1); $i++){
				if(!$this->plugin->getStore()->entryExist($this->objectiveName, ($i - 1))){
					$entry = new ScorePacketEntry();
					$entry->objectiveName = $this->objectiveName;
					$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
					$entry->customName = str_repeat(" ", $i); //You can't send two lines with the same message
					$entry->score = self::MAX_LINES - $i;
					$entry->scoreboardId = ($this->scoreboardId + $i);
					$pk->entries[] = $entry;
					$this->plugin->getStore()->addEntry($this->objectiveName, ($i - 1), $entry);
				}
			}
		}

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $this->objectiveName;
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$this->padding ? $entry->customName = str_pad($message, ((strlen($this->displayName) * 2) - strlen($message))) : $entry->customName = $message;
		$entry->score = self::MAX_LINES - $line;
		$entry->scoreboardId = ($this->scoreboardId + $line); //You can't send two lines with the same sc id
		$pk->entries[] = $entry;
		$this->plugin->getStore()->addEntry($this->objectiveName, ($line - 1), $entry);

		foreach($this->plugin->getStore()->getViewers($this->objectiveName) as $name){
			$p = $this->plugin->getServer()->getPlayer($name);
			if($p !== null){
				$pk = new SetScorePacket();
				$pk->type = SetScorePacket::TYPE_CHANGE;
				foreach($this->plugin->getStore()->getEntries($this->objectiveName) as $index => $entry){
					$pk->entries[$index] = $entry;
				}
				$p->sendDataPacket($pk);
			}
		}
	}

	/**
	 * @param int    $line
	 */

	public function removeLine(int $line) : void{
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_REMOVE;

		$entry = new ScorePacketEntry();
		$entry->objectiveName = $this->objectiveName;
		$entry->score = self::MAX_LINES - $line;
		$entry->scoreboardId = ($this->scoreboardId + $line);
		$pk->entries[] = $entry;

		foreach($this->plugin->getStore()->getViewers($this->objectiveName) as $name){
			$p = $this->plugin->getServer()->getPlayer($name);
			$p->sendDataPacket($pk);
		}

		$this->plugin->getStore()->removeEntry($this->objectiveName, $line);
	}

	public function removeLines() : void{
		$pk = new SetScorePacket();
		$pk->type = SetScorePacket::TYPE_REMOVE;

		for($line = 0; $line <= self::MAX_LINES; $line++){
			$entry = new ScorePacketEntry();
			$entry->objectiveName = $this->objectiveName;
			$entry->score = $line;
			$entry->scoreboardId = ($this->scoreboardId + $line);
			$pk->entries[] = $entry;
		}

		foreach($this->plugin->getStore()->getViewers($this->objectiveName) as $name){
			$p = $this->plugin->getServer()->getPlayer($name);
			$p->sendDataPacket($pk);
		}

		$this->plugin->getStore()->removeEntries($this->objectiveName);
	}

	/**
	 * @param string $displaySlot
	 * @param int    $sortOrder
	 */

	public function create(string $displaySlot, int $sortOrder, bool $padding = true) : void{
		$this->displaySlot = $displaySlot;
		$this->sortOrder = $sortOrder;
		$this->padding = $padding;
		$this->scoreboardId = mt_rand(1, 100000);
		$this->plugin->getStore()->registerScoreboard($this->objectiveName, $this->displayName, $this->displaySlot, $this->sortOrder, $this->scoreboardId);
	}

	public function delete() : void{
		$this->plugin->getStore()->unregisterScoreboard($this->objectiveName, $this->displayName);
	}

	/**
	 * @param string $oldName
	 * @param string $newName
	 */

	public function rename(string $oldName, string $newName) : void{
		$this->displayName = $newName;

		$this->plugin->getStore()->rename($oldName, $newName);

		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $this->objectiveName;

		$pk2 = new SetDisplayObjectivePacket();
		$pk2->displaySlot = $this->displaySlot;
		$pk2->objectiveName = $this->objectiveName;
		$pk2->displayName = $this->displayName;
		$pk2->criteriaName = "dummy";
		$pk2->sortOrder = $this->sortOrder;

		$pk3 = new SetScorePacket();
		$pk3->type = SetScorePacket::TYPE_CHANGE;
		foreach($this->plugin->getStore()->getEntries($this->objectiveName) as $index => $entry){
			$pk3->entries[$index] = $entry;
		}

		foreach($this->plugin->getStore()->getViewers($this->objectiveName) as $name){
			$p = $this->plugin->getServer()->getPlayer($name);
			$p->sendDataPacket($pk);
			$p->sendDataPacket($pk2);
			$p->sendDataPacket($pk3);
		}
	}

	/**
	 * @return array
	 */

	public function getViewers() : array{
		return $this->plugin->getStore()->getViewers($this->objectiveName);
	}

	/**
	 * @return array
	 */

	public function getEntries() : array{
		return $this->plugin->getStore()->getEntries($this->objectiveName);
	}
}