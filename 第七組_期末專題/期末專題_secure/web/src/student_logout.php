<?php
session_start();

// 清空所有 Session 變數
$_SESSION = [];

// 銷毀 Session
session_destroy();

// 導回登入頁
header("Location: student_login.php");
exit;
