<?php

require_once 'medoo.php';
require_once 'model/User.php';
require_once 'model/footballLeague.php';
require_once 'model/footballTeam.php';
require_once 'model/matchGroup.php';

class DatabaseManager{
	
	private  $database;
	private static $_singleton_;
	
	private function __construct(){
			$this->database = new medoo();
	}
	
	public function __destruct(){
		
	}
	
	public function getAllUsers(){
		$datas = $this->database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime",
				"CreatedTime"
		
				]);
		$users_array = array();
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			$users_array[] = $user;
		}
		return $users_array;
	}
	
	public function getUserById($user_id){
		$datas = $this->database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime",
				"CreatedTime"
				
				], [
				"ID" => (int)$user_id
				]);
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			if($user->getId() == (int)$user_id){
				return $user;
			}
		}
		//echo "getUserById null ".$user_id;
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
				"UpdatedTime",
				"CreatedTime"
		
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
		//echo "getUserByHashId null";
		return null;
	}
	
	public function getUserByEmail($email){
		$datas = $this->database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime",
				"CreatedTime"
		
				], [
				"Email" => $email
				]);
		foreach ($datas as $data){
			$user = new User();
			$user->initFromDbEntry($data);
			if($user->getEmail() == $email){
				return $user;
			}
		}
		//echo "getUserByEmail null";
		return null;
	}
	
	public function getLeagueById($league_id){
		$datas = $this->database->select("FootballLeagues", [
					"ID",
					"Name",
					"CreatorId",
					"UpdatedTime",
					"CreatedTime"
				], [
					"ID" => (int)$league_id
				]);
		foreach ($datas as $data){
			$league = new FootballLeague();
			$league->initFromDbEntry($data);
			if($league->getId() == (int)$league_id){
				return $league;
			}
		}
		return null;
	}
	
	public function getLeagueByName($league_name){
		$datas = $this->database->select("FootballLeagues", [
				"ID",
				"Name",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime"
				], [
				"Name" => $league_name
				]);
		foreach ($datas as $data){
			$league = new FootballLeague();
			$league->initFromDbEntry($data);
			if($league->getName() == $league_name){
				return $league;
			}
		}
		return null;
	}
	
	public function getAllLeagues(){
		$datas = $this->database->select("FootballLeagues", [
				"ID",
				"Name",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime"
				]);
		$leagues_array = array();
		foreach ($datas as $data){
			$league = new FootballLeague();
			$league->initFromDbEntry($data);
			$leagues_array[] = $league;
		}
		return $leagues_array;
	}
	
	public function getTeamById($team_id){
		$datas = $this->database->select("",
				[
					"ID",
					"Name",
					"LeagueId",
					"CreatorId",
					"UpdatedTime",
					"CreatedTime",
				],
				[
					"ID" => (int)$team_id
				]);
		foreach ($datas as $data){
			$team = new FootballTeam();
			$team->initFromDbEntry($data);
			if($team->getId() == (int)$team_id){
				return $team;
			}
		}
		return null;
	}
	
	public function  getTeamsByName($team_name){
		$datas = $this->database->select("",
				[
				"ID",
				"Name",
				"LeagueId",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime",
				],
				[
				"ID" => $team_id
				]);
		$teams = array();
		foreach ($datas as $data){
			$team = new FootballTeam();
			$team->initFromDbEntry($data);
			if($team->getName() == $team_name){
				$teams[] = $team;
			}
		}
		return $teams;
	}
	
	public function getGameGroupById($game_group_id){
		$datas = $this->database->select("MatchGroups",
				[
					"ID",
					"Name",
					"FromDate",
					"ToDate",
					"CreatorId",
					"UpdatedTime",
					"CreatedTime"
				],
				[
					"ID" => $game_group_id
				]
				);
		foreach ($datas as $data){
			$match_group = new MatchGroup();
			$match_group->initFromDbEntry($data);
			if($match_group->getId() == (int)$game_group_id){
				return $match_group;
			}
		}
		return null;
	}
	
	public function setUserHashForUser($user_hash, $user_id){
		$this->database->update("Users", [
				"UserHash" => $user_hash
				],
				["ID" => $user_id]);
		return true;
	}
	
	public function addNewUser($email, $password_hash, $name, $userHash, $rank){
		$datetime = date('Y-m-d H:i:s');
		$last_user_id = $this->database->insert("Users",
				[
					"Name" => $name, 
					"Email" => $email,
					"PasswordHash" => $password_hash,
					"UserHash" => $userHash,
					"UserRank" => $rank,
					"UpdatedTime" => $datetime
				]);
		
		return $last_user_id;
	}
	
	public function addNewLeague($league_name, $creator_id){
		$datetime = date('Y-m-d H:i:s');
		$new_league_id = $this->database->insert("FootballLeagues",
				[
					"Name" => $league_name,
					"CreatorId" => $creator_id,
					"UpdatedTime" => $datetime
				]);
		return $new_league_id;
	}
	
	public function addNewTeamForLeague($team_name, $league_id, $creator_id){
		$datetime = date('Y-m-d H:i:s');
		$new_team_id = $this->database->insert("FootballTeams",
				[
					"Name" => $team_name,
					"LeagueId" => $league_id,
					"CreatorId" => $creator_id,
					"UpdatedTime" => $datetime
				]);
	}
	
	public function addNewGameGroup($name, $from_data, $to_date, $creator_id){
		$datetime = date('Y-m-d H:i:s');
		$new_group_id = $this->database->insert("MatchGroups",
				[
					"Name" => $name,
					"FromData" => $from_data,
					"ToDate" => $to_date,
					"CreatorId" => $creator_id,
					"UpdatedTime" => $datetime
				]);
	}
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new DatabaseManager();
		}
		
		return self::$_singleton_;
	}
}

?>