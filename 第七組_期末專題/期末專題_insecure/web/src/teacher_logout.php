<?php
session_start();
session_destroy();
header("Location: teacher_login.php"); // 登出後回到教師登入頁
exit;