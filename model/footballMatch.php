<?php

class FootballMatch{
	
	private $id;
	private $updated_time;
	private $creator_id;
	private $host_team_id;
	private $guest_team_id;
	private $date_time;
	private $match_group_id;
	private $host_score;
	private $guest_score;
	
	public function __construct(){
		$this->id = -1;
		$this->updated_time = 0;
		$this->creator_id = -1;
		$this->host_team_id = -1;
		$this->guest_team_id = -1;
		$this->date_time = new DateTime();
		$this->match_group_id = -1;
		$this->host_score = null;
		$this->guest_score = null;
	}
	
	public function  __destruct(){
	
	}
	
	public function initFromDbEntry($db_entry){
		$this->id = $db_entry["ID"];
		$this->updated_time = $db_entry["UpdatedTime"];
		$this->creator_id = $db_entry["CreatorId"];
		$this->host_team_id = $db_entry["HostTeamId"];
		$this->guest_team_id = $db_entry["GuestTeamId"];
		$this->date_time = $db_entry["DateAndTime"];
		$this->match_group_id = $db_entry["MatchGroupId"];
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
	
	public function getHostTeamId(){
		return $this->host_team_id;
	}
	
	public function getGuestTeamId(){
		return $this->guest_team_id;
	}
	
	public function getDateAndTime(){
		return $this->date_time;
	}
	
	public function getMatchGroupId(){
		return $this->match_group_id;
	}
	
	public function getHostScore(){
		return $this->host_score;
	}
	
	public function getGuestScore(){
		return $this->guest_score;
	}
}

?>