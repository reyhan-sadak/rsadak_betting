<?php

require_once 'databaseManager.php';
require_once 'utils/httpStatus.php';


session_start();

function compareUserScores($first, $second){
	if($first->getLdbPoints() == $second->getLdbPoints()){
		if($first->getName() > $second->getName()){
			return 1;
		}
		return -1;
	}else{
		return $first->getLdbPoints() - $second->getLdbPoints();
	}
}

class SessionManager{
	
	private static $_singleton_;
	
	private static $s_cookie_time = 2590000; // 30 days
	private $current_user;
	
	private function __construct(){
		$this->current_user = null;
		
		$this->loadUserFromSessionAndCookies();
	}
	
	public function __destruct(){
		
	}
	
	private function loadUserFromSessionAndCookies(){
		/*if(isset($_COOKIE["userHash"])){
			$user_hash = $_COOKIE["userHash"];
			$this->current_user = DatabaseManager::getInstance()->getUserByHashId($user_hash);
		}else */
		if(isset($_SESSION["userId"])){
			$this->current_user = DatabaseManager::getInstance()->getUserById($_SESSION["userId"]);
		}
	}
	
	private function saveCurrentUserInCookies(){
		if($this->current_user){
			$new_hash = $this->generateUserHash();
			echo $new_hash;
			if($new_hash){
				if(DatabaseManager::getInstance()->setUserHashForUser($this->current_user->getId(), $new_hash)){
					echo "set cookie and session";
					setcookie("userHash", $new_hash, self::$s_cookie_time);
					$_SESSION["userId"] = $this->current_user->getId();
					echo '<br> _SESSION["userId"] = '. $_SESSION["userId"].'<br>';
				}
			}
		}
	}
	
	private function hashPassword($rawPassword){
		return crypt($rawPassword);
	}
	
	private function generateUserHash($email=null, $name=null){
		if($this->current_user){
			if($email==null){
				$email = $this->current_user->getEmail();	
			}
			if($name==null){
				$name = $this->current_user->getName();
			}
		}
		if($email != null && $name != null){
			$str_to_hash = $email.$name.(string)time();
			return hash("md5", $str_to_hash);
		} else{
			return null;
		}
	}
	
	public static function isNameValid($name){
		return true; // todo
	}
	
	public function doPasswordsmatch($password, $repeatPassword){
		return $password === $repeatPassword;
	}
	
	public function isEmailALreadyUsed($email){
		$user = DatabaseManager::getInstance()->getUserByEmail($email);
		if($user){
			return true;
		}else{
			return false;
		}
	}
	
