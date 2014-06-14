<?php

require_once 'medoo.php';
require_once 'model/User.php';

class DatabaseManager{
	
	private  $database;
	private static $_singleton_;
	
	private function __construct(){
			$this->database = new medoo();
	}
	
	public function __destruct(){
		
	}
	
	public function getUserById($user_id){
		$datas = $database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime"
				
				], [
				"ID" => $user_id
				]);
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			if($user->getId() == $user_id){
				return $user;
			}
		}
		
		return null;
	}
	
	public function getUserByHashId($hash_id){
		$datas = $database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime"
		
				], [
				"UserHash" => $hash_id
				]);
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			if($user->getUserHash() == $hash_id){
				return $user;
			}
		}
		
		return null;
	}
	
	public function getUserByEmail($email){
		$datas = $database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime"
		
				], [
				"Email" => $email
				]);
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			if($user->getUserHash() == $hash_id){
				return $user;
			}
		}
		
		return null;
	}
	
	public function setUserHasForUser($user_hash, $user_id){
		$database->update("Users", [
				"UserHash" => $user_hash
				],
				["ID" => $user_id]);
		return true;
	}
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new DatabaseManager();
		}
		
		return self::$_singleton_;
	}
}

?>