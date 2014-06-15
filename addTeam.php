<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

function addTeamForm($leagues, $team_name_error){
	echo '<form action="addTeam.php" method="post" id="teamForm">
	Team Name: <input type="text" name="team_name" value="">'.$team_name_error.'
	<select name="league_id" form="teamForm">';
	foreach ($leagues as $key => $value){
	echo '<option value='.$key.'>'.$value.'</option>';
	}
	echo '</select>
	<input type="submit" value="Add team">
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
controlPanel();

$leagues_array = array();
$leagues = DatabaseManager::getInstance()->getAllLeagues();
foreach ($leagues as $league){
	$leagues_array[$league->getId()] = $league->getName();
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$league_id = $team_name = $team_name_error = "";
	if(isset($_POST["league_id"]) && isset($_POST["team_name"])){
		$league_id = (int)$_POST["league_id"];
		$team_name = $_POST["team_name"];
		if(SessionManager::getInstance()->leagueWithIdExists($league_id)){
			if(SessionManager::getInstance()->teamWithNameForLeagueExists($team_name, $league_id)){
				$team_name_error = "Team ".$team_name." already exists in this league!";
				addTeamForm($leagues_array, $team_name_error);
			}else{
				$error = SessionManager::getInstance()->addNewTeam($team_name, $league_id);
				if($error == HttpStatus::$HTTP_STATUS_UNAUTHORIZED){
					redirect("login.php", "Please login!");
				}else{
					redirect("viewTeams.php");
				}
			}
		}else{
			// this league does not exist
		}
	}else{
		$team_name_error = "Team name should not be empty!";
		addTeamForm($leagues_array, $team_name_error);
	}
}else{
	addTeamForm($leagues_array, "");
}

pageFooter();

?>