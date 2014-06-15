<?php

require_once 'databaseManager.php';
require_once 'utils/httpStatus.php';


session_start();

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
	
	private function isGameloftEmail($email){
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
				$user_id = DatabaseManager::getInstance()->addNewUser($email, $password_hash, $name, $userHash, User::$s_rank_noraml_user);
				/*$user = DatabaseManager::getInstance()->getUserByEmail($email);
				if($user){
					$this->current_user = $user;
					$this->saveCurrentUserInCookies();
					return HttpStatus::$HTTP_STATUS_OK;
				}
				else{
					return HttpStatus::$HTTP_STATUS_INTERNAL_SERVER_ERROR;
				}*/
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
				$this->current_user = $user;
				$this->saveCurrentUserInCookies();
				return HttpStatus::$HTTP_STATUS_OK;
			}
			else{
				return HttpStatus::$HTTP_STATUS_UNAUTHORIZED;
			}
		}else{
			return HttpStatus::$HTTP_STATUS_NOT_FOUND;
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
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new SessionManager();
		}
		
		return self::$_singleton_;
	} 
}
?>