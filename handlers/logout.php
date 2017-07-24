<?php

session_start();

if (isset($_SESSION['employee_id'])) unset($_SESSION['employee_id']);

echo "Logout Successful";

header("location: ../index.php");

?>