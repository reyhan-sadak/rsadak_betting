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
			array("index.php", "Home"),
			array("myBets.php", "My Bets")
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

?>