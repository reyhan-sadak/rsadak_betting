<?php

require_once 'basePage.php';
require_once 'utils/functions.php';

function gamesTable($games, $is_single_group, $is_admin=false){
	echo '<table id="gamesTable" border=1>';
	echo '<tr>';
	echo '<th>Host</th>';
	echo '<th>Guest</th>';
	echo '<th>Host goals</th>';
	echo '<th>Guest goals</th>';
	echo '<th>Date/Time</th>';
	if($is_single_group == false){
		echo '<th>Group</th>';
	}
	if($is_admin){
		echo '<th>Creator</th>';
		echo '<th>Created</th>';
		echo '<th>Last Updated</th>';
	}
	echo '</tr>';
	foreach ($games as $game){
		echo '<tr id="'.$game->getId().'">';
		echo '<th>'.$game->getHostTeamName().'</th>';
		echo '<th>'.$game->getGuestTeamName().'</th>';
		echo '<th id="hostScore">';
		echo '<input type="text" class="scoreInput" name="hostScore" value="'.$game->getHostScore().'">';
		echo '</th>';
		echo '<th id="guestScore">';
		echo '<input type"text" class="scoreInput" name="guestScore" value="'.$game->getGuestScore().'">';
		echo '</th>';
		echo '<th>'.$game->getDateAndTime().'</th>';
		if($is_single_group == false){
			echo '<th>'.$game->getMatchGroupName().'</th>';
		}
		if($is_admin){
			echo '<th>'.$game->getCreatorName().'</th>';
			echo '<th>'.$game->getCreatedTime().'</th>';
			echo '<th>'.$game->getUpdatedTime().'</th>';
		}
		echo '<th><button type="button" onclick="updateFootballGame('.$game->getId().')">Update</button></th>';
		echo '</tr>';
	}
	echo '</table>';
}

pageHeader();
addScripts(["functions"]);
controlPanel();

$group_id = null;
if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["group_id"])){
		$group_id = $_POST["group_id"];
	}
}else if ($_SERVER["REQUEST_METHOD"] == "GET"){
	if(isset($_GET["group_id"])){
		$group_id = $_GET["group_id"];
	}
}

$games = DatabaseManager::getInstance()->getSortedMatchesByGroupId($group_id);
gamesTable($games, $group_id!=null);

pageFooter();

?>