<?php

require_once 'managers/sessionManager.php';
require_once 'utils/functions.php';

static $scriptsFolder = "js";

static $noUserControlPanel = array(
			array("login.php", "Login"),
			array("register.php", "Register")
		);

static $normalUserControlPanel = array(
			array("logout.php", "Logout"),
			array("index.php", "Home")
		);
static $moderatorControlPanel = array(
			// new content
			array("addLeague.php", "Add League"),
			array("addTeam.php", "Add Team"),
			array("addGameGroup.php", "Add Group"),
			array("addFootballGame.php", "Add FootballMatch"),
			
			// existing content
			array("viewLeagues.php", "View Leagues"),
			array("viewTeams.php", "View Teams"),
			array("viewGroups.php", "View Groups"),
			array("viewFootballGames.php", "View Games"),
		);
static $adminControlPanel = array(
			array("viewUsers.php", "View users")
		);

function pageHeader(){
	echo
	'<html>
	<header>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	</header>
	<body>';
	showHeaderMessage();
}

function pagefooter(){
	echo
	'</body>
	<footer>
	</footer>
	</html>';
}

function showHeaderMessage(){
	if($_SERVER["REQUEST_METHOD"] == "GET"){
		if(isset($_GET["errorMsg"])){
			echo $_GET["errorMsg"].'<br>'; // todo show in a separate div
		}else if(isset($_GET["msg"])){
			echo $_GET["msg"].'<br>'; // todo show in a separate div
		}
	}
}

function controlPanel(){
	$file_name = getFileNameFromFilePath($_SERVER['SCRIPT_NAME']);
	global $noUserControlPanel;
	global $normalUserControlPanel;
	global $moderatorControlPanel;
	global $adminControlPanel;
	echo '<div id="controlPanel">';
	echo '<table id="controlPanelTable">';
	echo '<tr>';
	$current_user = SessionManager::getInstance()->getCurrentUser();
	if($current_user != null){
		echo '<th>Hello, '.$current_user->getName().'!</th>';
		foreach($normalUserControlPanel as $panel){
			if($file_name == $panel[0]){
				echo '<th>'.$panel[1].'</th>';
			}else{
				echo '<th><a href="'.$panel[0].'"> '.$panel[1].' </a></th>';
			}
		}
		if($current_user->isModerator()){
			foreach($moderatorControlPanel as $panel){
				if($file_name == $panel[0]){
					echo '<th>'.$panel[1].'</th>';
				}else{
					echo '<th><a href="'.$panel[0].'"> '.$panel[1].' </a></th>';
				}
			}
		}
		if($current_user->isAdmin()){
			foreach($adminControlPanel as $panel){
				if($file_name == $panel[0]){
					echo '<th>'.$panel[1].'</th>';
				}else{
					echo '<th><a href="'.$panel[0].'"> '.$panel[1].' </a></th>';
				}
			}
		}
	}else {
		foreach($noUserControlPanel as $panel){
			if($file_name == $panel[0]){
				echo '<th>'.$panel[1].'</th>';
			}else{
				echo '<th><a href="'.$panel[0].'"> '.$panel[1].' </a></th>';
			}
		}
	}
	echo '</tr>';
	echo '</table>';
	echo '</div>';
}

function addScripts($scriptnames){
	global $scriptsFolder;
	echo '<script src="'.$scriptsFolder.'/'.'jquery-2.1.1.js"></script>';
	foreach ($scriptnames as $scriptname){
		echo '<script src="'.$scriptsFolder.'/'.$scriptname.'.js"></script>';
	}
}

function datePickerScript($inputIds=[]){
	if(count($inputIds)==0){
		$inputIds[] = "datepicker";
	}
	echo '
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script>
	$(function() {';
	foreach($inputIds as $inputId){
		echo '$( "#'.$inputId.'" ).datepicker();';
	}
	echo '});
	</script>
	';
}

function getSortedMatchGroups(){
	$started_groups = DatabaseManager::getInstance()->getAllGroups(DatabaseManager::$MATCH_GROUP_STATUS_STARTED);
	$upcoming_groups = DatabaseManager::getInstance()->getAllGroups(DatabaseManager::$MATCH_GROUP_STATUS_UPCOMING);
	$ended_groups = DatabaseManager::getInstance()->getAllGroups(DatabaseManager::$MATCH_GROUP_STATUS_ENDED);
	$result = array();
	foreach ($started_groups as $started_group){
		$result[] = $started_group;
	}
	foreach ($upcoming_groups as $upcoming_group){
		$result[] = $upcoming_group;
	}
	foreach ($ended_groups as $ended_group){
		$result[] = $ended_group;
	}
	return $result;
}

?>