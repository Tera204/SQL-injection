<?php
session_start();

// 如果已經登入過，直接跳轉後台
if (isset($_SESSION['teacher_result'])) {
    header("Location: teacher_result.php");
    exit;
}

// 連線資料庫
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$dbname = getenv('DB_NAME') ?: 'class_db';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

$error = "";

if ($_POST) {
    // 接收表單輸入
    $u_input = $_POST['username'];
    $p_input = $_POST['password'];

    // ✅ 安全寫法：使用 Prepare Statement 防止 SQL Injection
    // 這裡只允許 role 為 'teacher' 的帳號登入
    // 資料表：userTable, 欄位：username, password, role
    $stmt = $conn->prepare("SELECT * FROM userTable WHERE username = ? AND password = ? AND role = 'teacher'");
    $stmt->bind_param("ss", $u_input, $p_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 登入成功，記錄 Session
        $_SESSION['teacher_result'] = $row['username'];
        header("Location: teacher_result.php"); // 跳轉到教師後台
        exit;
    } else {
        $error = "登入失敗：權限不足或密碼錯誤";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>教師管理登入</title>
    <style>
        body { 
            font-family: "Microsoft JhengHei", sans-serif; 
            padding: 20px;
            background-color: #e9ecef;
        }
        .box { 
            border: 1px solid #ccc; 
            padding: 30px; 
            width: 400px; 
            margin: 80px auto; 
            background-color: #fff;
            border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; }
        p { margin-bottom: 15px; font-weight: bold; color: #555;  }
        input[type="text"], input[type="password"] {
            width: 95%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;
        }
        button {
            width: 100%; padding: 10px; cursor: pointer;
            background: #3fb485ff; color: white; border: none; border-radius: 4px;
            font-size: 16px;
        }
        button:hover { background: #35926dff; }
        .back-btn {
            background: none; border: none; color: #666;
            margin-top: 20px; text-decoration: underline; cursor: pointer;
            width: auto; display: block; margin-left: auto; margin-right: auto;
        }
        .back-btn:hover { background: none; color: #333; }
    </style>
</head>
<body>

    <div class="box">
        <h2>成績管理系統(教師)</h2>
        
        <form method="POST">
            <p>帳號：
                <input type="text" name="username" required>
            </p>
            <p>密碼：
                <input type="password" name="password" required>
            </p>
            <button type="submit">登入</button>
        </form>

        <?php if($error): ?>
            <p style="color:red; text-align:center; margin-top:15px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <hr style="margin-top:25px; border-top:1px solid #eee;">
        <button class="back-btn" onclick="location.href='student_login.php'">← 切換至學生登入介面</button>
    </div>

</body>
</html>