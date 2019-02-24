<?php

declare(strict_types=1);

namespace Miste\scoreboardspe\API;

/**
 * Interface ScoreboardDisplaySlot
 * @package Miste\scoreboardspe\API
 */
interface ScoreboardDisplaySlot{

	public const LIST = "list";
	public const SIDEBAR = "sidebar";
	public const BELOWNAME = "belowname"; //not working in 1.7.0.2
}