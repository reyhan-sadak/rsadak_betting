<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

function addLeagueForm($league_name_error){
	echo '<form action="addLeague.php" method="post">
	League Name: <input type="text" name="league_name" value="">'.$league_name_error.' <br>
	<input type="submit" value="Create">
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$league_name = $league_name_error = "";
	if(isset($_POST["league_name"])){
		$league_name = $_POST["league_name"];
		if(SessionManager::getInstance()->leagueWithNameExists($league_name)){
			$league_name_error = "League already exists!";
		}else{
			$error = SessionManager::getInstance()->addNewLeague($league_name);
			if($error == HttpStatus::$HTTP_STATUS_UNAUTHORIZED){
				redirect("login.php", "Please login!");
			}else{
				redirect("viewLeagues.php");
			}
		}
	}else{
		$league_name_error = "League name should not be empty!";
		addLeagueForm($league_name_error);
	}
}else{
	addLeagueForm("");
}

pageFooter();

?>