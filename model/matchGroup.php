<?php

class MatchGroup{
	
	private $id;
	private $updated_time;
	private $created_time;
	private $name;
	private $creator_id;
	private $from_date;
	private $to_date;
	private $visible;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->created_time = 0;
		$this->name = "";
		$this->creator_id = -1;
		$this->from_date = 0;
		$this->to_date = 0;
		$this->visible = 1;
	}
	
	public function  __destruct(){
	
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->created_time = $db_entry["CreatedTime"];
		$this->name = $db_entry["Name"];
		$this->creator_id = $db_entry["CreatorId"];
		$this->from_date = $db_entry["FromDate"];
		$this->to_date = $db_entry["ToDate"];
		$this->visible = $db_entry["Visible"];
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
	
	public function getCreatorName(){
		$creator = DatabaseManager::getInstance()->getUserById($this->getCreatorId());
		if($creator){
			return $creator->getName();
		}else{
			return "Not found";
		}
	}
	
	public function getFromDate(){
		return $this->from_date;
	}
	
	public function getToDate(){
		return $this->to_date;
	}
	
	public function isVisible(){
		return $this->visible;
	}
}

?>