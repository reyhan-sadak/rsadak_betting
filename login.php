<?php

require_once 'initSession.php';
require_once 'sessionManager.php';

$current_user = SessionManager::getInstance()->getCurrentUser();
if($current_user){
	// redirect to the main page
	header("Location: index.php");
	exit();
}

echo
'<html>
<head>
</head>

<body>';

if ($_SERVER["REQUEST_METHOD"] == "POST"){
	$name = $pass = "";
	
	if(isset($_POST["email"])){
		$email = $_POST["email"];
		if(!SessionManager::isEmailValid($email)){
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
	
	$error = SessionManager::getInstance()->login($email, $pass);
}
else{
echo '<form action="login.php" method="post">
E-mail: <input type="text" name="email"><br>
Password: <input type="password" name="password"><br>
<input type="submit">
</form>';
}

echo
'</body>
</html>';

?>