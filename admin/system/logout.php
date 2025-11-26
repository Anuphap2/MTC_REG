<?php
session_start();

unset($_SESSION['admin_log']);
unset($_SESSION['user']);
$_SESSION['success'] = "ออกจากระบบแล้ว";

header("location: ../index.php");
