<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

function addTeamGroupForm($game_group_name_error){
	echo '<form action="addGameGroup.php" method="post">
	Name: <input type="text" name="group_name" value=""></input>'.$game_group_name_error.'
	From date <input type="text" name="from_date" id="datepicker_from" value=""></input>
	To date <input type="text" name="to_date" id="datepicker_to" value=""></input>
	<input type="submit" value="Create"></input>
	</form>';
}
	
redirectIfNotModerator();

pageHeader();
addScripts(["functions"]);
datePickerScript(["datepicker_from", "datepicker_to"]);
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["group_name"]) && isset($_POST["from_date"]) && isset($_POST["to_date"])){
		$group_name = $_POST["group_name"];
		$from_date = DateTime::createFromFormat('m/d/Y', $_POST["from_date"]);
		$to_date = DateTime::createFromFormat('m/d/Y', $_POST["to_date"]);
		if($from_date > $to_date){
			addTeamGroupForm("End time should be greater or equal to start time");
		}
		if($from_date == null){
			addTeamGroupForm("From date is not valid");
		}else if($to_date == null){
			addTeamGroupForm("To date is not valid");
		}else{
			if(SessionManager::getInstance()->gameGroupWithNameExists($group_name)){
				addTeamGroupForm("Group with this name already exists!");
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