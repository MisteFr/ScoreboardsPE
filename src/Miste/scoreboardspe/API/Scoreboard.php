<?php

namespace Miste\scoreboardspe\API;

use Miste\scoreboardspe\ScoreboardsPE;
use pocketmine\Player;

use pocketmine\network\mcpe\protocol\{
	SetScorePacket, RemoveObjectivePacket, SetDisplayObjectivePacket
};
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard{

	public function __construct(ScoreboardsPE $plugin, string $title, bool $isNew = true){
		$this->plugin = $plugin;
		$this->displayName = $title;
		if(!$isNew){
			$this->objectiveName = $this->plugin->getStore()->getId($title);
			if($this->plugin->getStore()->getId($title) === null){
				$this->objectiveName = uniqid();
				$this->plugin->getStore()->registerScoreboard($this->objectiveName, $this->displayName);
			}
		}else{
			$this->objectiveName = uniqid();
			$this->plugin->getStore()->registerScoreboard($this->objectiveName, $this->displayName);
		}
	}

	const MAX_LINES = 9;

	/** @var ScoreboardsPE */
	private $plugin;

	/** @var string */
	private $objectiveName;

	/** @var string */
	private $displayName;

	/**
	 * @param        $player
	 * @param string $displaySlot (sidebar, list, belowname)
	 * @param int    $sortOrder
	 */

	public function addDisplay(Player $player, string $displaySlot = "sidebar", int $sortOrder = 0){
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = $displaySlot;
		$pk->objectiveName = $this->objectiveName;
		$pk->displayName = $this->displayName;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = $sortOrder;
		$player->sendDataPacket($pk);
	}

	/**
	 * @param        $player
	 */

	public function removeDisplay(Player $player){
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = $this->objectiveName;
		$player->sendDataPacket($pk);
		$this->plugin->getStore()->unregisterScoreboard($this->objectiveName, $this->displayName);
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
		$entry->scoreboardId = $line;
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
					$entry->scoreboardId = $i;
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
		$entry->scoreboardId = $line;
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
		$entry->scoreboardId = $line;
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);

		$this->plugin->getStore()->removeEntry($this->objectiveName, ($line - 1));
	}
}