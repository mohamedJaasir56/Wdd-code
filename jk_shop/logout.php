<?php
require 'config.php';

$_SESSION = array();
session_destroy();
header('Location: login.php');
exit;
?>