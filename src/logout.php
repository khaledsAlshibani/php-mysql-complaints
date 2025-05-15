<?php
require_once 'config.php';

$user = new User();
$user->logout();

header("Location: " . BASE_URL . "templates/pages/login.php");
exit();
