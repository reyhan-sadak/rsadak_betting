<?php

require_once 'utils/functions.php';
require_once 'basePage.php';

static $users_per_page = 10;

pageHeader();
controlPanel();

addScripts(["functions"]);

function usersTable($users, $page_number, $page_entries){
	$users_count = count($users);
	echo '<table border=1>
		<tr>
		<th>Name</th>
		<th>Email</th>
		<th>Rank</th>
		<th>Created</th>
		<th>Last Updated</th>
		</tr>';
	for($index = 0; $index < $users_count; $index++){
		if($index >= ($page_number - 1)*$page_entries){
			echo
			'<tr>
			<th>'.$users[$index]->getName().'</th>
			<th>'.$users[$index]->getEmail().'</th>
			<th>'.$users[$index]->getUserRankName().'</th>
			<th>'.$users[$index]->getCreatedTime().'</th>
			<th>'.$users[$index]->getUpdatedTime().'</th>';
			if(!$users[$index]->isModerator()){
				echo '<th><button type="button" onclick="makeModerator('.(int)$users[$index]->getId().')">Make Moderator</button></th>';
			}
			if(!$users[$index]->isAdmin()){
				echo '<th><button type="button" onclick="makeAdmin('.(int)$users[$index]->getId().')">Make Admin</button></th>';
				echo '<th><button type="button" onclick="setPassword('.(int)$users[$index]->getId().')">Set Password</button></th>';
			}
			echo '</tr>';
		}
	}
	echo '</table>';
}
	
redirectIfNotAdmin();

$page_number = 1;
if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["page_number"])){
		$page_number = (int)$_POST["page_number"];
	}
}else if($_SERVER["REQUEST_METHOD"] == "GET"){
	if(isset($_GET["page_number"])){
		$page_number = (int)$$_GET["page_number"];
	}
}
$users = DatabaseManager::getInstance()->getAllUsers();

usersTable($users, $page_number, $users_per_page);

pageFooter();

?>