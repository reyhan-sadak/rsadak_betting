<?php

require_once 'basePage.php';
require_once 'utils/functions.php';

function groupsTable($groups){
	echo '<table id="groupesTable" border=1>';
	echo '<tr>';
	echo '<th>Name</th>';
	echo '<th>From date</th>';
	echo '<th>To Date</th>';
	echo '<th>Creator</th>';
	echo '<th>Created</th>';
	echo '<th>Last Updated</th>';
	echo '<th>Visible</th>';
	echo '</tr>';
	foreach ($groups as $group){
		echo '<tr>';
		echo '<th>'.$group->getName().'</th>';
		echo '<th>'.$group->getFromDate().'</th>';
		echo '<th>'.$group->getToDate().'</th>';
		echo '<th>'.$group->getCreatorName().'</th>';
		echo '<th>'.$group->getCreatedTime().'</th>';
		echo '<th>'.$group->getUpdatedTime().'</th>';
		if($group->isVisible()){
			echo '<th>Yes</th>';
		}else{
			echo '<th>No</th>';
		}
		echo '<th><button type="button" onclick="addFootballGame('.$group->getId().')">Add game</button></th>';
		echo '<th><button type="button" onclick="viewFootballGames('.$group->getId().')">View games</button></th>';
		echo '</tr>';
	}
	echo '</table>';
}

pageHeader();
addScripts(["functions"]);
controlPanel();

$groups = DatabaseManager::getInstance()->getAllGroups(null, true);
groupsTable($groups);

pageFooter();

?>