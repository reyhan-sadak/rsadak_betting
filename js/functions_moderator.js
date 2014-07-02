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