<?php 

require_once 'sessionManager.php';

function printHello($current_user){
	echo 'Hello, '.$current_user->getName().'!<br>';
	echo '<a href="logout.php"> Logout </a><br>';
	if($current_user->isModerator()){
		echo '<a href="addLeague.php"> Add League </a><br>';
		echo '<a href="addTeam.php"> Add Team </a><br>';
		echo '<a href="addGameGroup.php"> Add Game Group </a><br>';
		echo '<a href="addFootballGame.php"> Add Football Game </a><br>';
	}
	if($current_user->isAdmin()){
		echo '<a href="viewUsers.php"> View Users </a><br>';
	}
}

echo
'<html>
<header>

</header>
';

$current_user = SessionManager::getInstance()->getCurrentUser();
if($current_user != null){
	printHello($current_user);
}else{
	echo
	'<a href="login.php">Login</a> or <a href="register.php">Register</a>
	';
}

echo
'</html>
';

?>