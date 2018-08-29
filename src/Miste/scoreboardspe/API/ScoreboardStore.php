<?php

namespace Miste\scoreboardspe\API;

use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class ScoreboardStore{

	/** @var array */
	private $entries;

	/** @var array */
	private $scoreboards;

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
	 */

	public function registerScoreboard(string $objectiveName, string $displayName){
		$this->entries[$objectiveName] = null;
		$this->scoreboards[$displayName] = $objectiveName;
	}

	/**
	 * @param string $objectiveName
	 * @param string $displayName
	 */

	public function unregisterScoreboard(string $objectiveName, string $displayName){
		unset($this->entries[$objectiveName]);
		unset($this->scoreboards[$displayName]);
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
}