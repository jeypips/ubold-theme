<?php

session_start();
if (!isset($_SESSION['employee_id'])) header("location: login.html");

?>