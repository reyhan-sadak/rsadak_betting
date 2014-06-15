<?php

require_once 'sessionManager.php';

function redirect($location=null){
	if($location == null){
		$location = "index.php";
	}
	header("Location: ".$location);
	die();
}

function redirectIfNotAuthorized($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_authorized = ($current_user != null);
	if(!$is_authorized){
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
		redirect($location);
	}
}

function redirectIfNotAdmin($location=null){
	$current_user = SessionManager::getInstance()->getCurrentUser();
	$is_admin = false;
	if($current_user != null){
		$is_admin = $current_user->isAdmin();
	}
	if(!$is_admin){
		redirect($location);
	}
}

?>