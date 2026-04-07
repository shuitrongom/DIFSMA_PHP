<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo "PHP version: " . PHP_VERSION . "<br>";
require_once 'config.php';
echo "Config OK<br>";
require_once 'includes/db.php';
echo "DB OK<br>";
