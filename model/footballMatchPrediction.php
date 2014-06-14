<?php

class FootballMatchPrediction{
	
	private $id;
	private $updated_time;
	private $creator_id;
	private $match_id;
	private $host_score;
	private $guest_score;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->creator_id = -1;
		$this->match_id = -1;
		$this->host_score = null;
		$this->guest_score = null;
	}
	
	public function  __destruct(){
	
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->creator_id = $db_entry["CreatorId"];
		$this->match_id = $db_entry["MatchId"];
		$this->host_score = $db_entry["HostScore"];
		$this->guest_score = $db_entry["GuestScore"];
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getUpdatedTime(){
		$dt = new DateTime($this->updated_time);  // convert UNIX timestamp to PHP DateTime
		return $dt->format('H:i:s Y-m-d');
	}
	
	public function getCreatorId(){
		return $this->creator_id;
	}
	
	public function getMatchId(){
		return $this->match_id;
	}
	
	public function getHostScore(){
		return $this->host_score;
	}
	
	public function getGuestScore(){
		return $this->guest_score;
	}
}

?>