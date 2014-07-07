<?php 

require_once 'basePage.php';

pageHeader('setLocalTime');
addScripts(["functions"]);
controlPanel();

$current_user = SessionManager::getInstance()->getCurrentUser();
if($current_user){
	$groups = getSortedMatchGroups();
	$datas = array();
	$leaderboards = array();
	foreach ($groups as $group){
		$data = array();
		$data["group"] = $group;
		$matches = DatabaseManager::getInstance()->getSortedMatchesByGroupId($group->getId());
		$data["matches"] = $matches;
		$predictions = array();
		foreach($matches as $match){
			$prediction = DatabaseManager::getInstance()->getPredictionForGameOfUser($match->getId(), $current_user->getId());
			$predictions[] = $prediction;
		}
		$data["predictions"] = $predictions;
		$datas[] = $data;
		
		$leaderboard = array();
		$leaderboard["group"] = $group;
		$leaderboard["data"] = SessionManager::getInstance()->getUsersLeaderboardForMatchGroup($group->getId());
		$leaderboards[] = $leaderboard;
	}
		
		predictionsTable($datas, false, $leaderboards, $current_user->getId());
}

pageFooter();

?>