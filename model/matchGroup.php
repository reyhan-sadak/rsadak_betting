<?php

class MatchGroup{
	
	private $id;
	private $updated_time;
	private $name;
	private $creator_id;
	private $from_date;
	private $to_date;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->name = "";
		$this->creator_id = -1;
		$this->from_date = new Date();
		$this->to_date = new Date();
	}
	
	public function  __destruct(){
	
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->name = $db_entry["Name"];
		$this->creator_id = $db_entry["CreatorId"];
		$this->from_date = $db_entry["FromDate"];
		$this->to_date = $db_entry["ToDate"];
	}
	
	public function getId(){
		return $this->id;
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
	
	public function getFromDate(){
		return $this->from_date;
	}
	
	public function getToDate(){
		return $this->to_date;
	}
}

?>