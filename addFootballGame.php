<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

function addGameForm($group_id, $group_name, $leagues){
	echo '<form action="addFootballGame.php" method="post" id="gameForm" name="gameForm">
	Adding game for '.$group_name.'
	<input type="hidden" name="group_id" value="'.$group_id.'">
	<select id="league_id" name="league_id" form="gameForm" onchange="getTeamsByLeague(this)">';
	echo '<option value="-1">-----</option>';
	foreach ($leagues as $key => $value){
		echo '<option value='.$key.'>'.$value.'</option>';
	}
	echo '</select>
	<select name="host_team_id" form="gameForm">
		<option value=-1>-----</option>
	</select>
	<select name="guest_team_id" form="gameForm">
		<option value=-1>-----</option>
	</select>
	<input type="datetime" name="datetime" value="">
	<input type="submit" value="Add team">
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
addScripts(["functions"]);
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(true){//isset($_POST["league_id"]) && isset($_POST["group_id"]) && isset($_POST["host_team_id"]) && isset($_POST["guest_team_id"])){
		$league_id = $_POST["league_id"];
		$group_id = $_POST["group_id"];
		$host_team_id = $_POST["host_team_id"];
		$guest_team_id = $_POST["guest_team_id"];
		$error = SessionManager::getInstance()->addNewGame($group_id, $host_team_id, $guest_team_id);
		if($error == HttpStatus::$HTTP_STATUS_OK){
			redirect("viewGroups.php", "Game added successfully", false);
		}
	}else{
		redirect("viewGroups.php", "Too few params");
	}
}else if($_SERVER["REQUEST_METHOD"] == "GET"){
	if(isset($_GET["group_id"])){
		$group = DatabaseManager::getInstance()->getGameGroupById((int)$_GET["group_id"]);
		if($group){
			$leagues_array = array();
			$leagues = DatabaseManager::getInstance()->getAllLeagues();
			foreach ($leagues as $league){
				$leagues_array[$league->getId()] = $league->getName();
			}
			addGameForm($group->getId(), $group->getName(), $leagues_array);
		}else{
			redirect("viewGroups.php", "Group not found");
		}
	}else{
		redirect("viewGroups.php", "Group id not set");
	}
	
}
else{
	redirect(null, "Unsupported method");
}

pageFooter();

?>