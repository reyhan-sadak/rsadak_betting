<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

function addTeamGroupForm($game_group_name_error, $start_date_error, $end_date_error){
	echo '<form action="addGameGroup.php" method="post">
	Name: <input type="text" name="group_name" value="">'.$game_group_name_error.' <br>
	From date <input type="date" name="from_date">'.$start_date_error.'<br>
	To date <input type="date" name="to_date">'.$end_date_error.'<br>
	<input type="submit" value="Create">
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["group_name"]) && isset($_POST["from_date"]) && isset($_POST["to_date"])){
		$group_name = $_POST["group_name"];
		$from_date = DateTime::createFromFormat('Y-m-d', $_POST["from_date"]);
		$to_date = DateTime::createFromFormat('Y-m-d', $_POST["to_date"]);
		if($from_date > $to_date){
			addTeamGroupForm("End time should be greater or equal to start time", "YYYY-MM-DD", "YYYY-MM-DD");
		}
		if($from_date == null){
			addTeamGroupForm("From date is not valid", "YYYY-MM-DD", "YYYY-MM-DD");
		}else if($to_date == null){
			addTeamGroupForm("To date is not valid", "YYYY-MM-DD", "YYYY-MM-DD");
		}else{
			if(SessionManager::getInstance()->gameGroupWithNameExists($group_name)){
				addTeamGroupForm("Group with this name already exists!", "YYYY-MM-DD", "YYYY-MM-DD");
			}else{
				$error = SessionManager::getInstance()->addNewGameGroup($group_name, $from_date, $to_date);
				if($error == HttpStatus::$HTTP_STATUS_OK){
					redirect('viewGroups.php', "The game group has been added", false);
				}else{
					redirect('viewGroups.php', "The game group has not been added");
				}
			}
		}
		
	}else{
		addTeamGroupForm("", "YYYY-MM-DD", "YYYY-MM-DD");
	}
}else{
	addTeamGroupForm("", "YYYY-MM-DD", "YYYY-MM-DD");
}

pageFooter();

?>