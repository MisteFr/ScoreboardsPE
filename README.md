# ScoreboardPE

A PocketMine-MP plugin that let you easily send scoreboards by using command on server who comes with a rich API for developers for MCBE 1.7.
### Commands

The command to use the soreboards is ``scoreboard``, his aliases are ``sc`` and ``score``.

* /scoreboard create < Name of the scoreboard > < displaySlot (sidebar/list/belowname) > < sortOrder (0->ascending/1->descending) >

  - **DisplaySlot**  
  It's where the scoreboard is displayed on your screen
    - sidebar: the default scoreboard position (at the right of your screen).
    - list: above the player list.
    - belowname: below the name of the player (not implemented in 1.7.0.2 beta).
  - **Sort Order**  
  This is the order in which your entries are classified.
    - 0: The score is from the smallest to the biggest
    - 1: The score is from the biggest to the smallest
    
It creates the scoreboard and save it.  

* /scoreboard add < player / @all > < title >

It sends the scoreboard display.  
For example: ``/scoreboard add @all Miste
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple1.png" width="100">

* /scoreboard setLine < player / @all > < Name of the scoreboard > < line > < message >

It adds the line you want with the text you want to the scoreboard.  
The player to which you are sending this setLine have to have received the scoreboard first.  
**NB**: You can't add two lines with the same message. If you don't have any lines to your scoreboard and you add the line 4 it will add 3 empty lines too.  
For example: ``/scoreboard setLine Misteboss1 Miste 1 My name is Miste
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple2.png" width="200">  

* /scoreboard rmLine < player / @all > < Name of the scoreboard > < line >

It removes the line you want of the scoreboard.  
For example: ``/scoreboard rmLine Misteboss1 Miste 
`` display that:

<img src="https://github.com/MisteFr/ScoreboardsPE/raw/master/img/exemple1.png" width="100">

* /scoreboard rename < old name > < new name >

It renames your scoreboard and re send it to all the viewers.

* /scoreboard remove < player / @all > < Name of the scoreboard >

It removes the scoreboard from the player.  
For example: ``/scoreboard remove @all Miste`` will remove the scoreboard from all the online players.    

* /scoreboard delete < Name of the scoreboard >

It removes the scoreboard from the database, that means that you wouldn't be able to use it in the future.  
**NB**: Please note that this command doesn't remove the scoreboard from it's viewers  
Example: ``/scoreboard delete Miste`` will remove the scoreboard.

* /scoreboard help

It gives you the list of available commands and how to use them.

### API
```
use Miste\scoreboardspe\API\{
	Scoreboard, ScoreboardDisplaySlot, ScoreboardSort, ScoreboardAction
};

/*
    Create a scoreboard with the display name Miste
    The id is created and saved linked with the display name internally
*/    

$scoreboard = new Scoreboard($this->getServer()->getPluginManager()->getPlugin("ScoreboardsPE")->getPlugin(), "Miste", ScoreboardAction::CREATE);
$scoreboard->create(ScoreboardDisplaySlot::SIDEBAR, ScoreboardSort::DESCENDING);

/*
    If you want to get back the Scoreboard instance of one of your scoreboard do as you were creating on and add the ScoreboardAction::MODIFY at the end of the constructor.
    Here you will get back the instance of the scoreboard we created above
*/

$scoreboard = new Scoreboard($this->getServer()->getPluginManager()->getPlugin("ScoreboardsPE")->getPlugin(), "Miste", ScoreboardAction::MODIFY);

/*
    Send the scoreboard to the player (without any lines)
*/

$scoreboard->addDisplay($player, ScoreboardDisplaySlot::SIDEBAR, ScoreboardSort::ASCENDING);

/*
    Add lines to the scoreboard O
    If you send line 1 then line 4 the plugin will automatically send two empty lines between.
    The max number of lines is 15, send more than 15 can bring issues when removing some
*/

$scoreboard->setLine($player, 1, "line1");
$scoreboard->setLine($player, 2, "line2");
$scoreboard->setLine($player, 5, "line5");
$scoreboard->setLine($player, 8, "line8");
$scoreboard->setLine($player, 9, "line9");

/*
    Remove the line you choose from the scoreboard. This line will be removed from all the viewers.
*/

$scoreboard->removeLine(2);

/*
    Remove all the lines from the scoreboard. The lines will be removed from all the viewers.
*/

$scoreboard->removeLines();

/*
    Rename the scoreboard called Miste to Miste1 and re send it to all the viewers
*/
    
$scoreboard->rename("Miste", "Miste1");

/*
    Remove the scoreboard from the display of the player
*/

$scoreboard->removeDisplay($player);

/*
    Delete the scoreboard from the database (in order to save RAM)
*/

$scoreboard->delete();

/*
    Return an array with all the players that can view the scoreboards
    **NB** Players are automatically removed from this array if the player quits the server or if you remove the scoreboard for this player
*/

$scoreboard->getViewers();


```
