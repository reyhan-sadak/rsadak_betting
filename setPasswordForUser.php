<?php

require_once 'managers/sessionManager.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	if($current_user){
		if($current_user->isAdmin()){
			if(isset($_POST["user_id"]) && isset($_POST["new_pass"])){
				$user_id = $_POST["user_id"];
				$new_pass = $_POST["new_pass"];
				$new_pass_hash = SessionManager::getInstance()->hashPassword($new_pass);
				$result = DatabaseManager::getInstance()->updatePasswordForUser($user_id, $new_pass_hash);
			}else{
				http_response_code(HttpStatus::$HTTP_STATUS_BAD_REQUEST);
			}
		}else{
			http_response_code(HttpStatus::$HTTP_STATUS_FORBIDDEN);
		}
	}else{
		http_response_code(HttpStatus::$HTTP_STATUS_UNAUTHORIZED);
	}
}else{
	http_response_code(HttpStatus::$HTTP_STATUS_METHOD_NOT_ALLOWED);
}

?>