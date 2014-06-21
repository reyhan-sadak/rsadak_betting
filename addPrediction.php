<?php

require_once 'managers/sessionManager.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
	if(isset($_POST["game_id"]) && isset($_POST["host_score"]) && isset($_POST["guest_score"])){
		$match_id = $_POST["game_id"];
		$host_score = $_POST["host_score"];
		$guests_score = $_POST["guest_score"];
		$game = DatabaseManager::getInstance()->getGameById($match_id);
		if($game){
			$dt = new DateTime();
			$dt_match = DateTime::createFromFormat('Y-m-d H:i:s', $game->getDateAndTime());
			if($dt < $dt_match){
				$result = SessionManager::getInstance()->createGamePrediction($match_id, $host_score, $guests_score);
				http_response_code($result);
			}else{
				// the match has already started
				http_response_code(HttpStatus::$HTTP_STATUS_CONFLICT);
			}
		}else{
			// the match was not found
			http_response_code(HttpStatus::$HTTP_STATUS_NOT_FOUND);
		}
	}else{
		// missing parameters
		print_r($_POST);
		//http_response_code(HttpStatus::$HTTP_STATUS_BAD_REQUEST);
	}
}else{
	// method not allowed
	http_response_code(HttpStatus::$HTTP_STATUS_METHOD_NOT_ALLOWED);
}

?>