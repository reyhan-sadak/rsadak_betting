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
	<input type="text" name="date" id="datepicker" value="">
	<select name="hours" form="gameForm">';
	for($hour = 0; $hour <= 23; $hour++){
		$hour_str = (string)$hour;
		if($hour < 10){
			$hour_str = "0".(string)$hour;
		}
		echo '<option value="'.$hour_str.'">'.$hour_str.'</option>';
	}
	echo
	'</select>
	<select name="minutes" form="gameForm">';
	for($minute = 0; $minute <= 59; $minute++){
		if($minute % 5 != 0){
			continue;
		}
		$minute_str = (string)$minute;
		if($minute < 10){
			$minute_str = "0".(string)$minute;
		}
		echo '<option value="'.$minute_str.'">'.$minute_str.'</option>';
	}
	echo
	'</select>
	<input type="submit" value="Add Game">
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
addScripts(["functions"]);
datePickerScript();
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["league_id"]) && isset($_POST["group_id"]) && isset($_POST["host_team_id"]) && isset($_POST["guest_team_id"]) && isset($_POST["date"])  && isset($_POST["hours"])  && isset($_POST["minutes"])){
		$league_id = $_POST["league_id"];
		$group_id = $_POST["group_id"];
		$host_team_id = $_POST["host_team_id"];
		$guest_team_id = $_POST["guest_team_id"];
		$date = $_POST["date"];
		$hours = $_POST["hours"];
		$minutes = $_POST["minutes"];
		$date_time_debug = $date.' '.$hours.':'.$minutes.':00';
		echo $date_time_debug;
		$datetime = DateTime::createFromFormat('m/d/Y H:i:s', $date.' '.$hours.':'.$minutes.':00');
		$error = SessionManager::getInstance()->addNewGame($group_id, $host_team_id, $guest_team_id, $datetime);
		if($error == HttpStatus::$HTTP_STATUS_OK){
			redirect("viewGroups.php", "Game added successfully", false);
		}else{
			redirect("viewGroups.php", "Game was not added", false);
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