<?php 

require_once 'initSession.php';
require_once 'sessionManager.php';

$current_user = SessionManager::getInstance()->getCurrentUser(); 
if($current_user){
	echo "Hello, ".$current_user->getName();
}

?>