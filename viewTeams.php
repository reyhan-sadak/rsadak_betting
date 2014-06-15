<?php

require_once 'basePage.php';
require_once 'utils/functions.php';

function teamsTable($teams){
	echo '<table id="leaguesTable" border=1>';
	echo '<tr>';
	echo '<th>Name</th>';
	echo '<th>League</th>';
	echo '<th>Creator</th>';
	echo '<th>Created</th>';
	echo '<th>Last Updated</th>';
	echo '</tr>';
	foreach ($teams as $team){
		echo '<tr>';
		echo '<th>'.$team->getName().'</th>';
		echo '<th>'.$team->getCreatorName().'</th>';
		echo '<th>'.$team->getLeagueName().'</th>';
		echo '<th>'.$team->getCreatedTime().'</th>';
		echo '<th>'.$team->getUpdatedTime().'</th>';
		echo '</tr>';
	}
	echo '</table>';
}

pageHeader();
controlPanel();

$teams = DatabaseManager::getInstance()->getAllTeams();
teamsTable($teams);

pageFooter();

?>