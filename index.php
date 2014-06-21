<?php 

require_once 'basePage.php';
require_once 'utils/functions.php';

static $s_default_color = '#FFFFFF ';
static $s_correct_result_color = '00FF00';
static $s_correct_result_winner_color = 'FFFF00';
static $s_incorrect_result_color = 'FF0000';

function predictionsTable($datas, $is_other_user=false){
	global $s_default_color;
	global $s_correct_result_color;
	global $s_correct_result_winner_color;
	global $s_incorrect_result_color;
	foreach ($datas as $data){
		$group = $data["group"];
		$games = $data["matches"];
		$predictions = $data["predictions"];
		$games_count = count($games);
		$points = 0;
		echo $group->getName();
		echo '<table class="predictionsTable" border=1>';
		echo '<tr>';
		echo '<th>Host</th>';
		echo '<th>Guest</th>';
		echo '<th>Host goals</th>';
		echo '<th>Guest goals</th>';
		echo '<th>Date/Time</th>';
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
					$host_cell_value = "undefined";
					$guest_cell_value = "undefined";
					$cell_color = $s_incorrect_result_color;
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
		echo $points.' points!<br><br>';
	}
}

pageHeader();
addScripts(["functions"]);
controlPanel();

$current_user = SessionManager::getInstance()->getCurrentUser();
if($current_user){
	$groups = getSortedMatchGroups();
	$datas = array();
	foreach ($groups as $group){
		$data = array();
		$data["group"] = $group;
		$matches = DatabaseManager::getInstance()->getMatchesByGroupId($group->getId());
		$data["matches"] = $matches;
		$predictions = array();
		foreach($matches as $match){
			$prediction = DatabaseManager::getInstance()->getPredictionForGameOfUser($match->getId(), $current_user->getId());
			$predictions[] = $prediction;
		}
		$data["predictions"] = $predictions;
		$datas[] = $data;
	}
	predictionsTable($datas);
}

pageFooter();

?>