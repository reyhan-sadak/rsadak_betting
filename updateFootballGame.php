<?php

require_once 'managers/sessionManager.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["game_id"]) && isset($_POST["host_score"]) && isset($_POST["guest_score"])){
		$match_id = $_POST["game_id"];
		$host_score = $_POST["host_score"];
		$guest_score = $_POST["guest_score"];
		
		$result = SessionManager::getInstance()->updateGame($match_id, $host_score, $guest_score);
		if($result){
			echo json_encode($result->serializeToArray());
		}else{
			http_response_code(HttpStatus::$HTTP_STATUS_INTERNAL_SERVER_ERROR);
		}
	}else{
		http_response_code(HttpStatus::$HTTP_STATUS_BAD_REQUEST);
	}
}else {
	http_response_code(HttpStatus::$HTTP_STATUS_METHOD_NOT_ALLOWED);
}

?>