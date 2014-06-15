<?php

require_once 'managers/sessionManager.php';

SessionManager::getInstance()->logout();
header("Location: index.php");

?>