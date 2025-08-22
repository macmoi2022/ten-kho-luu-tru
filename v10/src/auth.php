<?php
// src/auth/auth.php
ini_set('session.gc_maxlifetime', 28800);
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$user_type = $_SESSION['type'];
$username = $_SESSION['username'];
?>