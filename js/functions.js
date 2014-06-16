function makeModerator(user_id){
	alert("Moderator");
}

function makeAdmin(user_id){
	alert("Admin");
}

function addFootballGame(group_id){
	location.href = "addFootballGame.php?group_id=" + group_id;
}

function viewFootballGames(group_id){
	alert("View Football Games for group id = " + group_id);
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