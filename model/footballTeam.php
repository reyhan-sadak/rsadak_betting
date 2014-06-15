<?php

require_once 'managers/databaseManager.php';

class FootballTeam{
	
	private $id;
	private $updated_time;
	private $created_time;
	private $name;
	private $creator_id;
	private $league_id;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->created_time = 0;
		$this->name = "";
		$this->creator_id = -1;
		$this->league_id = -1;
	}
	
	public function  __destruct(){
	
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->created_time = $db_entry["CreatedTime"];
		$this->name = $db_entry["Name"];
		$this->creator_id = $db_entry["CreatorId"];
		$this->league_id = $db_entry["LeagueId"];
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getCreatedTime(){
		$dt = new DateTime($this->created_time);  // convert UNIX timestamp to PHP DateTime
		return $dt->format('H:i:s Y-m-d');
	}
	
	public function getUpdatedTime(){
		$dt = new DateTime($this->updated_time);  // convert UNIX timestamp to PHP DateTime
		return $dt->format('H:i:s Y-m-d');
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getCreatorId(){
		return $this->creator_id;
	}
	
	public function getLeagueId(){
		return $this->league_id;
	}
	
	public function getCreatorName(){
		$creator = DatabaseManager::getInstance()->getUserById($this->getCreatorId());
		if($creator){
			return $creator->getName();
		}else{
			return "Not found";
		}
	}
	
	public function getLeagueName(){
		$league = DatabaseManager::getInstance()->getLEagueById($this->getLeagueId());
		if($league){
			return $league->getName();
		}else{
			return "Unknown";
		}
	}
}
?>