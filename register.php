<?php

require_once 'managers/sessionManager.php';
require_once 'utils/functions.php';
require_once 'basePage.php';

function registerForm(){
	echo '<form action="register.php" method="post">
	Name: <input type="text" name="name"><br>
	E-mail: <input type="email" name="email_register"><br>
	Password: <input type="password" name="password_register"><br>
	Repeat Password: <input type="password" name="repeat_password"><br>
	<input type="submit" value="Register">
	</form>';
}

redirectIfAuthorized();

pageHeader();

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$name_err = $email_err = $pass_err = $repeat_pass_err = "";
	$name = $email = $pass = $repeat_pass = "";
	if(isset($_POST["name"])){
		$name = $_POST["name"];
		if(!SessionManager::isNameValid($name)){
			$name = "";
			$name_err = "Name is not valid";
		}
	}else{
		$name_err = "Name is required";
	}
	
	if(isset($_POST["email_register"])){
		$email = $_POST["email_register"];
		//if(!SessionManager::isEmailValid($email)){
		if(!SessionManager::isGameloftEmail($email)){
			$email = "";
			$email_err = "Email is not valid";
		}
	}else{
		$email_err = "Email is required";
	}
	
	if(isset($_POST["password_register"])){
		$pass = $_POST["password_register"];
	}else{
		$pass_err = "Password is required";
	}
	
	if(isset($_POST["repeat_password"])){
		$repeat_pass = $_POST["repeat_password"];
	}else{
		$repeat_pass_err = "Password is required";
	}
	
	if($pass != $repeat_pass){
		$repeat_pass_err = "Passwords don't match";
	}
	
	if($name_err === "" && $email_err === "" && $pass_err === "" && $repeat_pass_err === ""){
		$error = SessionManager::getInstance()->register($email, $pass, $repeat_pass, $name);
		if($error == HttpStatus::$HTTP_STATUS_OK){
			header("Location: index.php");
			exit();
		}else if($error == HttpStatus::$HTTP_STATUS_BAD_REQUEST){
			registerForm();
		}
		else if($error == HttpStatus::$HTTP_STATUS_INTERNAL_SERVER_ERROR){
			registerForm();
		}
		echo $error;
	}
	else{
		echo $name_err."<br>";
		echo $email_err."<br>";
		echo $pass_err."<br>";
		echo $repeat_pass_err."<br>";
	}
	
}
else{
	registerForm();
}

pageFooter();

?>