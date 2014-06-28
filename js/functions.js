function isNumber(n)
{
	return /^-?[\d.]+(?:e-?\d+)?$/.test(n);
} 

function makeModerator(user_id){
	alert("Moderator");
}

function makeAdmin(user_id){
	alert("Admin");
}

function setPassword(user_id){
	new_password = prompt("Enter new password for user");
	$request = "setPasswordForUser.php";
	$data = {"new_pass": new_password, "user_id": user_id};
	$.post($request, $data, function(data,status){
			if(status=="success"){
				alert("Password updated!" + data);
			}else{
				alert("password NOT updated!");
			}
		});
}

function addFootballGame(group_id){
	location.href = "addFootballGame.php?group_id=" + group_id;
}

function viewFootballGames(group_id){
	location.href = "viewFootballGames.php?group_id=" + group_id;
}

function getTeamsByLeague(sel) {
    var value = sel.value;
    $.get("getTeamsByLeague.php?league_id=" + value,function(data,status){
    	if(status=="success"){
    		var obj = jQuery.parseJSON(data);
    		document.gameForm.host_team_id.options.length=0;
    		for (i = 0; i < obj.length; i++) { 
    		    var team = obj[i];
    		    if(team.length == 2){
    		    	document.gameForm.host_team_id[i]=new Option(team[1], team[0]);
    		    }
    		}
    		document.gameForm.guest_team_id.options.length=0;
    		for (i = 0; i < obj.length; i++) { 
    		    var team = obj[i];
    		    if(team.length == 2){
    		    	document.gameForm.guest_team_id[i]=new Option(team[1], team[0]);
    		    }
    		}
    	}
      });
}

function updateFootballGame(game_id){
	var table = document.getElementById("gamesTable");
	for (var i = 0, row; row = table.rows[i]; i++) {
		if(row.id == game_id){
			host_score = "";
			guest_score = "";
			for (var j = 0, col; col = row.cells[j]; j++){
				if(col.id == "hostScore"){
					host_score = col.firstChild.value;
				}else if(col.id == "guestScore"){
					guest_score = col.firstChild.value;
				}
			}
			if(host_score == "" || guest_score == ""){
				alert("You should fill the score!");
			}else if(!isNumber(host_score) || !isNumber(guest_score)){
				alert("Scores should be numbers");
			}else if(host_score < 0 || guest_score < 0){
				alert("Score can not be negative!");
			}
			else{
				$request = "updateFootballGame.php";
				$data = {"game_id": game_id, "host_score": host_score, "guest_score": guest_score};
				$.post($request, $data, function(data,status){
						if(status=="success"){
							alert("The score was updated!");
						}else{
							alert("The score was not updated!");
						}
					});
			}
		}
	}
}

function onUpdateFootballGameSuccess(){
	
}

function onUpdateFootballGameError(){
	
}

function updatePrediction(game_id){
	var tables = document.getElementsByClassName("predictionsTable");
	for(index = 0; index < tables.length; ++index){
		table = tables[index];
		for (var i = 0, row; row = table.rows[i]; i++) {
			if(row.id == game_id){
				host_score = "";
				guest_score = "";
				for (var j = 0, col; col = row.cells[j]; j++){
					if(col.id == "hostScore"){
						host_score = col.firstChild.value;
					}else if(col.id == "guestScore"){
						guest_score = col.firstChild.value;
					}
				}
				if(host_score == "" || guest_score == ""){
					alert("You should fill the score!");
				}else if(!isNumber(host_score) || !isNumber(guest_score)){
					alert("Scores should be numbers");
				}else if(host_score < 0 || guest_score < 0){
					alert("Score can not be negative!");
				}
				else{
					$request = "addPrediction.php";
					$data = {"game_id": game_id, "host_score": host_score, "guest_score": guest_score};
					$.post($request, $data, function(data,status){
							if(status=="success"){
								alert("The score was updated!");
							}else{
								alert("The score was not updated!");
							}
						});
				}
			}
		}
	}
}

function setLocalTime(){
	date_times = document.getElementsByClassName("datetime");
	for(index = 0; index < date_times.length; ++index){
		gmt_value = date_times[index].innerHTML;
		var local_value = new Date(parseInt(gmt_value)*1000);
		date_times[index].innerHTML = local_value.toLocaleString();
	}
}