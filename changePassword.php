<?php

require_once 'managers/sessionManager.php';
require_once 'utils/functions.php';
require_once 'basePage.php';

function changePasswordForm($old_pass_error = "", $new_pass_error = "", $new_pass_repeat_error = ""){
	echo '<form action="changePassword.php" method="post">
	Old Password: <input type="password" name="old_pass" value="">'.$old_pass_error.' <br>
	New Password: <input type="password" name="new_pass">'.$new_pass_error.'<br>
	Repeat Password: <input type="password" name="new_pass_repeat">'.$new_pass_repeat_error.'<br>
	<input type="submit" value="Change">
	</form>';
}

redirectIfNotAuthorized();

pageHeader();
controlPanel();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$old_pass = "";
	$new_pass = "";
	$new_pass_repeat = "";
	
	$old_pass_error = "";
	$new_pass_error = "";
	$new_pass_repeat_error = "";
	
	if(isset($_POST["old_pass"])){
		$old_pass = $_POST["old_pass"];
	}else{
		$old_pass_error = "Old password required!";
	}
	
	if(isset($_POST["new_pass"])){
		$new_pass = $_POST["new_pass"];
	}else{
		$new_pass_error = "Password is required";
	}
	
	if(isset($_POST["new_pass_repeat"])){
		$new_pass_repeat = $_POST["new_pass_repeat"];
	}else{
		$new_pass_repeat_error = "Password is required";
	}
	
	if(!empty($old_pass_error) || !empty($new_pass_error) || !empty($new_pass_repeat_error)){
		changePasswordForm($old_pass_error, $new_pass_error, $new_pass_repeat_error);
	}else{
		if($new_pass != $new_pass_repeat){
			changePasswordForm("", "", "New passwords does not match");
		}else{
			$error = SessionManager::getInstance()->changePassword($old_pass, $new_pass);
			if($error == HttpStatus::$HTTP_STATUS_UNAUTHORIZED){
				redirect();
			} else if($error == HttpStatus::$HTTP_STATUS_CONFLICT){
				changePasswordForm("Conflict", "", "");
			}else if($error == HttpStatus::$HTTP_STATUS_FORBIDDEN){
				changePasswordForm("Old password is incorrect");
			}else if($error == HttpStatus::$HTTP_STATUS_INTERNAL_SERVER_ERROR){
				changePasswordForm("An error occured, please try again later!");
			} else if($error == HttpStatus::$HTTP_STATUS_OK){
				redirect(null, "Password changed successfully!", false);
			}
		}
	}
}
else{
	changePasswordForm();
}

pageFooter();

?>