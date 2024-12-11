<?php
session_start();
if (!isset($_SESSION['user_id']) && ($_SESSION['role'] != 1)) {
    header("Location: ../../index.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="">
</head>
<body>


<script></script>
</body>
</html>