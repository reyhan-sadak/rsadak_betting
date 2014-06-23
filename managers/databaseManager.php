<?php

require_once 'framework/medoo.min.php';
require_once 'model/user.php';
require_once 'model/footballLeague.php';
require_once 'model/footballTeam.php';
require_once 'model/matchGroup.php';
require_once 'model/footballMatch.php';
require_once 'model/footballMatchPrediction.php';

class DatabaseManager{
	
	public static $MATCH_GROUP_STATUS_ENDED = 1;
	public static $MATCH_GROUP_STATUS_STARTED = 2;
	public static $MATCH_GROUP_STATUS_UPCOMING = 3;
	
	private  $database;
	private static $_singleton_;
	
	private function __construct(){
		try {
			$this->database = new medoo();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function __destruct(){
		
	}
	
	public function getAllUsers($is_active=null, $is_game_predictor=null){
		$filter = array();
		if($is_active && $is_game_predictor){
			$filter_and = array();
			$filter_and["Active"] = 1;
			$filter_and["Predictor"] = 1;
			$filter["AND"] = $filter_and;
		}else if($is_active){
			$filter["Active"] = 1;
		}else if($is_game_predictor){
			$filter["Prefictor"] = 1;
		}
		$datas = $this->database->select("Users", [
				"ID",
				"Name",
				"Email",
				"PasswordHash",
				"UserHash",
				"UserRank",
				"UpdatedTime",
				"CreatedTime",
				"Active",
				"Predictor"
				],
				$filter);
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
				"CreatedTime",
				"Active",
				"Predictor"
				
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
				"CreatedTime",
				"Active",
				"Predictor"
				
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
				"CreatedTime",
				"Active",
				"Predictor"
		
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
		$datas = $this->database->select("FootballTeams",
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
		$datas = $this->database->select("FootballTeams",
				[
				"ID",
				"Name",
				"LeagueId",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime",
				],
				[
				"Name" => $team_name
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
	
	public function getAllTeams($league_id=null){
		$filter = array();
		if($league_id != null){
			$filter["LeagueId"] = (int)$league_id;
		}
		$datas = $this->database->select("FootballTeams",
				[
				"ID",
				"Name",
				"LeagueId",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime",
				],
				$filter
				);
		$teams = array();
		foreach ($datas as $data){
			$team = new FootballTeam();
			$team->initFromDbEntry($data);
			$teams[] = $team;
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
					"CreatedTime",
					"Visible"
				],
				[
					"ID" => $game_group_id
				]
				);
		foreach ($datas as $data){
			$match_group = new MatchGroup();
			$match_group->initFromDbEntry($data);
			if($match_group->getId() == (int)$game_group_id && $match_group->isVisible()){
				return $match_group;
			}
		}
		return null;
	}
	
	public function getGameGroupByName($game_group_name){
		$datas = $this->database->select("MatchGroups",
				[
				"ID",
				"Name",
				"FromDate",
				"ToDate",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime",
				"Visible"
				],
				[
				"Name" => $game_group_name
				]
		);
		foreach ($datas as $data){
			$match_group = new MatchGroup();
			$match_group->initFromDbEntry($data);
			if($match_group->getName() == $game_group_name && $match_group->isVisible()){
				return $match_group;
			}
		}
		return null;
	}
	
	public function getAllGroups($status=null, $hidden=false){
		$filter = array();
		if($status){
			$date = date('Y-m-d');
			switch($status){
				case self::$MATCH_GROUP_STATUS_ENDED:
					$filter["ToDate[<]"] = $date;
					break;
					
				case self::$MATCH_GROUP_STATUS_STARTED:
					$filter_and = array();
					$filter_and["ToDate[>=]"] = $date;
					$filter_and["FromDate[<=]"] = $date;
					$filter["AND"] = $filter_and;
					break;
						
				case self::$MATCH_GROUP_STATUS_UPCOMING:
					$filter["FromDate[>]"] = $date;
					break;
				
			}
		}
		$datas = $this->database->select("MatchGroups",
				[
				"ID",
				"Name",
				"FromDate",
				"ToDate",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime",
				"Visible"
				],
				$filter
		);
		$match_groups = array();
		foreach ($datas as $data){
			$match_group = new MatchGroup();
			$match_group->initFromDbEntry($data);
			if($match_group->isVisible() || $hidden){
				$match_groups[] = $match_group;
			}
		}
		return $match_groups;
	}
	
	public function getMatchesByGroupId($group_id=null){
		$filter = array();
		if($group_id){
			$filter["MatchGroupId"] = (int)$group_id;
		}
		$datas = $this->database->select("FootballMatches",
				[
					"ID",
					"HostTeamId",
					"GuestTeamId",
					"MatchGroupId",
					"CreatorId",
					"DateAndTime",
					"HostScore",
					"GuestScore",
					"UpdatedTime",
					"CreatedTime"
				],
				$filter
		);
		$football_matches_array = array();
		foreach($datas as $data){
			$football_match = new FootballMatch();
			$football_match->initFromDbEntry($data);
			$football_matches_array[] = $football_match;
		}
		return $football_matches_array;
	}
	
	public function getGameById($game_id){
		$datas = $this->database->select("FootballMatches",
				[
					"ID",
					"HostTeamId",
					"GuestTeamId",
					"MatchGroupId",
					"CreatorId",
					"DateAndTime",
					"HostScore",
					"GuestScore",
					"UpdatedTime",
					"CreatedTime"
				],
				["ID" => $game_id]
		);
		foreach ($datas as $data)
		{
			$football_match = new FootballMatch();
			$football_match->initFromDbEntry($data);
			if($football_match->getId() == $game_id){
				return $football_match;
			}
		}
		return null;
	}
	
	public function getPredictionForGameOfUser($game_id, $user_id){
		$datas = $this->database->select("FootballMatchPredictions",
			[
				"ID",
				"MatchId",
				"HostScore",
				"GuestScore",
				"CreatorId",
				"UpdatedTime",
				"CreatedTime"
			],
			[
				"AND" =>
					[
						"MatchId" => (int)$game_id,
						"CreatorId" => (int)$user_id
					]
			]);
		foreach ($datas as $data){
			$prediction = new FootballMatchPrediction();
			$prediction->initFromDbEntry($data);
			if($prediction->getMatchId() == $game_id && $prediction->getCreatorId() == $user_id){
				return $prediction;
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
	
	public function addNewUser($email, $password_hash, $name, $userHash, $rank, $is_active, $is_game_predictor){
		$datetime = date('Y-m-d H:i:s');
		$last_user_id = $this->database->insert("Users",
				[
					"Name" => $name, 
					"Email" => $email,
					"PasswordHash" => $password_hash,
					"UserHash" => $userHash,
					"UserRank" => $rank,
					"UpdatedTime" => $datetime,
					"Active" => $is_active,
					"Predictor" => $is_game_predictor
				]);
		
		return $last_user_id;
	}
	
	public function updatePasswordForUser($user_id, $new_password_hash){
		$datetime = date('Y-m-d H:i:s');
		$result = $this->database->update("Users",
				[
					"PasswordHash" => $new_password_hash,
					"UpdatedTime" => $datetime
				],
				[
					"ID" => (int)$user_id
				]
			);
		return $result;
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
	
	public function addNewGameGroup($name, $from_date, $to_date, $creator_id){
		$datetime = date('Y-m-d H:i:s');
		$from_date_db = $from_date->format('Y-m-d H:i:s');
		$to_date_db = $to_date->format('Y-m-d H:i:s');
		$new_group_id = $this->database->insert("MatchGroups",
				[
					"Name" => $name,
					"FromDate" => $from_date_db,
					"ToDate" => $to_date_db,
					"CreatorId" => $creator_id,
					"UpdatedTime" => $datetime,
					"Visible" => 1
				]);
		return $new_group_id;
	}
	
	public function addNewGame($group_id, $host_team_id, $guest_team_id, $creator_id, $date_time){
		$datetime = date('Y-m-d H:i:s');
		$date_time_db = $date_time->format('Y-m-d H:i:s');
		$new_game_id = $this->database->insert("FootballMatches",
				[
					"HostTeamId" => (int)$host_team_id,
					"GuestTeamId" => (int)$guest_team_id,
					"MatchGroupId" => $group_id,
					"CreatorId" => $creator_id,
					"DateAndTime" => $date_time_db,
					"UpdatedTime" => $datetime
				]
				);
		return $new_game_id;
	}
	
	public function updateGame($game_id, $host_score, $guest_score){
		$datetime = date('Y-m-d H:i:s');
		$result = $this->database->update("FootballMatches",
				[
					"HostScore" => (int)$host_score,
					"GuestScore" => (int)$guest_score,
					"UpdatedTime" => $datetime
				],
				[
					"ID" => $game_id
				]
			);
		
		return $result;
	}
	
	public function createGamePrediction($game_id, $creator_id, $host_score, $guests_score){
		$datetime = date('Y-m-d H:i:s');
		$result = $this->database->insert("FootballMatchPredictions",
				[
				"MatchId" => (int)$game_id,
				"CreatorId" => (int)$creator_id,
				"HostScore" => (int)$host_score,
				"GuestScore" => (int)$guests_score,
				"UpdatedTime" => $datetime
				]
		);
		
		return $result;
	}
	
	public function updateGamePrediction($game_id, $creator_id, $host_score, $guests_score){
		$datetime = date('Y-m-d H:i:s');
		$result = $this->database->update("FootballMatchPredictions",
				[
				"HostScore" => (int)$host_score,
				"GuestScore" => (int)$guests_score,
				"UpdatedTime" => $datetime
				],
				[
					"AND" =>
					[
						"MatchId" => $game_id,
						"CreatorId" => $creator_id
					]
				]
		);
		
		return $result;
	}
	
	public static function getInstance(){
		if(!isset(self::$_singleton_)){
			self::$_singleton_ = new DatabaseManager();
		}
		
		return self::$_singleton_;
	}
}

?>