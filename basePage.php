<?php

require_once 'utils/functions.php';

static $scriptsFolder = "js";
static $s_default_color = '#FFFFFF ';
static $s_correct_result_color = '00FF00';
static $s_correct_result_winner_color = 'FFFF00';
static $s_incorrect_result_color = 'FF0000';

static $noUserControlPanel = array(
			array("index.php", "Home"),
			array("login.php", "Login"),
			array("register.php", "Register")
		);

static $normalUserControlPanel = array(
			array("logout.php", "Logout"),
			array("changePassword.php", "Change Password")
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
	<link rel="stylesheet" type="text/css" href="resources/styles.css">
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
			echo '<div id="errorMessage">';
			echo $_GET["errorMsg"].'<br>';
			echo '</div>';
		}else if(isset($_GET["msg"])){
			echo '<div id="successMessage">';
			echo $_GET["msg"].'<br>';
			echo '</div>';
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
	echo '<script src="'.$scriptsFolder.'/'.'jquery-2.1.1.min.js"></script>';
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

function predictionsTable($datas, $is_other_user=false, $leaderboards=null){
	for($index = 0; $index < count($datas); ++$index){
		$data = $datas[$index];
		$leaderboard = null;
		if($leaderboards){
			$leaderboard = $leaderboards[$index];
		}
		echo '<div id="groupDiv">';
		predictionTable($data, $is_other_user);
		leaderboardTable($leaderboard);
		echo '</div>';
	}
}

function predictionTable($data, $is_other_user){
	global $s_default_color;
	global $s_correct_result_color;
	global $s_correct_result_winner_color;
	global $s_incorrect_result_color;

	$group = $data["group"];
	$games = $data["matches"];
	$predictions = $data["predictions"];
	$games_count = count($games);
	$points = 0;
	echo '<div id="gamesDiv" float="left">';
	echo '<table class="predictionsTable" border=1>';
	echo '<tr>';
	echo '<th colspan="6">'.$group->getName().'</th>';
	echo '</tr>';
	echo '<tr>';
	echo '<th>Host</th>';
	echo '<th>Guest</th>';
	echo '<th>Host goals</th>';
	echo '<th>Guest goals</th>';
	echo '<th>Date/Time</th>';
	if(!$is_other_user){
		echo '<th>Action</th>';
	}
	echo '</tr>';
	for($index = 0; $index < $games_count; ++$index){
		$game = $games[$index];
		$prediction = $predictions[$index];
		$dt = new DateTime();
		$dt_match = DateTime::createFromFormat('Y-m-d H:i:s', $game->getDateAndTime());
		$did_game_start = $dt >= $dt_match;
		echo '<tr id="'.$game->getId().'">';
		echo '<th>'.$game->getHostTeamName().'</th>';
		echo '<th>'.$game->getGuestTeamName().'</th>';
		$host_cell_value = '';
		$guest_cell_value = '';
		$cell_color = $s_default_color;
		if($did_game_start || $is_other_user){
			if($prediction){
				$host_cell_value = $prediction->getHostScore();
				$guest_cell_value = $prediction->getGuestScore();
				if(isScoreCorrect($game->getHostScore(), $game->getGuestScore(), $prediction->getHostScore(), $prediction->getGuestScore())){
					$cell_color = $s_correct_result_color;
					$points += 3;
				}else if(isScoreWinnerCorrect($game->getHostScore(), $game->getGuestScore(), $prediction->getHostScore(), $prediction->getGuestScore())){
					$cell_color = $s_correct_result_winner_color;
					$points += 1;
				}else{
					$cell_color = $s_incorrect_result_color;
				}
			}else{
				$host_cell_value = "null";
				$guest_cell_value = "null";
				if($did_game_start){
					$cell_color = $s_incorrect_result_color;
				}
			}
		}else{
			$host_prediction = '';
			$guest_prediction = '';
			if($prediction){
				$host_prediction = $prediction->getHostScore();
				$guest_prediction = $prediction->getGuestScore();
			}
			$host_cell_value = '<input type="text" class="scoreInput" name="hostScore" value="'.$host_prediction.'">';
			$guest_cell_value = '<input type="text" class="scoreInput" name="hostScore" value="'.$guest_prediction.'">';
		}
		echo '<th id="hostScore" bgcolor="'.$cell_color.'">';
		echo $host_cell_value;
		echo '</th>';
		echo '<th id="guestScore" bgcolor="'.$cell_color.'">';
		echo $guest_cell_value;
		echo '</th>';
		echo '<th>'.$game->getDateAndTime().'</th>';
		if($did_game_start == false && $is_other_user == false){
			echo '<th><button type="button" onclick="updatePrediction('.$game->getId().')">Update</button></th>';
		}
		echo '</tr>';
	}
	echo '</table>';
	//echo $points.' points!<br><br>';
	echo '</div>';
}

function leaderboardTable($leaderboard){
	if($leaderboard){
		echo '<div id="leaderboardDiv" float="right">';
		$leaderboardGroup = $leaderboard["group"];
		$leaderboardData = $leaderboard["data"];
		echo '<table class="leaderboardTable" border=1>';
		echo '<tr>';
		echo '<th colspan="2">';
		echo $leaderboardGroup->getName();
		echo '</th>';
		echo '</tr>';
		echo '<tr>';
		echo '<th>';
		echo 'Name';
		echo '</th>';
		echo '<th>';
		echo 'Points';
		echo '</th>';
		foreach ($leaderboardData as $user){
			echo '<tr>';
			echo '<th>';
			echo '<a href="viewUserBets.php?id='.$user->getId().'">'.$user->getName().'</a>';
			echo '</th>';
			echo '<th>';
			echo $user->getLdbPoints();
			echo '</th>';
			echo '</th>';
		}
		echo '</tr>';
		echo '</table>';
		echo '</div>';
	}
}

?>