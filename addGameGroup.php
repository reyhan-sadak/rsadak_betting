<?php

require_once 'utils/functions.php';

function addTeamGroupForm($game_group_name_error){
	echo '<form action="addGameGroup.php" method="post">
	Name: <input type="text" name="league_name" value="">'.$game_group_name_error.' <br>
	From date <input type="date" name="from_date"><br>
	To date <input type="date" name="to_date"><br>
	<input type="submit" value="Create">
	</form>';
}
	
redirectIfNotModerator();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$league_name = $game_group_name_error = "";
	if(isset($_POST["league_name"])){
		$league_name = $_POST["league_name"];
		if(SessionManager::getInstance()->leagueWithNameExists($league_name)){
			$game_group_name_error = "League already exists!";
		}else{
			$error = SessionManager::getInstance()->addNewLeague($league_name);
			if($error == HttpStatus::$HTTP_STATUS_UNAUTHORIZED){
				redirect("login.php");
			}else{
				redirect();
			}
		}
	}else{
		$game_group_name_error = "League name should not be empty!";
		addTeamGroupForm($game_group_name_error);
	}
}else{
	addTeamGroupForm("");
}

?>