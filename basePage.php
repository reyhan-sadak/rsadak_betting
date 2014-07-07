<?php

require_once 'utils/functions.php';

static $scriptsFolder = "js";
static $s_default_color = '#FFFFFF ';
static $s_correct_result_color = '#00FF00';
static $s_correct_result_winner_color = '#FFFF00';
static $s_incorrect_result_color = '#FF0000';
static $s_pending_result_color = '#C0C0C0';

date_default_timezone_set("UTC");

static $noUserControlPanel = array(
			array("index.php", "Home"),
			array("login.php", "Login"),
			array("register.php", "Register")
		);

static $normalUserControlPanel = array(
			array("logout.php", "Logout"),
			array("changePassword.php", "Change Password"),
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

function pageHeader($on_load=null){
	echo
	'<html>
	<header>
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	<link rel="stylesheet" type="text/css" href="resources/styles.css">
	</header>
	<body ';
	if($on_load != null){
		echo 'onload="'.$on_load.'()"';
	}
	echo '>';
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
		if($scriptname == 'functions'){
			$current_user = SessionManager::getInstance()->getCurrentUser();
			if($current_user){
				if($current_user->isAdmin()){
					echo '<script src="'.$scriptsFolder.'/'.$scriptname.'_admin.js"></script>';
					echo '<script src="'.$scriptsFolder.'/'.$scriptname.'_moderator.js"></script>';
				}else if($current_user->isModerator()){
					echo '<script src="'.$scriptsFolder.'/'.$scriptname.'_moderator.js"></script>';
				}
			}
		}
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
	usort($started_groups, 'compareMatchGroups');
	usort($upcoming_groups, 'compareMatchGroups');
	usort($ended_groups, 'compareMatchGroups');
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

function predictionsTable($datas, $is_other_user=false, $leaderboards=null, $user_id=null){
	for($index = 0; $index < count($datas); ++$index){
		$data = $datas[$index];
		$leaderboard = null;
		if($leaderboards){
			$leaderboard = $leaderboards[$index];
		}
		echo '<table id="listTable">';
		echo '<tr>';
		echo '<th>';
		predictionTable($data, $is_other_user);
		echo '</th>';
		echo '<th>';
		leaderboardTable($leaderboard, $user_id);
		echo '</th>';
		echo '</tr>';
		echo '</table>';
	}
}

function predictionTable($data, $is_other_user){
	global $s_default_color;
	global $s_correct_result_color;
	global $s_correct_result_winner_color;
	global $s_incorrect_result_color;
	global $s_pending_result_color;

	$dt = new DateTime();
	$group = $data["group"];
	$games = $data["matches"];
	$predictions = $data["predictions"];
	$games_count = count($games);
	echo '<div id="gamesDiv">';
	echo '<table class="predictionsTable">';
	echo '<tr>';
	echo '<th colspan="5">'.$group->getName().'</th>';
	echo '</tr>';
	echo '<tr>';
	echo '<th class="with_border">Host</th>';
	echo '<th class="with_border">Guest</th>';
	echo '<th class="with_border" id="score_cell">Host prediction</th>';
	echo '<th class="with_border" id="score_cell">Guest prediction</th>';
	echo '<th class="with_border" id="score_cell">Host score</th>';
	echo '<th class="with_border" id="score_cell">Guest score</th>';
	echo '<th class="with_border" id="datetime_header">Date/Time(your timezone)</th>';
	if(!$is_other_user){
		echo '<th>&nbsp</th>';
	}
	echo '</tr>';
	for($index = 0; $index < $games_count; ++$index){
		$game = $games[$index];
		$prediction = $predictions[$index];
		$dt_match = DateTime::createFromFormat('Y-m-d H:i:s', $game->getDateAndTime());
		$dt_match_formatted = $game->getDateAndTime();
		$did_game_start = $dt >= $dt_match;
		$is_game_result_pending = $did_game_start && $game->getHostScore() == null && $game->getGuestScore() == null;
		echo '<tr id="'.$game->getId().'">';
		echo '<th class="with_border" id="team_name">'.$game->getHostTeamName().'</th>';
		echo '<th class="with_border" id="team_name">'.$game->getGuestTeamName().'</th>';
		$host_cell_value = '';
		$guest_cell_value = '';
		$host_score_value = '&nbsp';
		$guest_score_value = '&nbsp';
		$cell_color = $s_default_color;
		if($did_game_start || $is_other_user){
			if($prediction){
				if($is_other_user && $did_game_start == false){
					$host_cell_value = '?';
					$guest_cell_value = '?';
				}else{
					$host_cell_value = $prediction->getHostScore();
					$guest_cell_value = $prediction->getGuestScore();
				}
				if($is_game_result_pending){
					$cell_color = $s_pending_result_color;
				}else if(isScoreCorrect($game->getHostScore(), $game->getGuestScore(), $prediction->getHostScore(), $prediction->getGuestScore())){
					$cell_color = $s_correct_result_color;
				}else if(isScoreWinnerCorrect($game->getHostScore(), $game->getGuestScore(), $prediction->getHostScore(), $prediction->getGuestScore())){
					$cell_color = $s_correct_result_winner_color;
				}else if($is_other_user && !$did_game_start){
					$cell_color = $s_default_color;
				}else{
					$cell_color = $s_incorrect_result_color;
				}
			}else{
				if($is_other_user && $did_game_start == false){
					$host_cell_value = '?';
					$guest_cell_value = '?';
				}else{
					$host_cell_value = "&nbsp";
					$guest_cell_value = "&nbsp";
				}
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
			$host_cell_value = '<input type="text" class="scoreInput" name="hostScore" value="'.$host_prediction.'" size="3" text-align="center">';
			$guest_cell_value = '<input type="text" class="scoreInput" name="hostScore" value="'.$guest_prediction.'" size="3" text-align="center">';
		}
		if($game->getHostScore() != null){
			$host_score_value = $game->getHostScore();
		}
		if($game->getGuestScore() != null){
			$guest_score_value = $game->getGuestScore();
		}
		echo '<th class="with_border" id="hostScore" bgcolor="'.$cell_color.'">';
		echo $host_cell_value;
		echo '</th>';
		echo '<th class="with_border" id="guestScore" bgcolor="'.$cell_color.'">';
		echo $guest_cell_value;
		echo '</th>';
		
		echo '<th class="with_border" id="score_cell">';
		echo $host_score_value;
		echo '</th>';
		echo '<th class="with_border" id="score_cell">';
		echo $guest_score_value;
		echo '</th>';
		
		echo '<th class="datetime">'.$game->getTimeEpoch().'</th>';
		if($did_game_start == false && $is_other_user == false){
			echo '<th><button type="button" onclick="updatePrediction('.$game->getId().')">Update</button></th>';
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}

function leaderboardTable($leaderboard, $user_id=null){
	if($leaderboard){
		echo '<div id="leaderboardDiv" float="right">';
		$leaderboardGroup = $leaderboard["group"];
		$leaderboardData = $leaderboard["data"];
		echo '<table class="leaderboardTable">';
		echo '<tr>';
		echo '<th colspan="2">';
		//echo $leaderboardGroup->getName();
		echo 'Ranking';
		echo '</th>';
		echo '</tr>';
		echo '<tr>';
		echo '<th class="ldb_name">';
		echo 'Name';
		echo '</th>';
		echo '<th class="ldb_points">';
		echo 'Points';
		echo '</th>';
		foreach ($leaderboardData as $user){
			echo '<tr>';
			echo '<th class="ldb_name">';
			if($user_id != null && $user_id != $user->getId()){
				echo '<a href="viewUserBets.php?id='.$user->getId().'">'; 
			}
			echo $user->getName();
			if($user_id != null && $user_id != $user->getId()){
				echo '</a>';
			}
			echo '</th>';
			echo '<th class="ldb_points">';
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