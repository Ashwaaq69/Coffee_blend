<?php
ob_start(); // Start output buffering
require_once "../includes/header.php";
require "../config/config.php";

if(!isset($_SESSION['user_id'])) {
    header("location: ".APPURL."");
    exit();
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $delete = $conn->prepare("DELETE FROM cart WHERE id = :id");
    $delete->execute([':id' => $id]);

    header("location: cart.php");
    exit();
}

ob_end_flush(); // Send output to the browser
