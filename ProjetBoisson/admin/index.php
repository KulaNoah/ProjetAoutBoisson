<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?page=login");
    exit();
}

$page = $_GET['page'] ?? 'dashboard';
include __DIR__ . "/content/$page.php";
