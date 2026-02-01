<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['email'])) {
    header("Location: ../frontend/login.php");
    exit();
}
