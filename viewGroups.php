<?php

require_once 'basePage.php';
require_once 'utils/functions.php';

function leaguesTable($leagues){
	echo '<table id="leaguesTable" border=1>';
	echo '<tr>';
	echo '<th>Name</th>';
	echo '<th>Creator</th>';
	echo '<th>Created</th>';
	echo '<th>Last Updated</th>';
	echo '</tr>';
	foreach ($leagues as $league){
		echo '<tr>';
		echo '<th>'.$league->getName().'</th>';
		echo '<th>'.$league->getCreatorName().'</th>';
		echo '<th>'.$league->getCreatedTime().'</th>';
		echo '<th>'.$league->getUpdatedTime().'</th>';
		echo '</tr>';
	}
	echo '</table>';
}

pageHeader();
controlPanel();

$leagues = DatabaseManager::getInstance()->getAllLeagues();
leaguesTable($leagues);

pageFooter();

?>