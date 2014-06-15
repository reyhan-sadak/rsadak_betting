<?php

require_once 'sessionManager.php';

SessionManager::getInstance()->logout();
header("Location: index.php");

?>