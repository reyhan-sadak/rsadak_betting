<?php

require_once 'managers/databaseManager.php';

if($_SERVER["REQUEST_METHOD"] == "GET"){
	$league_id = -1;
	if(isset($_GET["league_id"])){
		$league_id = $_GET["league_id"];
	}
	$teams = DatabaseManager::getInstance()->getAllTeams($league_id);
	$teams_array = array();
	foreach ($teams as $team){
		$team_array = array();
		$team_array[0] = $team->getId();
		$team_array[1] = $team->getName();
		$teams_array[] = $team_array;
	}
	echo json_encode($teams_array);
}

?>