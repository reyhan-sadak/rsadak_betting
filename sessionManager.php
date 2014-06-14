<?php

require_once 'databaseManager.php';
require_once 'httpStatus.php';

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
		if(isset($_COOKIE["userHash"])){
			$user_hash = $_COOKIE["userHash"];
			$this->current_user = DatabaseManager::getInstance()->getUserByHashId($user_hash);
		}
	}
	
	private function saveCurrentUserInCookies(){
		if($this->current_user){
			$new_hash = generateUserHash();
			if($new_hash){
				if(DatabaseManager::getInstance()->setUserHasForUser($this->currentUser->getId(), $new_hash)){
					setcookie("userHash", $new_hash, self::$s_cookie_time);
				}
			}
		}
	}
	
	private static function hashPassword($rawPassword){
		return crypt($rawPassword);
	}
	
	private function generateUserHash(){
		if($this->current_user){
			$str_to_hash = (string)$this->current_user->getId().$this->current_user->getName().(string)time();
			return hash("md5", $str_to_hash);
		}else{
			return null;
		}
	}
	
	private function isNameValid($name){
		return true; // todo
	}
	
	private function doPasswordsmatch($password, $repeatPassword){
		return $password === $repeatPassword;
	}
	
	private function isEmailALreadyUsed($email){
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
	
	public function getCurrentUser(){
		return $this->current_user;
	}
	
	public function register($email, $pass, $repeat_pass, $name){
		if(!$this->current_user){
			if($this->isEmailValid($email)){
				
			}
		}else{
			return HttpStatus::$HTTP_STATUS_BAD_REQUEST;
		}
	}
	
	public function login($email, $pass){
		$user = DatabaseManager::getInstance()->getUserByEmail($email);
		if($user){
			$hashed_pass = $this->hashPassword($pass);
			if($user->passwordHashMatch($hashed_pass)){
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
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new SessionManager();
		}
		
		return self::$_singleton_;
	} 
}
?>