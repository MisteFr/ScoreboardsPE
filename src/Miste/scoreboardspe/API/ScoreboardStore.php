<?php

namespace Miste\scoreboardspe\API;

use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreboardStore{

	/** @var array */
	private $entries;

	/** @var array */
	private $scoreboards;

	/** @var array */
	private $displaySlots;

	/** @var array */
	private $sortOrders;

	/** @var array */
	private $ids;

	/**
	 * @param string $objectiveName
	 * @param int    $line
	 * @param ScorePacketEntry  $entry
	 */

	public function addEntry(string $objectiveName, int $line, ScorePacketEntry $entry){
		$this->entries[$objectiveName][$line] = $entry;
	}

	/**
	 * @param string $objectiveName
	 * @param int    $line
	 */

	public function removeEntry(string $objectiveName, int $line){
		unset($this->entries[$objectiveName][$line]);
	}

	/**
	 * @param string $objectiveName
	 * @param string $displayName
	 * @param string $displaySlot
	 * @param int $sortOrder
	 */

	public function registerScoreboard(string $objectiveName, string $displayName, string $displaySlot, int $sortOrder, int $scoreboardId){
		$this->entries[$objectiveName] = null;
		$this->scoreboards[$displayName] = $objectiveName;
		$this->displaySlots[$objectiveName] = $displaySlot;
		$this->sortOrders[$objectiveName] = $sortOrder;
		$this->ids[$objectiveName] = $scoreboardId;
	}

	/**
	 * @param string $objectiveName
	 * @param string $displayName
	 */

	public function unregisterScoreboard(string $objectiveName, string $displayName){
		unset($this->entries[$objectiveName]);
		unset($this->scoreboards[$displayName]);
		unset($this->displaySlots[$objectiveName]);
		unset($this->sortOrders[$objectiveName]);
		unset($this->ids[$objectiveName]);
	}

	public function getEntries(string $objectiveName) : array{
		return $this->entries[$objectiveName];
	}

	public function entryExist(string $objectiveName, int $line) : bool{
		return isset($this->entries[$objectiveName][$line]);
	}

	public function getId(string $displayName){
		return $this->scoreboards[$displayName] ?? null;
	}

	public function getDisplaySlot(string $objectiveName) : string{
		return $this->displaySlots[$objectiveName];
	}

	public function getSortOrder(string $objectiveName) : int{
		return $this->sortOrders[$objectiveName];
	}

	public function getScoreboardId(string $objectiveName) : int{
		return $this->ids[$objectiveName];
	}
}