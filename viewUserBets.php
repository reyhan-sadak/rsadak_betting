<?php 

require_once 'basePage.php';

pageHeader();
addScripts(["functions"]);
controlPanel();

if($_SERVER["REQUEST_METHOD"] == "GET"){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	if(isset($_GET["id"]) && $current_user){
		$user = DatabaseManager::getInstance()->getUserById((int)$_GET["id"]);
		if($user){
			if($user->isPredictor()){
				$groups = getSortedMatchGroups();
				$datas = array();
				foreach ($groups as $group){
					$data = array();
					$data["group"] = $group;
					$matches = DatabaseManager::getInstance()->getMatchesByGroupId($group->getId());
					$data["matches"] = $matches;
					$predictions = array();
					foreach($matches as $match){
						$prediction = DatabaseManager::getInstance()->getPredictionForGameOfUser($match->getId(), $user->getId());
						$predictions[] = $prediction;
					}
					$data["predictions"] = $predictions;
					$datas[] = $data;
				}
				predictionsTable($datas, ($user->getId() != $current_user->getId()));
			}
		}
	}
}

pageFooter();

?>