	public static function isEmailValid($email){
		return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email);
	}
	
	public static function isGameloftEmail($email){
		return preg_match("/([\w\-]+\@gameloft.[\w\-]+)/",$email);
	}
	
	public function leagueWithNameExists($league_name){
		$league = DatabaseManager::getInstance()->getLeagueByName($league_name);
		if($league){
			return true;
		}else{
			return false;
		}
	}
	
	public function leagueWithIdExists($league_id){
		$league = DatabaseManager::getInstance()->getLeagueById($league_id);
		if($league){
			return true;
		}else{
			return false;
		}
	}
	
	public function teamWithNameForLeagueExists($team_name, $league_id){
		$teamsWithName = DatabaseManager::getInstance()->getTeamsByName($team_name);
		foreach ($teamsWithName as $team){
			if($team->getLeagueId() == $league_id){
				return true;
			}
		}
		return false;
	}
	
	public function gameGroupWithNameExists($game_group_name){
		$game_group = DatabaseManager::getInstance()->getGameGroupByName($game_group_name);
		if($game_group){
			return true;
		}else{
			return false;
		}
	}
	
	public function gameWithIdExist($game_id){
		$game = DatabaseManager::getInstance()->getGameById($game_id);
		if($game){
			return true;
		}else{
			return false;
		}
	}
	
	public function predictionForGameOfUserExists($game_id, $user_id){
		$prediction = DatabaseManager::getInstance()->getPredictionForGameOfUser($game_id, $user_id);
		if($prediction){
			return true;
		}else{
			return false;
		}
	}
	
	public function getCurrentUser(){
		if($this->current_user == null){
			$this->loadUserFromSessionAndCookies();
		}
		return $this->current_user;
	}
	
	public function register($email, $pass, $repeat_pass, $name){
		if(!$this->current_user){
			if($this->isEmailValid($email) && $this->doPasswordsmatch($pass, $repeat_pass)){
				$password_hash = $this->hashPassword($pass);
				$userHash = $this->generateUserHash($email, $name);
				$user_id = DatabaseManager::getInstance()->addNewUser($email, $password_hash, $name, $userHash, User::$s_rank_noraml_user, 1, 1);
				return $this->login($email, $pass);
			}
		}else{
			return HttpStatus::$HTTP_STATUS_BAD_REQUEST;
		}
	}
	
	public function login($email, $pass){
		$user = DatabaseManager::getInstance()->getUserByEmail($email);
		if($user){
			if($user->passwordHashMatch($pass)){
				if($user->isActive()){
					$this->current_user = $user;
					$this->saveCurrentUserInCookies();
					return HttpStatus::$HTTP_STATUS_OK;
				}
				else{
					return HttpStatus::$HTTP_STATUS_FORBIDDEN;
				}
			}
			else{
				return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
			}
		}else{
			return HttpStatus::$HTTP_STATUS_NOT_FOUND;
		}
	}
	
	public function changePassword($old_password, $new_password){
		if($old_password == $new_password){
			return HttpStatus::$HTTP_STATUS_CONFLICT;
		}
		$user = $this->current_user;
		if($user){
			if($user->passwordHashMatch($old_password)){
				$new_password_hash = $this->hashPassword($new_password);
				$result = DatabaseManager::getInstance()->updatePasswordForUser($user->getId(), $new_password_hash);
				if($result > 0){
					return HttpStatus::$HTTP_STATUS_OK;
				}else{
					return HttpStatus::$HTTP_STATUS_INTERNAL_SERVER_ERROR;
				}
			}else{
					return HttpStatus::$HTTP_STATUS_FORBIDDEN;
				}
		}else{
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function logout(){
		unset($_SESSION["userId"]);
		unset($this->current_user);
	}
	
	public function addNewLeague($league_name){
		$current_user = $this->getCurrentUser();
		if($current_user){
			DatabaseManager::getInstance()->addNewLeague($league_name, $current_user->getId());
			return HttpStatus::$HTTP_STATUS_OK;
		}else{
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function addNewTeam($team_name, $league_id){
		$current_user = $this->getCurrentUser();
		if($current_user){
			DatabaseManager::getInstance()->addNewTeamForLeague($team_name, $league_id, $current_user->getId());
			return HttpStatus::$HTTP_STATUS_OK;
		}else{
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function addNewGameGroup($name, $from_date, $to_date){
		$current_user = $this->getCurrentUser();
		if($current_user){
			DatabaseManager::getInstance()->addNewGameGroup($name, $from_date, $to_date, $current_user->getId());
			return HttpStatus::$HTTP_STATUS_OK;
		}else{
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function addNewGame($group_id, $host_team_id, $guest_team_id, $date_time){
		$group = DatabaseManager::getInstance()->getGameGroupById($group_id);
		$host_team = DatabaseManager::getInstance()->getTeamById($host_team_id);
		$guest_team = DatabaseManager::getInstance()->getTeamById($guest_team_id);
		if($group == null || $host_team == null || $guest_team == null){
			return HttpStatus::$HTTP_STATUS_NOT_FOUND;
		}
		if($host_team_id == $guest_team_id){
			return HttpStatus::$HTTP_STATUS_CONFLICT;
		}
		$current_user = $this->getCurrentUser();
		if($current_user){
			if($current_user->isModerator()){
				DatabaseManager::getInstance()->addNewGame($group_id, $host_team_id, $guest_team_id, $current_user->getId(), $date_time);
				return HttpStatus::$HTTP_STATUS_OK;
			}else {
				return HttpStatus::$HTTP_STATUS_FORBIDDEN;
			}
		}else {
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function updateGame($game_id, $host_score, $guest_score){
		if($this->gameWithIdExist($game_id)){
			$result = DatabaseManager::getInstance()->updateGame($game_id, $host_score, $guest_score);
			if($result >= 0){
				$game = DatabaseManager::getInstance()->getGameById($game_id);
				return $game;
			}else {
				return null;
			}
		}
		return null;
	}
	
	public function createGamePrediction($game_id, $host_score, $guests_score){
		$current_user = $this->getCurrentUser();
		if($current_user){
			if($this->predictionForGameOfUserExists($game_id, $current_user->getId())){
				DatabaseManager::getInstance()->updateGamePrediction($game_id, $current_user->getId(), $host_score, $guests_score);
			}else{
				DatabaseManager::getInstance()->createGamePrediction($game_id, $current_user->getId(), $host_score, $guests_score);
			}
			return HttpStatus::$HTTP_STATUS_OK;
		}else{
			return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
		}
	}
	
	public function getUserPointsForMachGroup($user_id, $match_group_id){
		$match_group = DatabaseManager::getInstance()->getMatchesByGroupId($match_group_id);
		$user = DatabaseManager::getInstance()->getUserById($user_id);
		$score = 0;
		if($match_group && $user){
			$matches = DatabaseManager::getInstance()->getMatchesByGroupId($match_group_id);
			foreach ($matches as $match){
				$host_score = $match->getHostScore();
				$guest_score = $match->getGuestScore();
				if($host_score && $guest_score){
					$prediction = DatabaseManager::getInstance()->getPredictionForGameOfUser($match->getId(), $user_id);
					if($prediction){
						$host_prediction = $prediction->getHostScore();
						$guest_prediction = $prediction->getGuestPrediction();
						if(isScoreCorrect($host_score, $guest_score, $host_prediction, $guest_prediction)){
							$score += 3;
						}else if(isScoreWinnerCorrect($host_score, $guest_score, $host_prediction, $guest_prediction)){
							$score += 1;
						}
					}
				}
			}
		}
		return $score;
	}
	
	public function getUsersLeaderboardForMatchGroup($match_group_id){
		$users = DatabaseManager::getInstance()->getAllUsers(true, true);
		foreach ($users as $user){
			$user->setLdbPoints($this->getUserPointsForMachGroup($user->getId(), $match_group_id));
		}
		usort($users, 'compareUserScores');
		return $users;
	}
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new SessionManager();
		}
		
		return self::$_singleton_;
	} 
}
?>