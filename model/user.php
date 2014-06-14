<?php

class User{

	private $id;
	private $updated_time;
	private $email;
	private $name;
	private $rank;
	private $pass_hash;
	private $user_hash;
	
	public static $s_rank_noraml_user = 0;
	public static $s_rank_moderator = 10;
	public static $s_rank_admin = 100;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->email = "";
		$this->name = "";
		$this->rank = self::$s_rank_noraml_user;
		$this->pass_hash = "";
		$this->user_hash = "";
	}
	
	public function __destruct(){
		
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->email = $db_entry["Email"];
		$this->name = $db_entry["Name"];
		$this->rank = $db_entry["UserRank"];
		$this->pass_hash = $db_entry["PasswordHash"];
		$this->user_hash = $db_entry["UserHash"];
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getUpdatedTime(){
		$dt = new DateTime($this->updated_time);  // convert UNIX timestamp to PHP DateTime
		return $dt->format('H:i:s Y-m-d');
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getUserRank(){
		return $this->rank;
	}
	
	public function isModerator(){
		return $this->getUserRank() >= self::$s_rank_moderator;
	}
	
	public function isAdmin(){
		return $this->getUserRank() >= self::$s_rank_admin;
	}
	
	public function getUserHash(){
		return $this->user_hash;
	}
	
	public function passwordHashMatch($password_hash){
		return strcmp($this->pass_hash, $password_hash) == 0;
	}
}

?>