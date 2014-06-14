<?php

require_once 'initSession.php';
require_once 'sessionManager.php';

$current_user = SessionManager::getInstance()->getCurrentUser();
if($current_user){
	// redirect to the main page
	header("Location: index.php");
	exit();
}

echo '<html>
<body>';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$name_err = $email_err = $pass_err = $repeat_pass_err = "";
	$name = $email = $pass = $repeat_pass = "";
	if(isset($_POST["name"])){
		$name = $_POST["name"];
		if(!User::isNameValid($name)){
			$name = "";
			$name_err = "Name is not valid";
		}
	}else{
		$name_err = "Name is required";
	}
	
	if(isset($_POST["email"])){
		$email = $_POST["email"];
		if(!User::isEmailValid($email)){
			$email = "";
			$email_err = "Email is not valid";
		}
	}else{
		$email_err = "Email is required";
	}
	
	if(isset($_POST["password"])){
		$pass = $_POST["password"];
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
		echo SessionManager::getInstance()->register($email, $pass, $repeat_pass, $name);
	}
	else{
		echo $name_err."<br>";
		echo $email_err."<br>";
		echo $pass_err."<br>";
		echo $repeat_pass_err."<br>";
	}
	
}
else{
echo '<form action="register.php" method="post">
Name: <input type="text" name="name"><br>
E-mail: <input type="text" name="email"><br>
Password: <input type="password" name="password"><br>
Repeat Password: <input type="password" name="repeat_password"><br>
<input type="submit">
</form>';
}

echo '</body>
</html>';

?>