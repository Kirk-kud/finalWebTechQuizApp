<?php
session_start();

if (isset($_SESSION['user_id'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: ../view/login.php");
    exit();
} else {
    header("Location: ../view/login.php");
    exit();
}
?>