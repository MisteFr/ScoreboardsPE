# ScoreboardPE

A PocketMine-MP plugin that let you easily send scoreboards by using command on server who comes with a rich API for developers.

**NB**: As scoreboards are going to be added in 1.7, this plugin only works with 1.7.0.2 beta.
### Commands

The command to use the soreboards is ``scoreboard``, his aliases are ``sc`` and ``score``.

* /scoreboard add < player / @all > < scoreboard name > < displaySlot (sidebar/list/belowname) > < sortOrder (0->ascending/1->descending) >

  - **DisplaySlot**  
  It's where the scoreboard is displayed on your screen
    - sidebar: the default scoreboard position (at the right of your screen).
    - list: above the player list.
    - belowname: below the name of the player (not implemented in 1.7.0.2 beta).
  - **Sort Order**  
  This is the order in which your entries are classified.
    - 0: The score is from the smallest to the biggest
    - 1: The score is from the biggest to the smallest
    
It adds the scoreboard to the player.  
For exemple ``/scoreboard add Misteboss1 Miste sidebar 1
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple1.png" width="100">

* /scoreboard addLine < player / @all > < Name of the scoreboard > < line > < message >

It adds the line you want with the text you want to the scoreboard.  
The player to which you are sending this addLine have to have received the scoreboard first.  
**NB**: You can't add two lines with the same message. If you don't have any lines to your scoreboard and you add the line 4 it will add 3 empty lines too.  
For exemple ``/scoreboard addLine Misteboss1 Miste 1 My name is Miste
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple2.png" width="200">  

* /scoreboard rmLine < player / @all > < Name of the scoreboard > < line >

It removes the line you want of the scoreboard.  
For exemple ``/scoreboard rmLine Misteboss1 Miste 1
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple1.png" width="100">

* /scoreboard remove < player / @all > < Name of the scoreboard >

It removes the scoreboard from the player.  
For exemple ``/scoreboard rmLine Misteboss1 Miste 1`` will remove the scoreboard.

### API
```
use Miste\scoreboardspe\API\{
	Scoreboard, ScoreboardDisplaySlot, ScoreboardSort
};

/*
    Create a scoreboard with the display name Miste
    The id is created and saved linked with the display name internally
*/    

$scoreboard = new Scoreboard($this->getServer()->getPluginManager()->getPlugin("ScoreboardsPE)->getPlugin(), "Miste");

/*
    If you want to reget the Scoreboard instance of one of your scoreboard do as you were creating on and add the bool false
    Here you will get back the instance of the scoreboard we created above
*/

$scoreboard = new Scoreboard($this->getServer()->getPluginManager()->getPlugin("ScoreboardsPE)->getPlugin(), "Miste", false);

/*
    Send the scoreboard to the player (without any lines)
*/

$scoreboard->addDisplay($player, ScoreboardDisplaySlot::SIDEBAR, ScoreboardSort::ASCENDING);

/*
    Add lines to the scoreboard O
    If you send line 1 then line 4 the plugin will automatically send two empty lines between.
 
*/

$scoreboard->setLine($player, 1, "line1");
$scoreboard->setLine($player, 2, "line2");
$scoreboard->setLine($player, 5, "line5");
$scoreboard->setLine($player, 8, "line8");
$scoreboard->setLine($player, 9, "line9");

/*
    Remove the line you choose from the scoreboard
*/

$scoreboard->removeLine($player, 2);

/*
    Remove the scoreboard from the display of the player
*/

$scoreboard->removeDisplay($player);


```