<?php

require_once 'managers/sessionManager.php';

function redirect($location=null, $head_message=null, $is_error=true){
	if($location == null){
		$location = "index.php";
	}
	if($head_message != null){
		if($is_error){
			$location = $location."?errorMsg=".$head_message;
		}else{
			$location = $location."?msg=".$head_message;
		}
	}
	header("Location: ".$location);
	die();
}

function redirectIfNotAuthorized($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_authorized = ($current_user != null);
	if(!$is_authorized){
		redirect($location, "You should login to perform this action!");
	}
}

function redirectIfAuthorized($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_authorized = ($current_user != null);
	if($is_authorized){
		redirect($location);
	}
}

function redirectIfNotModerator($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_moderator = false;
	if($current_user != null){
		$is_moderator = $current_user->isModerator();
	}
	if(!$is_moderator){
		redirect($location, "You should be a moderator to perform this action!");
	}
}

function redirectIfNotAdmin($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_admin = false;
	if($current_user != null){
		$is_admin = $current_user->isAdmin();
	}
	if(!$is_admin){
		redirect($location, "You need to be an administrator to perform this action!");
	}
}

function getFileNameFromFilePath($filePath){
	$pos = strrpos($filePath, '/');
	
	if ($pos === false) {
		return $filePath;
	} else {
		return substr($filePath, $pos+1);
	}
}

?>